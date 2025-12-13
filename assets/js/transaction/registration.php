<?php
include('../../../database/connection.php');

// Collect form data
$osca_id        = $_POST['osca_id'] ?? '';
$first_name     = $_POST['first_name'] ?? '';
$last_name      = $_POST['last_name'] ?? '';
$birth_date     = $_POST['birth_date'] ?? '';
$place_birth    = $_POST['place_birth'] ?? '';
$civil_status   = $_POST['civil_status'] ?? '';
$pensioner      = $_POST['pensioner'] ?? '';
$pension_details= $_POST['pension_details'] ?? '';
$email          = $_POST['email'] ?? '';
$password       = $_POST['password'] ?? '';
$chapter        = $_POST['chapter'] ?? '';
$position       = "member";
$date_added     = date("Y-m-d H:i:s"); // define timestamp

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepare insert
$stmt = $conn->prepare("
    INSERT INTO user_table
    (osca_id, first_name, last_name, birth_date, place_birth, civil_status, pensioner, pension_details, email, password, chapter, position, date_registration)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssssssssssss",
    $osca_id,
    $first_name,
    $last_name,
    $birth_date,
    $place_birth,
    $civil_status,
    $pensioner,
    $pension_details,
    $email,
    $hashedPassword,
    $chapter,
    $position,
    $date_added
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registration successful!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>