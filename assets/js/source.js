document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registerForm");
  const submitBtn = form.querySelector('button[type="submit"]');
  const chapterSelect = document.getElementById("chapter");

  // Disable submit until chapters load
  submitBtn.disabled = true;

  /* ============================
     LOAD CHAPTERS (AJAX)
  ============================ */
  fetch("assets/js/transaction/fetch_chapter.php")
    .then((res) => {
      if (!res.ok) throw new Error("Failed to fetch chapters");
      return res.json();
    })
    .then((chapters) => {
      if (!Array.isArray(chapters)) {
        throw new Error("Invalid chapter data");
      }

      chapterSelect.innerHTML = '<option value="">Choose...</option>';

      chapters.forEach((chapter) => {
        const option = document.createElement("option");
        option.value = chapter.chapter_code; // or chapter.chapter_name
        option.textContent = chapter.chapter_name;
        chapterSelect.appendChild(option);
      });

      submitBtn.disabled = false;
    })
    .catch((err) => {
      console.error("Chapter load error:", err);

      chapterSelect.innerHTML =
        '<option value="" disabled selected>Unable to load chapters</option>';

      Swal.fire({
        icon: "error",
        title: "Load Failed",
        text: "Unable to load chapters. Please refresh the page.",
        confirmButtonColor: "#dc3545",
      });
    });

  /* ============================
     FORM SUBMISSION
  ============================ */
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    // Prevent submit if chapters not loaded
    if (chapterSelect.disabled || chapterSelect.options.length <= 1) {
      Swal.fire({
        icon: "warning",
        title: "Chapter Required",
        text: "Please wait until chapters are loaded.",
        confirmButtonColor: "#ffc107",
      });
      return;
    }

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

    submitBtn.disabled = true;

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
          chapterSelect.selectedIndex = 0;
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
