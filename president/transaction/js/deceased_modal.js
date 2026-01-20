document.addEventListener("DOMContentLoaded", () => {
  document
    .querySelectorAll("form[id^='approveForm'], form[id^='rejectForm']")
    .forEach((form) => {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("../president/transaction/php/deceased_transaction.php", {
          method: "POST",
          body: formData,
        })
          .then((res) => res.json())
          .then(() => {
            const modalEl = this.closest(".modal");
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            setTimeout(() => location.reload(), 300);
          })
          .catch(() => {
            const modalEl = this.closest(".modal");
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            setTimeout(() => location.reload(), 300);
          });
      });
    });
});
