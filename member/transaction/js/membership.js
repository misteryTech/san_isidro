document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("membership_form");
  const submitBtn = form ? form.querySelector('button[type="submit"]') : null;

  if (form) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      // Bootstrap validation
      if (!form.checkValidity()) {
        form.classList.add("was-validated");
        return;
      }

      // Confirm action
      const confirm = await Swal.fire({
        icon: "question",
        title: "Confirm Membership",
        text: "Do you want to submit this membership application?",
        showCancelButton: true,
        confirmButtonText: "Yes, submit",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#198754",
      });

      if (!confirm.isConfirmed) return;

      // Disable submit button
      if (submitBtn) submitBtn.disabled = true;

      // Loading alert
      Swal.fire({
        title: "Processing...",
        text: "Submitting membership request",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
      });

      const formData = new FormData(form);

      try {
        const res = await fetch("../member/transaction/php/membership.php", {
          method: "POST",
          body: formData,
        });

        if (!res.ok) {
          throw new Error("Server error");
        }

        const data = await res.json();
        Swal.close();

        if (data.status === "success") {
          Swal.fire({
            icon: "success",
            title: "Success 🎉",
            text: data.message,
            confirmButtonColor: "#198754",
          }).then(() => {
            form.reset();
            form.classList.remove("was-validated");
            loadMemberCard(); // refresh count
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Submission Failed",
            text: data.message,
            confirmButtonColor: "#dc3545",
          });
        }
      } catch (err) {
        Swal.close();
        Swal.fire({
          icon: "warning",
          title: "Network Error ⚠️",
          text: "Unable to connect to the server. Please try again.",
          confirmButtonColor: "#ffc107",
        });
      } finally {
        if (submitBtn) submitBtn.disabled = false;
      }
    });
  }

  loadMemberCard();
  setInterval(loadMemberCard, 10000);
});

function loadMemberCard() {
  fetch("../member/transaction/php/membership_count.php")
    .then((response) => response.json())
    .then((data) => {
      const totalRegularMembers = data.total ?? 0;

      document.getElementById("membershipCardContainer").innerHTML = `
        <div class="card shadow-sm border-0">
          <div class="card-body text-center">
            <h5 class="card-title fw-bold">Membership Benefits</h5>
            <p class="mb-3">
              Regular Members are entitled to the following benefits:
            </p>

            <div class="alert alert-success fw-bold">
              Total Regular Members: ${totalRegularMembers}
            </div>

            <ul class="list-group text-start">
              <li class="list-group-item">✔ Full voting rights</li>
              <li class="list-group-item">✔ Participation in special programs</li>
              <li class="list-group-item">✔ Priority access to services</li>
              <li class="list-group-item fw-bold text-primary">
                ✔ Paid Monthly Mortuary Fees to avail ₱40,000 package
              </li>
              <li class="list-group-item">✔ Other exclusive member privileges</li>
            </ul>
          </div>
        </div>
      `;
    })
    .catch((error) => {
      console.error("Error fetching member count:", error);
    });
}
