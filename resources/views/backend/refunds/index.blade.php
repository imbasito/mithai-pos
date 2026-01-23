@extends('backend.master')

@section('title', 'Refunds')

@section('content')
<div class="card">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-undo"></i> Refund History</h3>
  </div>
  <div class="card-body p-2 p-md-4 pt-0">
    <div class="row g-4">
      <div class="col-md-12">
        <div class="card-body table-responsive p-0" id="table_data">
          <table id="datatables" class="table table-hover">
            <thead>
              <tr>
                <th data-orderable="false">#</th>
                <th>Return #</th>
                <th>Order #</th>
                <th>Refund Amount</th>
                <th>Processed By</th>
                <th>Date</th>
                <th data-orderable="false">Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Refund Receipt Modal: Premium Maroon Theme -->
<div class="modal fade" id="refundReceiptModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg" style="border-radius: 12px; overflow: hidden; border: none;">
            <div class="modal-header text-white p-2 d-flex justify-content-between align-items-center" style="background: #800000;">
                <h5 class="modal-title m-0 ml-2" style="font-size: 1.1rem; font-weight: 600;"><i class="fas fa-receipt mr-2"></i> Return Receipt</h5>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-light mr-2 font-weight-bold shadow-sm px-3" onclick="printRefundFrame()">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button type="button" class="btn btn-sm btn-danger font-weight-bold shadow-sm px-3" style="background: #dc3545;" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Close
                    </button>
                </div>
            </div>
            <div class="modal-body p-0 position-relative" style="height: 650px; background: #fff;">
                 <!-- Guaranteed Loader -->
                 <div id="receiptLoader" class="d-flex flex-column justify-content-center align-items-center w-100 h-100 position-absolute" 
                      style="top:0; left:0; z-index:999; background:#fff;">
                      <div class="spinner-border text-maroon" role="status" style="color: #800000; width: 3rem; height: 3rem; border-width: 0.25em;"></div>
                      <span class="mt-3 font-weight-bold text-dark" style="font-size: 1.1rem;">Generating Receipt...</span>
                 </div>
                 
                 <!-- Iframe: The native onload is the most robust way to detect completion -->
                 <iframe id="refundReceiptFrame" name="refundReceiptFrame" src="about:blank" 
                         style="width:100%; height:100%; border:none; display:block; visibility: hidden;" 
                         scrolling="yes"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
  
  // The ONLY function allowed to hide the loader
  function finalizeReceiptLoad() {
      const loader = document.getElementById('receiptLoader');
      const frame = document.getElementById('refundReceiptFrame');
      
      console.log('Finalizing load...');
      if (loader) loader.style.setProperty('display', 'none', 'important');
      if (frame) {
          frame.style.visibility = 'visible';
          frame.style.display = 'block';
      }
  }

  function openRefundReceipt(url) {
      const frame = document.getElementById('refundReceiptFrame');
      const loader = document.getElementById('receiptLoader');
      
      // 1. Instantly reset UI
      loader.style.display = 'flex';
      frame.style.visibility = 'hidden';
      frame.src = 'about:blank';
      
      // 2. Open Modal
      $('#refundReceiptModal').modal('show');
      
      // 3. Set standard load handler
      frame.onload = function() {
          // Only trigger if it's the real content, or wait a bit
          if (frame.contentWindow.location.href !== "about:blank") {
              setTimeout(finalizeReceiptLoad, 100);
          }
      };

      // 4. Trigger Load
      const cacheBuster = url.indexOf('?') !== -1 ? '&_v=' : '?_v=';
      frame.src = url + cacheBuster + Date.now();

      // 5. Hard Fail-Safe (2.5 seconds - guaranteed to hide)
      setTimeout(finalizeReceiptLoad, 2500);
  }

  function printRefundFrame() {
      const frame = document.getElementById('refundReceiptFrame');
      if (frame && frame.contentWindow) {
          frame.contentWindow.focus();
          frame.contentWindow.print();
      }
  }

  // Double-Redundancy Handshake
  window.addEventListener('message', function(e) {
      if(e.data === 'receipt-loaded') { 
          finalizeReceiptLoad();
      }
      if(e.data === 'close-modal') { 
          $('#refundReceiptModal').modal('hide'); 
      }
  });

  $(function() {
    $('#datatables').DataTable({
      processing: true,
      serverSide: true,
      ajax: "{{ route('backend.admin.refunds.index') }}",
      columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
        { data: 'return_number', name: 'return_number' },
        { data: 'order_id', name: 'order_id' },
        { data: 'total_refund', name: 'total_refund' },
        { data: 'processed_by', name: 'processed_by' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', name: 'action' },
      ],
      order: [[1, 'desc']]
    });
    
    // Cleanup on modal close
    $('#refundReceiptModal').on('hidden.bs.modal', function() {
        document.getElementById('refundReceiptFrame').src = 'about:blank';
    });
  });
</script>
@endpush
