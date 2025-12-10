<?php
include('../../../database/connection.php');

// Collect form data
$osca_id  = $_POST['osca_id'] ?? '';
$password = $_POST['password'] ?? '';

// Prepare query to fetch user by OSCA ID
$stmt = $conn->prepare("SELECT id, osca_id, password, first_name, last_name, position FROM user_table WHERE osca_id = ?");
$stmt->bind_param("s", $osca_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {
        // Success: you can start a session here
        session_start();
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['osca_id']   = $user['osca_id'];
        $_SESSION['first_name']= $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['position']  = $user['position'];

        echo json_encode([
            "status"   => "success",
            "message"  => "Login successful!",
            "position" => $user['position']
        ]);

    } else {
        echo json_encode(["status" => "error", "message" => "Invalid password."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No account found with that OSCA ID."]);
}

$stmt->close();
$conn->close();
?>