<?php
session_start();
require_once '../../../database/connection.php';

header('Content-Type: application/json');

$password = trim($_POST['password'] ?? '');
$user_id  = $_SESSION['user_id'] ?? null;

$osca_id = $_POST['osca_id'] ?? null;
$prr_id  = $_POST['prr_id'] ?? null;
$dba_id  = $_POST['dba_id'] ?? null;

if (!$user_id || !$osca_id || !$prr_id || !$dba_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$stmt = $conn->prepare("SELECT password FROM user_table WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {

    // 🔒 Lock IDs to session (cannot be tampered)
    $_SESSION['password_verified'] = true;
    $_SESSION['approve_ids'] = [
        'osca_id' => (string)$osca_id,
        'prr_id'  => (int)$prr_id,
        'dba_id'  => (int)$dba_id,
    ];

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Incorrect password']);
}

exit;
