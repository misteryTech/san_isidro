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
        const data = await res.json(); // expect JSON now

        if (data.status === "success") {
          responseBox.innerHTML = "🎉 " + data.message;

          if (data.position === "Member") {
            window.location.href = "member/member_dashboard.php";
          } else if (data.position === "Admin") {
            window.location.href = "admin_dashboard.php";
          } else {
            responseBox.innerHTML = "⚠️ Unknown position.";
          }
        } else {
          responseBox.innerHTML = "❌ " + data.message;
        }
      } else {
        responseBox.innerHTML = "❌ Error submitting form.";
      }
    } catch (err) {
      responseBox.innerHTML = "⚠️ Network error: " + err.message;
    }
  });
});
