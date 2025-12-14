<?php
session_start();
include('../../../database/connection.php');
header('Content-Type: application/json');

$deceased_id  = $_POST['id'] ?? null;
$remarks      = $_POST['remarks'] ?? '';
$action       = $_POST['action'] ?? null; // accept | decline
$transact_by  = $_SESSION['user_id'] ?? null;

if (!$deceased_id) {
    echo json_encode(["status"=>"error","message"=>"deceased_id missing"]);
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

    /* 1️⃣ Determine new status */
    if ($action === 'accept') {
        $newStatus = 'Approved';
        $promptMsg = 'Membership successfully verified.';
    } elseif ($action === 'decline') {
        $newStatus = 'Rejected';
        $promptMsg = 'Membership has been declined.';
    } else {
        throw new Exception('Invalid action.');
    }

    /* 2️⃣ Update application record */
    $stmt1 = $conn->prepare("
        UPDATE deceased_benefit_applications
        SET status = ?, remarks = ?, updated_by = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt1->bind_param("ssii", $newStatus, $remarks, $transact_by, $deceased_id);

    if (!$stmt1->execute()) {
        throw new Exception($stmt1->error);
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