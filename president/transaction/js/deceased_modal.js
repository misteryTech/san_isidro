document.addEventListener("DOMContentLoaded", () => {
  // Handle both acceptForm and declineForm
  document
    .querySelectorAll("form[id^='approveForm'], form[id^='rejectForm']")
    .forEach((form) => {
      form.addEventListener("submit", function (e) {
        e.preventDefault(); // 🚫 stop normal submit

        const formData = new FormData(this);


        fetch("../president/transaction/php/deceased_transaction.php", {
          method: "POST",
          body: formData,
        })
          .then((res) => res.json())
          .then((data) => {
            if (data.status === "success") {
              alert(data.message);

              // Close the modal
              const modalEl = this.closest(".modal");
              const modal = bootstrap.Modal.getInstance(modalEl);
              modal.hide();

              // Optional refresh
              location.reload();
            } else {
              alert(data.message);
            }
          })
          .catch((err) => {
            alert("Error: " + err.message);
          });
      });
    });
});
