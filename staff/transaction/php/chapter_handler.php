<?php
include('../../../database/connection.php');
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

/* CREATE + UPDATE */
if ($action === 'save') {
    $id   = $_POST['id'] ?? '';
    $code = trim($_POST['chapter_code']);
    $name = trim($_POST['chapter_name']);

    if ($code === '' || $name === '') {
        echo json_encode(['status'=>'error','message'=>'All fields required']);
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("
            UPDATE chapters SET chapter_code=?, chapter_name=? WHERE id=?
        ");
        $stmt->bind_param("ssi", $code, $name, $id);
        $stmt->execute();

        echo json_encode(['status'=>'success','message'=>'Chapter updated']);
    } else {
        // CREATE
        $stmt = $conn->prepare("
            INSERT INTO chapters (chapter_code, chapter_name)
            VALUES (?, ?)
        ");
        $stmt->bind_param("ss", $code, $name);
        $stmt->execute();

        echo json_encode(['status'=>'success','message'=>'Chapter added']);
    }
}

/* DELETE */
if ($action === 'delete') {
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM chapters WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo json_encode(['status'=>'success','message'=>'Chapter deleted']);
}
