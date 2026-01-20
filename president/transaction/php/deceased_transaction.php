<?php
session_start();
include('../../../database/connection.php');
header('Content-Type: application/json');

$deceased_id  = $_POST['id'] ?? null;
$remarks      = $_POST['remarks'] ?? '';
$action       = $_POST['action'] ?? null; // accept | decline
$transact_by  = $_SESSION['user_id'] ?? null;

if (!$deceased_id || !$transact_by) {
    echo json_encode(["status"=>"error","message"=>"Missing required data"]);
    exit;
}

try {
    $conn->begin_transaction();

    // Get osca_id
    $stmt = $conn->prepare("SELECT osca_id FROM deceased_benefit_applications WHERE id = ?");
    $stmt->bind_param("i", $deceased_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $osca_id = $result['osca_id'] ?? null;

    // Check if registered
    $isRegistered = false;
    if ($osca_id) {
        $check = $conn->prepare("SELECT osca_id FROM user_table WHERE osca_id = ? LIMIT 1");
        $check->bind_param("s", $osca_id);
        $check->execute();
        $isRegistered = $check->get_result()->num_rows > 0;
    }

    // If OSCA ID not registered, reject automatically
    if (!$isRegistered) {
        $newStatus = 'Rejected';
        $remarks .= ' — OSCA ID is not registered';
        $promptMsg = 'Application rejected silently: OSCA ID not registered.';
    } else {
        // Handle normal accept/decline action
        if ($action === 'accept') {
            $newStatus = 'Approved';
            $promptMsg = 'Application approved successfully.';
        } else {
            $newStatus = 'Rejected';
            $promptMsg = 'Application declined successfully.';
        }
    }

    // Update application
    $stmt1 = $conn->prepare("
        UPDATE deceased_benefit_applications
        SET status = ?, remarks = ?, updated_by = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt1->bind_param("ssii", $newStatus, $remarks, $transact_by, $deceased_id);
    $stmt1->execute();


      $stmt2 = $conn->prepare("
        UPDATE user_table
        SET status = 'Deceased'
        WHERE osca_id = ?
    ");
    $stmt2->bind_param("s",$osca_id);
    $stmt2->execute();




    $conn->commit();

    echo json_encode([
        "status" => "success",
        "message" => $promptMsg
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
}
?>
