<?php
require_once __DIR__ . '/../../../database/connection.php';

header('Content-Type: application/json');

// IMPORTANT: stop PHP notices from breaking JSON
ini_set('display_errors', 0);
error_reporting(0);

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['payment_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing payment ID'
    ]);
    exit;
}

$payment_id = (int) $data['payment_id'];

$conn->begin_transaction();

try {
    // 1. CHECK PAYMENT EXISTS & STILL PENDING
    $check = $conn->prepare("
        SELECT payment_status
        FROM payments
        WHERE id = ? AND payment_method = 'cashless'
        LIMIT 1
    ");
    $check->bind_param("i", $payment_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows === 0) {
        throw new Exception("Payment not found");
    }

    $row = $res->fetch_assoc();
    if ($row['payment_status'] !== 'pending') {
        throw new Exception("Payment already processed");
    }

    // 2. UPDATE PAYMENT
    $update = $conn->prepare("
        UPDATE payments
        SET payment_status = 'completed'
        WHERE id = ?
    ");
    $update->bind_param("i", $payment_id);
    $update->execute();

    if ($update->affected_rows === 0) {
        throw new Exception("Failed to update payment");
    }

    $conn->commit();

    echo json_encode([
        'status' => 'success'
    ]);
    exit;

} catch (Exception $e) {
    $conn->rollback();

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}
