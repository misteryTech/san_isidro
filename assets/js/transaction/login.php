<?php
include('../../../database/connection.php');

// Collect form data
$osca_id  = $_POST['osca_id'] ?? '';
$password = $_POST['password'] ?? '';

// Prepare query to fetch user by OSCA ID
$stmt = $conn->prepare("SELECT * FROM user_table WHERE osca_id = ?");
$stmt->bind_param("s", $osca_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Check if status is deceased
    if (strtolower($user['status']) === 'deceased') {
        echo json_encode([
            "status"  => "error",
            "message" => "This account is marked as deceased. Login not allowed."
        ]);
        exit;
    }elseif (strtolower($user['status']) === 'inactive') {
        echo json_encode([
            "status"  => "error",
            "message" => "This account is inactive. Please contact support."
        ]);
        exit;
    }

    // Verify password
    if (password_verify($password, $user['password'])) {
        // Success: start session
        session_start();
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['osca_id']    = $user['osca_id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name']  = $user['last_name'];
        $_SESSION['position']   = $user['position'];
        $_SESSION['chapter']    = $user['chapter'];
        $_SESSION['account']    = $user['account'];
        $_SESSION['date_registration']    = $user['date_registration'];

        echo json_encode([
            "status"   => "success",
            "message"  => "Login successful!",
            "position" => strtolower(trim($user['position']))
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
