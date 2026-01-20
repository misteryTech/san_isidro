<?php
session_start();
include('../../../database/connection.php');
header('Content-Type: application/json');

$membership_id = $_POST['membership_id'] ?? null;
$remarks       = $_POST['remarks'] ?? '';
$action        = $_POST['action'] ?? null; // accept | decline
$transact_by   = $_SESSION['user_id'] ?? null;

if (!$membership_id || !$action || !$transact_by) {
    echo json_encode([
        "status"  => "error",
        "message" => "Missing required parameters"
    ]);
    exit;
}

try {
    // 🚀 Start transaction
    $conn->begin_transaction();

    /* 1️⃣ Get OSCA ID from membership_table */
    $stmtFetch = $conn->prepare("
        SELECT osca_id
        FROM membership_table
        WHERE id = ?
        FOR UPDATE
    ");
    $stmtFetch->bind_param("i", $membership_id);
    $stmtFetch->execute();
    $result = $stmtFetch->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Membership record not found.");
    }

    $row     = $result->fetch_assoc();
    $osca_id = $row['osca_id'];

    /* 2️⃣ Insert transaction log */
    $stmt1 = $conn->prepare("
        INSERT INTO membership_transaction
        (membership_id, remarks, transact_by, action, updated_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt1->bind_param("isss", $membership_id, $remarks, $transact_by, $action);

    if (!$stmt1->execute()) {
        throw new Exception($stmt1->error);
    }

    /* 3️⃣ Determine new membership status */
    if ($action === 'accept') {
        $newStatus = 'Verified';
        $promptMsg = 'Membership successfully verified.';
    } elseif ($action === 'decline') {
        $newStatus = 'Declined';
        $promptMsg = 'Membership has been declined.';
    } else {
        throw new Exception('Invalid action.');
    }

    /* 4️⃣ Update membership_table */
    $stmt2 = $conn->prepare("
        UPDATE membership_table
        SET status = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt2->bind_param("si", $newStatus, $membership_id);

    if (!$stmt2->execute()) {
        throw new Exception($stmt2->error);
    }

    /* 5️⃣ ✅ If accepted, update user_table to Regular */
    if ($action === 'accept') {
        $stmt3 = $conn->prepare("
            UPDATE user_table
            SET account = 'Regular'
            WHERE osca_id = ?
        ");
        $stmt3->bind_param("s", $osca_id);

        if (!$stmt3->execute()) {
            throw new Exception($stmt3->error);
        }
    }

    // ✅ Commit everything
    $conn->commit();

    echo json_encode([
        "status"  => "success",
        "message" => $promptMsg
    ]);

} catch (Exception $e) {
    // ❌ Rollback on error
    $conn->rollback();

    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
