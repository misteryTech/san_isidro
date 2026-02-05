<?php
require_once __DIR__ . '/../../../database/connection.php';

$osca_id      = $_POST['osca_id'];
$type         = $_POST['payment_type'];
$receipt      = $_POST['receipt'];
$amount       = $_POST['amount'];
$month        = $_POST['month'] ?? null;
$region       = $_POST['region'] ?? null;
$today        = date('Y-m-d');

switch ($type) {

  case 'membership':
    $sql = "INSERT INTO membership_fees (osca_id, amount, due_date, status, receipt_no)
            VALUES (?, ?, ?, 'paid', ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdss", $osca_id, $amount, $today, $receipt);
    break;

  case 'monthly':
    $year = date('Y', strtotime($month));
    $sql = "INSERT INTO monthly_dues (osca_id, amount, month, status, receipt_no)
            VALUES (?, ?, ?, 'paid', ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssds", $osca_id, $amount, $year, $receipt);
    break;

  case 'regional':
    $sql = "INSERT INTO regional_fees (osca_id, amount, region, due_date, status, receipt_no)
            VALUES (?, ?, ?, ?, 'paid', ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsss", $osca_id, $amount, $region, $today, $receipt);
    break;

  default:
    die('Invalid payment');
}

$stmt->execute();
header("Location: ../../membership_payment.php?status=success");
exit;
