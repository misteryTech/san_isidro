<?php
include('../../../database/connection.php');
header('Content-Type: application/json');

// Helper function
function response($status, $message) {
    echo json_encode(["status" => $status, "message" => $message]);
    exit;
}

// Collect form data
$osca_id         = trim($_POST['osca_id'] ?? '');
$first_name      = trim($_POST['first_name'] ?? '');
$middle_name     = trim($_POST['middle_name'] ?? '');
$last_name       = trim($_POST['last_name'] ?? '');
$birth_date      = $_POST['birth_date'] ?? '';
$place_birth     = trim($_POST['place_birth'] ?? '');
$civil_status    = $_POST['civil_status'] ?? '';
$pensioner       = $_POST['pensioner'] ?? '';
$pension_details = trim($_POST['pension_details'] ?? '');
$mobileno           = trim($_POST['mobileno'] ?? '');
$password        = $_POST['password'] ?? '';
$chapter         = trim($_POST['chapter'] ?? '');
$position        = "member";
$date_added      = date("Y-m-d H:i:s");

// Basic validation
if (empty($osca_id) || empty($first_name) || empty($last_name) || empty($mobileno) || empty($password)) {
    response("error", "Please fill in all required fields.");
}

// Check duplicate email or OSCA ID
$check = $conn->prepare("SELECT id FROM user_table WHERE mobileno = ? OR osca_id = ?");
$check->bind_param("ss", $mobileno, $osca_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    response("error", "Mobile Number or OSCA ID is already registered.");
}
$check->close();

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("
    INSERT INTO user_table
    (osca_id, first_name, middle_name, last_name, birth_date, place_birth,
     civil_status, pensioner, pension_details, mobileno, password, chapter,
     position, date_registration)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssssssssssssss",
    $osca_id,
    $first_name,
    $middle_name,
    $last_name,
    $birth_date,
    $place_birth,
    $civil_status,
    $pensioner,
    $pension_details,
    $mobileno,
    $hashedPassword,
    $chapter,
    $position,
    $date_added
);

if ($stmt->execute()) {
    response("success", "Registration successful!");
} else {
    response("error", "Registration failed. Please try again.");
}

$stmt->close();
$conn->close();
?>
