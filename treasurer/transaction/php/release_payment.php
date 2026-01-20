<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require_once __DIR__ . "/../../../database/connection.php";

// Only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

// Validate session
$requestedBy = $_SESSION['user_id'] ?? null;
if (!$requestedBy) {
    echo json_encode(['error' => 'Unauthorized request.']);
    exit;
}

// Get inputs
$memberId = trim($_POST['osca_id'] ?? '');
$releasedMethod = trim($_POST['released_method'] ?? '');
$releaseAmount = isset($_POST['release_amount']) ? floatval($_POST['release_amount']) : 0;

// Validate inputs
if ($memberId === '') {
    echo json_encode(['error' => 'Invalid or missing OSCA ID.']);
    exit;
}

if ($releaseAmount <= 0) {
    echo json_encode(['error' => 'Invalid release amount.']);
    exit;
}

if (!in_array($releasedMethod, ['Cash', 'Bank Transfer'])) {
    echo json_encode(['error' => 'Invalid release method.']);
    exit;
}

try {
    // Prevent duplicate pending requests
    $check = $conn->prepare("
        SELECT COUNT(*) AS cnt
        FROM payment_release_requests
        WHERE osca_id = ? AND requested_by = ? AND status = 'Pending'
    ");
    $check->bind_param("si", $memberId, $requestedBy);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        echo json_encode(['error' => 'A pending release request for this member already exists.']);
        exit;
    }

    // Insert new release request
    $stmt = $conn->prepare("
        INSERT INTO payment_release_requests
        (osca_id, release_amount, released_method, requested_by, status)
        VALUES (?, ?, ?, ?, 'Pending')
    ");
    $stmt->bind_param("sdsi", $memberId, $releaseAmount, $releasedMethod, $requestedBy);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        'success' => true,
        'message' => 'Release request submitted and pending president approval.'
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'error' => 'Failed to submit request: ' . $e->getMessage()
    ]);
    exit;
}
