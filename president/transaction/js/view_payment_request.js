
document.getElementById("approveForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');

    if (submitBtn.disabled) return;
    submitBtn.disabled = true;

    /* 🔁 Redundant Confirmation */
    const confirmAction = await Swal.fire({
        icon: "warning",
        title: "Confirm Approval",
        text: "This action will approve the transaction and cannot be undone.",
        showCancelButton: true,
        confirmButtonText: "Yes, approve",
        cancelButtonText: "Cancel",
        reverseButtons: true
    });

    if (!confirmAction.isConfirmed) {
        submitBtn.disabled = false;
        return;
    }

    /* 🔐 Password Check */
    const password = document.getElementById("confirmPassword").value.trim();
    if (!password) {
        submitBtn.disabled = false;
        Swal.fire({
            icon: "error",
            title: "Password Required",
            text: "Please enter your password to continue."
        });
        return;
    }

    const formData = new URLSearchParams(new FormData(form));
    formData.append("password", password);

    Swal.fire({
        title: "Verifying...",
        text: "Checking password, please wait.",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const response = await fetch(
            "../president/transaction/php/verify_password.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: formData.toString()
            }
        );

        const result = await response.json();

        if (!result.success) {
            Swal.fire({
                icon: "error",
                title: "Authentication Failed",
                text: result.error || "Incorrect password."
            });
            submitBtn.disabled = false;
            return;
        }

        /* ✅ Password Verified */
        Swal.fire({
            icon: "success",
            title: "Verified",
            text: "Password verified successfully."
        }).then(() => {
            form.submit(); // proceed to real approval
        });

    } catch (err) {
        Swal.fire({
            icon: "error",
            title: "Server Error",
            text: "Unable to verify password. Try again."
        });
        submitBtn.disabled = false;
    }
});

function printCard() {
  const card = document.querySelector(".card");
  if (!card) return;

  const printWindow = window.open("", "_blank", "width=900,height=600");

  printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print</title>
            <link rel="stylesheet" href="bootstrap.min.css">
            <style>
                body { padding: 20px; }
            </style>
        </head>
        <body>
            ${card.outerHTML}
        </body>
        </html>
    `);

  printWindow.document.close();
  printWindow.focus();
  printWindow.print();
  printWindow.close();
}