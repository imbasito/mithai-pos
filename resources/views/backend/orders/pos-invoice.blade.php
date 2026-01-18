@extends('backend.master')
@section('title', 'Receipt_'.$order->id)
@section('content')

<div class="card">
  <!-- Main content -->
  <div class="receipt-container mt-0" id="printable-section" style="max-width: {{ $maxWidth}}; font-size: 12px; font-family: 'Courier New', Courier, monospace;">
    <!-- Header with Logo -->
    <div class="text-center">
      @if(readConfig('is_show_logo_invoice'))
      <img src="{{ assetImage(readconfig('site_logo')) }}" height="80" width="180" alt="Logo">
      @endif
      @if(readConfig('is_show_site_invoice'))
      <h3>{{ readConfig('site_name') }}</h3>
      @endif
      @if(readConfig('is_show_address_invoice')){{ readConfig('contact_address') }}<br>@endif
      @if(readConfig('is_show_phone_invoice')){{ readConfig('contact_phone') }}<br>@endif
      @if(readConfig('is_show_email_invoice')){{ readConfig('contact_email') }}<br>@endif
    </div>
    User: {{ auth()->user()->name }}<br>
    Order: #{{ $order->id }}<br>
    <hr>
    <div class="row justify-content-between mx-auto">
      <div class="text-left">
        @if(readConfig('is_show_customer_invoice'))
        <address>
          Name: {{ $order->customer->name ?? 'N/A' }}<br>
          Address: {{ $order->customer->address ?? 'N/A' }}<br>
          Phone: {{ $order->customer->phone ?? 'N/A' }}
        </address>
        @endif
      </div>
      <div class="text-right">
        <address class="text-right">
          <p>{{ date('d-M-Y') }}</p>
          <p>{{ date('h:i:s A') }}</p>
        </address>
      </div>
    </div>
    <hr>
    
    <!-- Products Table - QTY, Price, Amount format -->
    <table style="width: 100%;">
      <thead>
        <tr>
          <th style="text-align: left;">Item</th>
          <th style="text-align: center;">QTY</th>
          <th style="text-align: right;">Price</th>
          <th style="text-align: right;">Amount</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($order->products as $item)
        <tr>
          <td>{{ $item->product->name }}</td>
          <td style="text-align: center;">{{ $item->quantity }}</td>
          <td style="text-align: right;">{{ number_format($item->discounted_price, 0) }}</td>
          <td style="text-align: right;">{{ number_format($item->total, 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <hr>
    
    <!-- Summary -->
    <div class="summary">
      <table style="width: 100%;">
        <tr>
          <td>Subtotal:</td>
          <td class="text-right">{{ number_format($order->sub_total, 2) }}</td>
        </tr>
        <tr>
          <td>Discount:</td>
          <td class="text-right">{{ number_format($order->discount, 2) }}</td>
        </tr>
        <tr>
          <td><strong>Total:</strong></td>
          <td class="text-right"><strong>{{ number_format($order->total, 2) }}</strong></td>
        </tr>
        <tr>
          <td>Paid:</td>
          <td class="text-right">{{ number_format($order->paid, 2) }}</td>
        </tr>
        @if($order->paid > $order->total)
        <!-- Change - Only show on screen -->
        <tr class="no-print" style="color: #28a745;">
          <td>Change:</td>
          <td class="text-right">{{ number_format($order->paid - $order->total, 2) }}</td>
        </tr>
        @endif
        <tr>
          <td>Due:</td>
          <td class="text-right">{{ number_format($order->due, 2) }}</td>
        </tr>
      </table>
    </div>
    <hr>
    
    <!-- Transaction Barcode -->
    <div class="text-center" style="margin: 8px 0;">
      <svg id="barcode"></svg>
      <div style="font-size: 10px;">ORD-{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</div>
    </div>
    
    <!-- Footer Note -->
    <div class="text-center">
      <p class="text-muted" style="font-size: 12px; margin-bottom: 2px;">@if(readConfig('is_show_note_invoice')){{ readConfig('note_to_customer_invoice') }}@endif</p>
      <div style="font-size: 10px; border-top: 1px solid #000; padding-top: 5px; margin-top: 2px;">
        <strong>Software by SINYX</strong><br>
        Contact: +92 342 9031328
      </div>
    </div>
  </div>

  <!-- Print Button -->
  <div class="text-center mt-3 no-print pb-3">
    <a href="/admin/cart" class="btn bg-gradient-secondary text-white mr-2"><i class="fas fa-arrow-left"></i> Back to POS</a>
    <button type="button" onclick="window.print()" class="btn bg-gradient-primary text-white"><i class="fas fa-print"></i> Print</button>
  </div>
</div>
@endsection

@push('style')
<style>
  .receipt-container {
    border: 1px dotted #000;
    padding: 8px;
  }

  hr {
    border: none;
    border-top: 1px dashed #000;
    margin: 5px 0;
  }

  table {
    width: 100%;
  }

  td, th {
    padding: 2px 0;
  }

  .text-right {
    text-align: right;
  }

  @media print {
    @page {
      margin-top: 5px !important;
      margin-left: 0px !important;
      padding-left: 0px !important;
    }

    .no-print {
      display: none !important;
    }

    footer {
      display: none !important;
    }
  }
</style>
@endpush

@push('script')
<!-- JsBarcode library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    try {
      JsBarcode("#barcode", "ORD{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}", {
        format: "CODE128",
        width: 1.5,
        height: 40,
        displayValue: false,
        margin: 0
      });
    } catch (e) {
      console.error('Barcode error:', e);
    }
  });
  
  // Auto-print
  window.onload = function() {
    setTimeout(function() {
      window.print();
    }, 500);
  };
</script>
@endpush