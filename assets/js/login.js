document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginProcess");
  const responseBox = document.getElementById("responseBox");

  /* -----------------------------
     PASSWORD TOGGLE
  ----------------------------- */
  const passwordInput = document.getElementById("yourPassword");
  const toggleBtn = document.getElementById("togglePassword");
  const icon = document.getElementById("toggleIcon");

  if (toggleBtn && passwordInput) {
    toggleBtn.addEventListener("click", () => {
      const isHidden = passwordInput.type === "password";
      passwordInput.type = isHidden ? "text" : "password";
      icon.className = isHidden ? "bi bi-eye-slash" : "bi bi-eye";
    });
  }

  /* -----------------------------
     LOGIN SUBMIT
  ----------------------------- */
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    const formData = new FormData(form);

    try {
      const res = await fetch("assets/js/transaction/login.php", {
        method: "POST",
        body: formData,
      });

      const data = await res.json();

      // Handle server-side errors (including deceased status)
      if (data.status !== "success") {
        responseBox.innerHTML = "❌ " + (data.message || "Login failed.");
        return;
      }

      const position = data.position?.toLowerCase();

      const routes = {
        member: "member/dashboard.php",
        admin: "dashboard.php",
        treasurer: "treasurer/dashboard.php",
        staff: "staff/dashboard.php",
        president: "president/dashboard.php",
      };

      if (routes[position]) {
        window.location.href = routes[position];
      } else {
        responseBox.innerHTML = "⚠️ Not Registered Member.";
      }
    } catch (err) {
      responseBox.innerHTML = "⚠️ Network error: " + err.message;
    }
  });
});
