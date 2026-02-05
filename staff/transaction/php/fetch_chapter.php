<?php
include('../../../database/connection.php');

header('Content-Type: application/json; charset=utf-8');

// Optional: stop PHP notices from breaking JSON
mysqli_set_charset($conn, "utf8mb4");

$query = "
    SELECT id, chapter_code, chapter_name
    FROM chapters
    ORDER BY chapter_name ASC
";

$result = mysqli_query($conn, $query);

if ($result === false) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to fetch chapters'
    ]);
    exit;
}

$chapters = [];

while ($row = mysqli_fetch_assoc($result)) {
    $chapters[] = [
        'id' => $row['id'],
        'chapter_code' => $row['chapter_code'],
        'chapter_name' => $row['chapter_name']
    ];
}

echo json_encode($chapters);
exit;
