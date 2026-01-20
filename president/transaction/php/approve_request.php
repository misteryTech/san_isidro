<?php
session_start();
require_once '../../../database/connection.php';

/* 🔐 Authorization */
if (empty($_SESSION['password_verified']) || empty($_SESSION['approve_ids'])) {
    $_SESSION['error_message'] = "Unauthorized request";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

$position = $_SESSION['position'] ?? null;
if (!$position) {
    $_SESSION['error_message'] = "Position not found in session";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

$ids = $_SESSION['approve_ids'];
$prr_id  = $ids['prr_id'] ?? null;
$dba_id  = $ids['dba_id'] ?? null;
$osca_id = $ids['osca_id'] ?? null;

if (!$prr_id || !$osca_id) {
    $_SESSION['error_message'] = "Missing required identifiers";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

/* 💰 Disbursement Amount */
$disb_amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
if ($disb_amount <= 0) {
    $_SESSION['error_message'] = "Invalid disbursement amount";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

$conn->begin_transaction();

try {

    /* 1️⃣ Insert disbursement batch */
    $stmtBatch = $conn->prepare("
        INSERT INTO disbursements
            (prr_id, dba_id, osca_id, approved_by, amount)
        VALUES (?, ?, ?, ?, ?)
    ");
    if (!$stmtBatch) throw new Exception("Prepare failed (disbursements): " . $conn->error);

    $stmtBatch->bind_param("iissd", $prr_id, $dba_id, $osca_id, $position, $disb_amount);
    $stmtBatch->execute();
    if ($stmtBatch->affected_rows === 0) throw new Exception("Failed to create disbursement record.");
    $batch_id = $stmtBatch->insert_id;

    /* 2️⃣ Update payment_release_requests */
    $stmtPRR = $conn->prepare("
        UPDATE payment_release_requests
        SET status = 'Approved',
            approved = 1,
            approved_by = ?,
            approved_at = NOW()
        WHERE id = ?
    ");
    if (!$stmtPRR) throw new Exception("Prepare failed (payment_release_requests): " . $conn->error);

    $stmtPRR->bind_param("si", $position, $prr_id);
    $stmtPRR->execute();

    /* 3️⃣ Update deceased_benefit_applications status safely */
    $stmtDBA = $conn->prepare("
        UPDATE deceased_benefit_applications
        SET status = 'Approved'
        WHERE osca_id = ?
    ");
    if (!$stmtDBA) throw new Exception("Prepare failed (deceased_benefit_applications): " . $conn->error);

    $stmtDBA->bind_param("s", $osca_id);
    $stmtDBA->execute();

    $conn->commit();

    unset($_SESSION['password_verified'], $_SESSION['approve_ids']);

    // ✅ Set success message and redirect
    $_SESSION['success_message'] = "Disbursement approved successfully!";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
