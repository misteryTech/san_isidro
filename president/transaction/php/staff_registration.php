<?php
include('../../../database/connection.php');
header('Content-Type: application/json');

try {
    $required = ['first_name', 'middle_name', 'last_name', 'osca_id', 'position', 'password'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Required
    $first_name  = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name   = $_POST['last_name'];
    $osca_id     = $_POST['osca_id'];
    $position    = $_POST['position'];
    $password    = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Optional
    $chapter          = $_POST['chapter'] ?? null;
    $birth_date       = $_POST['birth_date'] ?? null;
    $civil_status     = $_POST['civil_status'] ?? null;
    $place_birth      = $_POST['place_birth'] ?? null;
    $pensioner        = $_POST['pensioner'] ?? null;
    $pension_details  = $_POST['pension_details'] ?? null;
    $date_registration= $_POST['date_registration'] ?? date('Y-m-d H:i:s');
    $email            = $_POST['email'] ?? null;
    $account          = $_POST['account'] ?? 'Regular';
    $status           = $_POST['status'] ?? 'Active';

    // CHECK DUPLICATE OSCA ID
    $check = $conn->prepare("SELECT id FROM user_table WHERE osca_id = ?");
    $check->bind_param("s", $osca_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        throw new Exception("OSCA ID already exists.");
    }

    // INSERT
    $stmt = $conn->prepare("
        INSERT INTO user_table (
            chapter, osca_id, first_name, middle_name, last_name, birth_date,
            civil_status, place_birth, pensioner, pension_details,
            date_registration, position, password, email, account, status
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $stmt->bind_param(
        "ssssssssssssssss",
        $chapter, $osca_id, $first_name, $middle_name, $last_name,
        $birth_date, $civil_status, $place_birth, $pensioner, $pension_details,
        $date_registration, $position, $password, $email, $account, $status
    );

    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'User registered successfully.'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
