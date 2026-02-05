<!-- ================= WALK-IN PAYMENT MODAL ================= -->
<div class="modal fade" id="walkinPaymentModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="walkinPaymentForm" class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Walk-in Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="responseBox mt-2"></div>

        <!-- Hidden inputs -->
        <input type="hidden" name="application_id" id="walkin_application_id">
        <input type="hidden" name="user_id" id="walkin_user_id">
        <input type="hidden" name="osca_id" id="walk_in_osca_id">

        <!-- Display application/deceased name -->
        <p><strong>Deceased:</strong> <span id="walkin_deceased_name"></span></p>

        <div class="mb-3">
          <label class="form-label">Receipt No.</label>
          <input type="text" name="receipt_no" class="form-control" id="walkin_receipt" placeholder="Enter receipt number" required>
        </div>

        <!-- Amount -->
        <div class="mb-3">
          <label class="form-label">Amount</label>
          <input type="number" name="amount" class="form-control" id="walkin_amount" placeholder="Enter amount" required>
        </div>

        <!-- Payment Method -->
        <div class="mb-3">
          <label class="form-label">Payment Method</label>
          <select name="payment_method" class="form-select" required>
            <option value="">Select</option>
            <option value="Cash">Cash</option>

          </select>
        </div>

        <!-- Optional reference number (auto-generated on backend) -->
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit Payment</button>
      </div>

    </form>
  </div>
</div>
