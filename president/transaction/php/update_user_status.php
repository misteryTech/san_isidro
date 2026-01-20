<?php
include('../../../database/connection.php');

// Get JSON POST data
$data = json_decode(file_get_contents('php://input'), true);

$osca_id = $data['osca_id'] ?? '';
$status  = $data['status'] ?? '';

header('Content-Type: application/json');

if (!$osca_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

// Update user status
$stmt = $conn->prepare("UPDATE user_table SET status = ? WHERE osca_id = ?");
$stmt->bind_param("ss", $status, $osca_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
