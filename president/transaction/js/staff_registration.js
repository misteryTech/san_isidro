document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.getElementById("registerForm");

  registerForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!registerForm.checkValidity()) {
      registerForm.classList.add("was-validated");
      Swal.fire({
        icon: "warning",
        title: "Invalid Form",
        text: "Please fill in all required fields correctly.",
      });
      return;
    }

    const formData = new FormData(registerForm);

    try {
      const res = await fetch(
        "../president/transaction/php/staff_registration.php",
        {
          method: "POST",
          body: formData,
        }
      );

      const data = await res.json().catch(() => ({}));

      if (res.ok && data.success) {
        Swal.fire({
          icon: "success",
          title: "Registration Successful",
          text: data.message || "Staff member registered successfully!",
          confirmButtonText: "OK",
        }).then(() => {
          // 🔄 REFRESH PAGE
          window.location.reload();
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Registration Failed",
          text: data.message || "Unable to complete registration.",
        });
      }
    } catch (err) {
      Swal.fire({
        icon: "warning",
        title: "Network Error",
        text: err.message,
      });
    }
  });
});
