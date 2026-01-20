<?php
include('../../../database/connection.php');
header('Content-Type: application/json');

function respond($status, $message) {
    echo json_encode([
        "status" => $status,
        "message" => $message
    ]);
    exit;
}

// Collect form data
$osca_id         = trim($_POST['osca_id'] ?? '');
$cp_firstname     = trim($_POST['cp_firstname'] ?? '');
$cp_lastname     = trim($_POST['cp_lastname'] ?? '');
$cp_relationship = trim($_POST['cp_relationship'] ?? '');
$cp_contact      = trim($_POST['cp_contact'] ?? '');
$cp_email        = trim($_POST['cp_email'] ?? '');
$occupation      = trim($_POST['occupation'] ?? '');
$date_added      = date('Y-m-d H:i:s');

// Basic validation
if (empty($osca_id) || empty($cp_firstname) || empty($cp_lastname) || empty($cp_relationship) || empty($cp_contact)) {
    respond("error", "Required fields are missing.");
}

// 🔒 Check if membership already exists
$check = $conn->prepare(
    "SELECT id FROM membership_table WHERE osca_id = ? LIMIT 1"
);
$check->bind_param("s", $osca_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    respond("error", "This OSCA ID already has a membership record.");
}
$check->close();

// Insert membership
$stmt = $conn->prepare("
    INSERT INTO membership_table
    (osca_id, cp_firstname, cp_lastname, cp_relationship, cp_contact, cp_email, cp_occupation, date_added)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssssssss",
    $osca_id,
    $cp_firstname,
    $cp_lastname,
    $cp_relationship,
    $cp_contact,
    $cp_email,
    $occupation,
    $date_added
);

if ($stmt->execute()) {
    respond("success", "Membership details saved successfully!");
} else {
    respond("error", "Unable to save membership. Please try again.");
}

$stmt->close();
$conn->close();
