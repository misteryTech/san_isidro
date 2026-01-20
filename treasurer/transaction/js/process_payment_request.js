document.addEventListener("DOMContentLoaded", () => {
  document.addEventListener("submit", async (e) => {
    const form = e.target.closest(".releaseForm");
    if (!form) return;

    e.preventDefault();

    // ⛔ Prevent duplicate submissions (hard lock)
    if (form.dataset.submitting === "true") return;
    form.dataset.submitting = "true";

    const memberInput = form.querySelector('input[name="osca_id"]');
    const methodSelect = form.querySelector('select[name="released_method"]');
    const amountInput = form.querySelector('input[name="release_amount"]');
    const submitBtn = form.querySelector('button[type="submit"]');

    /* 🔍 Validation */
    if (!memberInput || !memberInput.value.trim()) {
      Swal.fire("Invalid OSCA ID", "Please select a valid member.", "error");
      resetFormState();
      return;
    }

    if (!methodSelect || !methodSelect.value) {
      Swal.fire("Missing Method", "Please select a release method.", "error");
      resetFormState();
      return;
    }

    if (!amountInput || parseFloat(amountInput.value) <= 0) {
      Swal.fire("Invalid Amount", "Please enter a valid amount.", "error");
      resetFormState();
      return;
    }

    const memberId = memberInput.value.trim(); // string (SC/Rxxxx)
    const releasedMethod = methodSelect.value;
    const releaseAmount = parseFloat(amountInput.value);

    /* 🔁 Confirmation */
    const confirm = await Swal.fire({
      icon: "warning",
      title: "Confirm Release",
      html: `
        <p><strong>OSCA ID:</strong> ${memberId}</p>
        <p><strong>Method:</strong> ${releasedMethod}</p>
        <p><strong>Amount:</strong> ₱${releaseAmount.toLocaleString()}</p>
      `,
      showCancelButton: true,
      confirmButtonText: "Yes, submit",
      cancelButtonText: "Cancel",
      reverseButtons: true,
    });

    if (!confirm.isConfirmed) {
      resetFormState();
      return;
    }

    /* ⏳ Loading */
    submitBtn.disabled = true;
    Swal.fire({
      title: "Processing...",
      text: "Submitting release request.",
      allowOutsideClick: false,
      allowEscapeKey: false,
      didOpen: () => Swal.showLoading(),
    });

    try {
      const formData = new FormData();
      formData.append("osca_id", memberId);
      formData.append("released_method", releasedMethod);
      formData.append("release_amount", releaseAmount);

      const response = await fetch(
        "../treasurer/transaction/php/release_payment.php",
        { method: "POST", body: formData }
      );

      const raw = await response.text();
      let result;

      try {
        result = JSON.parse(raw);
      } catch {
        console.error("Invalid JSON:", raw);
        Swal.fire("Server Error", "Invalid server response.", "error");
        resetFormState();
        return;
      }

      if (result.success) {
        Swal.fire({
          icon: "success",
          title: "Submitted",
          text: result.message || "Release request submitted successfully!",
        }).then(() => {
          const modalEl = form.closest(".modal");
          const modal = bootstrap.Modal.getInstance(modalEl);
          modal?.hide();
          location.reload();
        });
      } else {
        Swal.fire(
          "Failed",
          result.error || "Unable to submit request.",
          "error"
        );
      }
    } catch (err) {
      console.error(err);
      Swal.fire(
        "Unexpected Error",
        "Something went wrong. Please try again.",
        "error"
      );
    } finally {
      resetFormState();
    }

    /* 🔄 Reset submit lock */
    function resetFormState() {
      submitBtn.disabled = false;
      form.dataset.submitting = "false";
    }
  });
});
