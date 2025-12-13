document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginProcess");
  const responseBox = document.getElementById("responseBox");

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

      if (res.ok) {
        const data = await res.json();

        console.log("Server response:", data); // <-- ADD THIS
        const position = data.position?.toLowerCase();

        if (position === "member") {
          window.location.href = "member/dashboard.php";
        } else if (position === "admin") {
          window.location.href = "dashboard.php";
        } else if (position === "treasurer") {
          window.location.href = "treasurer/dashboard.php";
        } else if (position === "staff") {
          window.location.href = "staff/dashboard.php";
        } else if (position === "president") {
          window.location.href = "president/dashboard.php";
        } else {
          console.warn("Unexpected position:", data.position);
          responseBox.innerHTML = "⚠️ Not Registered Member.";
        }
      } else {
        // must parse JSON first before accessing data.message
        const errorData = await res.json().catch(() => ({}));
        responseBox.innerHTML = "❌ " + (errorData.message || "Login failed.");
      }
    } catch (err) {
      responseBox.innerHTML = "⚠️ Network error: " + err.message;
    }
  });
});
