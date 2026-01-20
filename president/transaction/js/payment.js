
document.addEventListener("DOMContentLoaded", () => {
  let selectedOscaId = null;

  // When opening the modal, set the user OSCA ID dynamically
  document.querySelectorAll(".set-inactive-btn").forEach((button) => {
    button.addEventListener("click", () => {
      selectedOscaId = button.dataset.oscaId;
      document.getElementById("modalOscaId").textContent = selectedOscaId;
    });
  });

  // Confirm button in modal
  document
    .getElementById("confirmInactiveBtn")
    .addEventListener("click", () => {
      if (!selectedOscaId) return;

      fetch("../president/transaction/php/update_user_status.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ osca_id: selectedOscaId, status: "INACTIVE" }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert(`User ${selectedOscaId} is now INACTIVE.`);
            // Update the status cell dynamically
            const row = document.querySelector(
              `tr[data-osca-id='${selectedOscaId}']`,
            );
            if (row) {
              row.querySelector("td:nth-child(2)").innerHTML =
                '<span class="badge bg-secondary">INACTIVE</span>';
            }
          } else {
            alert(`Error: ${data.message}`);
          }
        })
        .catch((err) => {
          console.error(err);
          alert("Failed to update user status.");
        });
    });
});
