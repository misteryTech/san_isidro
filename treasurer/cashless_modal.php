<div class="modal fade" id="cashlessPaymentModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="cashlessPaymentForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Cashless Payment Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="modal_payment_id">

          <p><strong>User:</strong> <span id="modal_user"></span></p>
          <p><strong>OSCA ID:</strong> <span id="modal_osca"></span></p>
          <p><strong>Deceased:</strong> <span id="modal_deceased"></span></p>
          <p><strong>Amount:</strong> ₱<span id="modal_amount"></span></p>
          <p><strong>Reference No:</strong> <span id="modal_ref"></span></p>
          <p><strong>Date:</strong> <span id="modal_date"></span></p>

          <div class="mb-3">
            <strong>Reference Picture</strong><br>
            <img id="modal_reference_picture"
                 class="img-fluid rounded d-none"
                 style="max-height:300px;">
          </div>

          <!-- STATUS -->
          <div id="cashlessStatusBox"></div>
        </div>

        <div class="modal-footer">
          <button type="submit"
                  id="cashlessProcessBtn"
                  class="btn btn-success">
            Mark as Completed
          </button>
          <button type="button"
                  class="btn btn-secondary"
                  data-bs-dismiss="modal">
            Close
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
