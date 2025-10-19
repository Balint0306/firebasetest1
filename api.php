<?php
header('Content-Type: application/json');

$resource = $_GET['resource'] ?? '';

$filePath = '';

if ($resource === 'songs') {
    $filePath = __DIR__ . '/data/songs.json';
} elseif ($resource === 'playlists') {
    $filePath = __DIR__ . '/data/playlists.json';
}

if (file_exists($filePath)) {
    readfile($filePath);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Resource not found']);
}
