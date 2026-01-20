document.addEventListener("DOMContentLoaded", () => {
  const modalEl = document.getElementById("cashlessPaymentModal");
  const modal = new bootstrap.Modal(modalEl);

  const confirmModalEl = document.getElementById("cashlessConfirmModal");
  const confirmModal = new bootstrap.Modal(confirmModalEl);

  const form = document.getElementById("cashlessPaymentForm");
  const statusBox = document.getElementById("cashlessStatusBox");
  const processBtn = document.getElementById("cashlessProcessBtn");
  const confirmBtn = document.getElementById("confirmCashlessBtn");

  let currentPaymentId = null;

  // ================= OPEN PAYMENT DETAILS MODAL =================
  document.querySelectorAll(".cashlessViewBtn").forEach((btn) => {
    btn.addEventListener("click", () => {
      if (!btn.dataset.payment) return;

      let data;
      try {
        data = JSON.parse(btn.dataset.payment);
      } catch {
        return;
      }

      currentPaymentId = data.id;

      document.getElementById("modal_payment_id").value = data.id ?? "";
      document.getElementById("modal_user").innerText =
        (data.first_name ?? "") + " " + (data.last_name ?? "");
      document.getElementById("modal_osca").innerText = data.osca_id ?? "";
      document.getElementById("modal_deceased").innerText =
        data.deceased_name ?? "";
      document.getElementById("modal_amount").innerText = parseFloat(
        data.amount ?? 0
      ).toFixed(2);
      document.getElementById("modal_ref").innerText =
        data.reference_no ?? "N/A";
      document.getElementById("modal_date").innerText = data.payment_date
        ? new Date(data.payment_date).toLocaleString()
        : "";

      const img = document.getElementById("modal_reference_picture");
      if (data.receipt_photo) {
        img.src = "../assets/uploads/payment_receipts/" + data.receipt_photo;
        img.classList.remove("d-none");
      } else {
        img.classList.add("d-none");
        img.src = "";
      }

      statusBox.innerHTML = "";
      processBtn.disabled = false;

      modal.show();
    });
  });

  // ================= OPEN CONFIRMATION MODAL =================
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    if (!currentPaymentId) return;

    confirmModal.show();
  });

  // ================= CONFIRM & PROCESS =================
  confirmBtn.addEventListener("click", () => {
    if (!currentPaymentId) return;

    confirmBtn.disabled = true;
    processBtn.disabled = true;

    confirmModal.hide();

    statusBox.innerHTML = `
      <div class="alert alert-info">Processing payment...</div>
    `;

    fetch("../treasurer/transaction/php/process_cashless_payment.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ payment_id: currentPaymentId }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "success") {
          statusBox.innerHTML = `
            <div class="alert alert-success">
              Payment completed successfully ✅
            </div>
          `;

          setTimeout(() => {
            modal.hide();
            window.location.reload();
          }, 1200);
        } else {
          processBtn.disabled = false;
          confirmBtn.disabled = false;
          statusBox.innerHTML = `
            <div class="alert alert-danger">${data.message}</div>
          `;
        }
      })
      .catch(() => {
        processBtn.disabled = false;
        confirmBtn.disabled = false;
        statusBox.innerHTML = `
          <div class="alert alert-danger">Server error occurred</div>
        `;
      });
  });
});
