<?php
session_start();
require_once __DIR__ . "/../../../database/connection.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
    exit;
}

$user_id             = $_POST['user_id'] ?? null;
$osca_id             = $_POST['osca_id'] ?? null;
$deceased_benefit_id = $_POST['application_id'] ?? null;
$amount              = $_POST['amount'] ?? null;
$payment_method      = $_POST['payment_method'] ?? null;
$receipt_no          = $_POST['receipt_no'] ?? null;

$reference_no     = 'WI-' . time() . '-' . rand(1000, 9999);
$transaction_type = 'walkin';
$remarks          = 'Walk-in payment';
$payment_status   = 'completed';
$transact_by      = $_SESSION['id'] ?? 'System';

if (!$user_id || !$deceased_benefit_id || !$amount || !$payment_method) {
    echo json_encode([
        "status" => "error",
        "message" => "All required fields must be filled."
    ]);
    exit;
}

/* ===============================
   INSERT WALK-IN PAYMENT
================================ */
$stmt = $conn->prepare("
    INSERT INTO payments (
        osca_id,
        deceased_benefit_id,
        transaction_type,
        amount,
        receipt_no,
        payment_status,
        payment_method,
        reference_no,
        remarks,
        transact_by,
        date_transact

    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
");

$stmt->bind_param(
    "sisdssssss",
    $osca_id,
    $deceased_benefit_id,
    $transaction_type,
    $amount,
    $receipt_no,
    $payment_status,
    $payment_method,
    $reference_no,
    $remarks,
    $transact_by,

);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Walk-in payment recorded successfully.",
        "reference_no" => $reference_no
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to save walk-in payment."
    ]);
}

$stmt->close();
$conn->close();
