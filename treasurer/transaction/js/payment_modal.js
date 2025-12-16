document.addEventListener("DOMContentLoaded", () => {
  const walkinForm = document.getElementById("walkinPaymentForm");
  if (!walkinForm) return; // exit if modal not in DOM

  const responseBox = walkinForm.querySelector(".responseBox");

  walkinForm.addEventListener("submit", function (e) {
    e.preventDefault();

    if (!responseBox) return;
    responseBox.innerHTML = ""; // clear previous messages

    const formData = new FormData(this);
    const submitBtn = walkinForm.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    fetch("../treasurer/transaction/php/process_walkin_payment.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (submitBtn) submitBtn.disabled = false;

        if (data.status === "success") {
          responseBox.innerHTML = `<div class="alert alert-success">${data.message} <br> Reference No: ${data.reference_no}</div>`;

          const appId = formData.get("application_id");
          const row = document.querySelector(`tr[data-app-id="${appId}"]`);
          if (row) {
            const statusCell = row.querySelector(".paymentStatus");
            if (statusCell)
              statusCell.innerHTML =
                '<span class="badge bg-success">Paid</span>';

            const btn = row.querySelector(".walkinPayBtn");
            if (btn) btn.remove();
          }

          setTimeout(() => {
            const modalEl = document.getElementById("walkinPaymentModal");
            if (modalEl) {
              const modal = bootstrap.Modal.getInstance(modalEl);
              if (modal) modal.hide();
            }
          }, 2000);
        } else {
          responseBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
      })
      .catch((err) => {
        if (submitBtn) submitBtn.disabled = false;
        console.error(err);
        responseBox.innerHTML = `<div class="alert alert-danger">An error occurred. Please try again.</div>`;
      });
  });

  // Populate modal dynamically when clicking walk-in buttons
  document.querySelectorAll(".walkinPayBtn").forEach((button) => {
    button.addEventListener("click", function () {
      const appInput = document.getElementById("walkin_application_id");
      const userInput = document.getElementById("walkin_user_id");
      const nameSpan = document.getElementById("walkin_deceased_name");

      if (appInput) appInput.value = this.dataset.application;
      if (userInput) userInput.value = this.dataset.user;
      if (nameSpan) nameSpan.innerText = this.dataset.name;

      const row = this.closest("tr");
      if (row) row.setAttribute("data-app-id", this.dataset.application);

      const modalEl = document.getElementById("walkinPaymentModal");
      if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
      }
    });
  });
});
