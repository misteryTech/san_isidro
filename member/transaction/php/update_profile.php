<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Collect POST data
$osca_id       = trim($_POST['osca_id'] ?? '');
$chapter       = trim($_POST['chapter'] ?? '');
$first_name    = trim($_POST['first_name'] ?? '');
$last_name     = trim($_POST['last_name'] ?? '');
$birth_date    = trim($_POST['birth_date'] ?? '');
$place_birth   = trim($_POST['place_birth'] ?? '');
$civil_status  = trim($_POST['civil_status'] ?? '');
$pensioner     = trim($_POST['pensioner'] ?? '');
$pension_details = trim($_POST['pension_details'] ?? '');
$member_account  = trim($_POST['member_account'] ?? '');

// Contact person fields (only used if Associate)
$cp_fullname     = trim($_POST['cp_fullname'] ?? '');
$cp_relationship = trim($_POST['cp_relationship'] ?? '');
$cp_contact      = trim($_POST['cp_contact'] ?? '');
$cp_email        = trim($_POST['cp_email'] ?? '');
$cp_occupation   = trim($_POST['cp_occupation'] ?? '');

// Validate required fields
if (empty($osca_id) || empty($first_name) || empty($last_name)) {
    echo json_encode(['status' => 'error', 'message' => 'Required fields missing']);
    exit;
}

// -----------------------------------------------
// CHECK if osca_id exists in membership_table
// -----------------------------------------------
$checkStmt = $conn->prepare("SELECT osca_id FROM membership_table WHERE osca_id = ? LIMIT 1");
if (!$checkStmt) {
    echo json_encode(['status' => 'error', 'message' => 'Check prepare failed: ' . $conn->error]);
    exit;
}
$checkStmt->bind_param("s", $osca_id);
$checkStmt->execute();
$checkStmt->store_result();

$existsInMembership = $checkStmt->num_rows > 0;
$checkStmt->close();

// -----------------------------------------------
// UPDATE user_table (always, if osca_id is valid)
// -----------------------------------------------
$stmt = $conn->prepare("UPDATE user_table
    SET chapter=?, first_name=?, last_name=?, birth_date=?, place_birth=?,
        civil_status=?, pensioner=?, pension_details=?
    WHERE osca_id=?");

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param(
    "sssssssss",
    $chapter,
    $first_name,
    $last_name,
    $birth_date,
    $place_birth,
    $civil_status,
    $pensioner,
    $pension_details,
    $osca_id
);

if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'User update failed: ' . $stmt->error]);
    $stmt->close();
    exit;
}
$stmt->close();

// -----------------------------------------------
// UPDATE membership_table ONLY if:
// - member is Associate
// - osca_id exists in membership_table
// -----------------------------------------------
if ($member_account === "Associate" && $existsInMembership) {

    // Split cp_fullname into first and last name
    $nameParts  = explode(' ', $cp_fullname, 2);
    $cp_fname   = $nameParts[0] ?? '';
    $cp_lname   = $nameParts[1] ?? '';

    $mStmt = $conn->prepare("UPDATE membership_table
        SET cp_firstname=?, cp_lastname=?, cp_relationship=?,
            cp_contact=?, cp_email=?, cp_occupation=?
        WHERE osca_id=?");

    if (!$mStmt) {
        echo json_encode(['status' => 'error', 'message' => 'Membership prepare failed: ' . $conn->error]);
        exit;
    }

    $mStmt->bind_param(
        "sssssss",
        $cp_fname,
        $cp_lname,
        $cp_relationship,
        $cp_contact,
        $cp_email,
        $cp_occupation,
        $osca_id
    );

    if (!$mStmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Membership update failed: ' . $mStmt->error]);
        $mStmt->close();
        exit;
    }
    $mStmt->close();

    echo json_encode(['status' => 'success', 'message' => 'Profile and contact information updated successfully!']);

} elseif ($member_account === "Associate" && !$existsInMembership) {
    // Associate but no membership record found — update user only, warn user
    echo json_encode([
        'status' => 'success',
        'message' => 'Profile updated. Contact info was not updated — no membership record found.'
    ]);

} else {
    // Regular member, no contact info to update
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully!']);
}

$conn->close();
exit;