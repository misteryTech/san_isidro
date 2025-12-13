<?php
session_start();
include('../../../database/connection.php');
header('Content-Type: application/json');

$membership_id = $_POST['membership_id'] ?? null;
$remarks       = $_POST['remarks'] ?? '';
$action        = $_POST['action'] ?? null; // accept | decline
$transact_by   = $_SESSION['user_id'] ?? null;

if (!$membership_id) {
    echo json_encode(["status"=>"error","message"=>"membership_id missing"]);
    exit;
}

if (!$action) {
    echo json_encode(["status"=>"error","message"=>"action missing"]);
    exit;
}

if (!$transact_by) {
    echo json_encode(["status"=>"error","message"=>"session id missing"]);
    exit;
}

try {
    // 🚀 Start DB transaction
    $conn->begin_transaction();

    /* 1️⃣ Insert transaction log */
    $stmt1 = $conn->prepare("
        INSERT INTO membership_transaction
        (membership_id, remarks, transact_by, action, updated_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt1->bind_param("isss", $membership_id, $remarks, $transact_by, $action);

    if (!$stmt1->execute()) {
        throw new Exception($stmt1->error);
    }

    /* 2️⃣ Determine new status */
    if ($action === 'accept') {
        $newStatus = 'Verified';
        $promptMsg = 'Membership successfully verified.';
    } elseif ($action === 'decline') {
        $newStatus = 'Declined';
        $promptMsg = 'Membership has been declined.';
    } else {
        throw new Exception('Invalid action.');
    }

    /* 3️⃣ Update membership status */
    $stmt2 = $conn->prepare("
        UPDATE membership_table
        SET status = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt2->bind_param("si", $newStatus, $membership_id);

    if (!$stmt2->execute()) {
        throw new Exception($stmt2->error);
    }

    // ✅ Commit transaction
    $conn->commit();

    echo json_encode([
        "status"  => "success",
        "message" => $promptMsg
    ]);

} catch (Exception $e) {
    // ❌ Rollback on failure
    $conn->rollback();

    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
