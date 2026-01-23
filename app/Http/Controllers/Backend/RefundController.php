<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductReturn;
use App\Models\ReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class RefundController extends Controller
{
    /**
     * Display list of all refunds
     */
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('refund_view'), 403);

        if ($request->ajax()) {
            $returns = ProductReturn::with(['order', 'processedBy'])->latest()->get();
            return DataTables::of($returns)
                ->addIndexColumn()
                ->addColumn('return_number', fn($data) => '<strong>' . $data->return_number . '</strong>')
                ->addColumn('order_id', fn($data) => '#' . $data->order_id)
                ->addColumn('total_refund', fn($data) => number_format($data->total_refund, 2))
                ->addColumn('processed_by', fn($data) => optional($data->processedBy)->name ?? 'N/A')
                ->addColumn('created_at', fn($data) => $data->created_at->format('d M, Y h:i A'))
                ->addColumn('action', function ($data) {
                    $url = route('backend.admin.refunds.receipt', $data->id);
                    return '<button type="button" onclick="openRefundReceipt(\'' . $url . '\')" class="btn btn-sm btn-info">
                        <i class="fas fa-receipt"></i> Receipt
                    </button>';
                })
                ->rawColumns(['return_number', 'action'])
                ->toJson();
        }

        return view('backend.refunds.index');
    }

    /**
     * Show refund form for an order (AJAX modal)
     */
    public function create($orderId)
    {
        abort_if(!auth()->user()->can('refund_create'), 403);

        $order = Order::with(['products.product', 'customer'])->findOrFail($orderId);
        
        // Calculate already returned quantities for each order product
        foreach($order->products as $item) {
            $item->returned_qty = ReturnItem::where('order_product_id', $item->id)->sum('quantity');
            $item->available_qty = $item->quantity - $item->returned_qty;
        }

        // Check if order already has a full refund
        if ($order->is_returned || $order->products->sum('available_qty') <= 0) {
            return response()->json(['error' => 'This order has already been fully refunded.'], 400);
        }

        return view('backend.refunds.create', compact('order'));
    }

    /**
     * Process refund
     */
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('refund_create'), 403);

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'items' => 'required|array|min:1',
            'items.*.order_product_id' => 'required|exists:order_products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($request->order_id);
            $totalRefund = 0;

            // Create return record
            $return = ProductReturn::create([
                'order_id' => $order->id,
                'return_number' => ProductReturn::generateReturnNumber(),
                'reason' => $request->reason,
                'processed_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $orderProduct = OrderProduct::findOrFail($item['order_product_id']);
                
                // Calculate already returned quantity to verify availability
                $alreadyReturned = ReturnItem::where('order_product_id', $orderProduct->id)->sum('quantity');
                $availableQty = $orderProduct->quantity - $alreadyReturned;
                
                $quantity = min($item['quantity'], $availableQty); 

                if ($quantity <= 0) continue;

                // Calculate refund amount for this item (exact proportional refund based on original bill)
                $pricePerUnit = $orderProduct->total / $orderProduct->quantity;
                $refundAmount = $pricePerUnit * $quantity;
                $totalRefund += $refundAmount;

                // Create return item
                ReturnItem::create([
                    'return_id' => $return->id,
                    'order_product_id' => $orderProduct->id,
                    'product_id' => $orderProduct->product_id,
                    'quantity' => $quantity,
                    'refund_amount' => $refundAmount,
                ]);

                // Update product stock (add back to inventory)
                $product = Product::find($orderProduct->product_id);
                if ($product) {
                    $product->quantity += $quantity;
                    $product->total_returned = ($product->total_returned ?? 0) + $quantity;
                    $product->save();
                }
            }

            // Update return total
            $return->update(['total_refund' => $totalRefund]);

            // Create a negative transaction recorded against the order
            OrderTransaction::create([
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'user_id' => auth()->id(),
                'amount' => -$totalRefund,
                'paid_by' => 'cash', // Refunds are usually cash-out
                'transaction_id' => 'REFUND-' . $return->return_number
            ]);

            // Mark order as returned if cumulative returns cover the full order total
            $cumulativeRefund = ProductReturn::where('order_id', $order->id)->sum('total_refund');
            if ($cumulativeRefund >= $order->total) {
                $order->update(['is_returned' => true]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully!',
                'return_id' => $return->id,
                'total_refund' => $totalRefund,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to process refund: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show return receipt
     */
    public function receipt($id)
    {
        $return = ProductReturn::with([
            'order.customer',
            'order.products.product',
            'order.returns', // Load all returns for this order
            'items.product',
            'items.orderProduct',
            'processedBy'
        ])->findOrFail($id);

        // Calculate total refunded for this order across all return instances
        $orderTotalRefunded = $return->order->returns->sum('total_refund');
        $return->order_total_refunded = $orderTotalRefunded;

        $maxWidth = readConfig('receiptMaxwidth') ?? '300px';
        return view('backend.refunds.receipt', compact('return', 'maxWidth'));
    }
}
