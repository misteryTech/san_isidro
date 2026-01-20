document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registerForm");
  const submitBtn = form.querySelector('button[type="submit"]');

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    // Bootstrap validation
    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    // Confirm submission
    const confirm = await Swal.fire({
      icon: "question",
      title: "Confirm Registration",
      text: "Are you sure all information is correct?",
      showCancelButton: true,
      confirmButtonText: "Yes, submit",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#198754",
    });

    if (!confirm.isConfirmed) return;

    // Disable button to prevent double submit
    submitBtn.disabled = true;

    // Show loading alert
    Swal.fire({
      title: "Processing...",
      text: "Creating your account",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    const formData = new FormData(form);

    try {
      const res = await fetch("assets/js/transaction/registration.php", {
        method: "POST",
        body: formData,
      });

      if (!res.ok) throw new Error("Server error");

      const data = await res.json();
      Swal.close();

      if (data.status === "success") {
        Swal.fire({
          icon: "success",
          title: "Registration Successful 🎉",
          text: data.message,
          confirmButtonColor: "#198754",
        }).then(() => {
          form.reset();
          form.classList.remove("was-validated");
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Registration Failed",
          text: data.message,
          confirmButtonColor: "#dc3545",
        });
      }
    } catch (error) {
      Swal.close();
      Swal.fire({
        icon: "warning",
        title: "Network Error ⚠️",
        text: "Unable to connect to the server. Please try again.",
        confirmButtonColor: "#ffc107",
      });
    } finally {
      submitBtn.disabled = false;
    }
  });
});
