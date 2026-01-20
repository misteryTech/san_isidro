document.addEventListener("DOMContentLoaded", () => {
  const walkinForm = document.getElementById("walkinPaymentForm");
  if (!walkinForm) return;

  const responseBox = walkinForm.querySelector(".responseBox");

  // Initialize modal once
  const modalEl = document.getElementById("walkinPaymentModal");
  const modal = new bootstrap.Modal(modalEl);

  // Listen for modal hidden event to reload page
  modalEl.addEventListener("hidden.bs.modal", () => {
    window.location.href = window.location.href; // full reload
  });

  // Handle form submission
  walkinForm.addEventListener("submit", function (e) {
    e.preventDefault();
    if (!responseBox) return;
    responseBox.innerHTML = "";

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
          responseBox.innerHTML = `
            <div class="alert alert-success">
              ${data.message}<br>
              Reference No: <strong>${data.reference_no}</strong>
            </div>
          `;

          // Close modal (reload happens automatically after hidden)
          modal.hide();
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

        document.querySelectorAll(".walkinPayBtn").forEach((button) => {
          button.addEventListener("click", function () {
            const appInput = document.getElementById("walkin_application_id");
            const userInput = document.getElementById("walkin_user_id");
            const nameSpan = document.getElementById("walkin_deceased_name");
            const oscaInput = document.getElementById("walk_in_osca_id");

            if (appInput) appInput.value = this.dataset.application;
            if (userInput) userInput.value = this.dataset.user;
            if (nameSpan) nameSpan.innerText = this.dataset.name;
            if (oscaInput) oscaInput.value = this.dataset.osca; // ✅ THIS WAS MISSING

            console.log("OSCA:", this.dataset.osca);

            modal.show();
          });
        });
});
