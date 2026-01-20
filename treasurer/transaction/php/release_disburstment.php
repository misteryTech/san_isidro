<?php
session_start();
require_once __DIR__ . "/../../../database/connection.php";

/*
|--------------------------------------------------------------------------
| VALIDATE REQUEST
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method');
}

if (empty($_POST['disbursement_id'])) {
    die('Disbursement ID is required');
}

$disbursement_id = (int) $_POST['disbursement_id'];
$released_by     = 'Treasurer'; // fixed value as requested

/*
|--------------------------------------------------------------------------
| CHECK IF ALREADY RELEASED
|--------------------------------------------------------------------------
*/
$checkStmt = $conn->prepare("
    SELECT released_by
    FROM disbursements
    WHERE id = ?
    LIMIT 1
");
$checkStmt->bind_param("i", $disbursement_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    die('Disbursement not found');
}

$row = $checkResult->fetch_assoc();

if ((int)$row['released'] === 1) {
    $_SESSION['error'] = 'Disbursement already released.';
    header('Location: disbursement_list.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE DISBURSEMENT
|--------------------------------------------------------------------------
*/
$updateStmt = $conn->prepare("
    UPDATE disbursements
    SET
        released = 1,
        released_by = ?,
        released_date = NOW()
    WHERE id = ?
");

$updateStmt->bind_param("si", $released_by, $disbursement_id);

if ($updateStmt->execute()) {
    $_SESSION['success'] = 'Disbursement successfully released.';
} else {
    $_SESSION['error'] = 'Failed to release disbursement.';
}

/*
|--------------------------------------------------------------------------
| REDIRECT BACK
|--------------------------------------------------------------------------
*/
header('Location: ../../confirm_payment.php');
exit;
