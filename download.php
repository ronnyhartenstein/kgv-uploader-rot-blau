<?php
$uploadDir = __DIR__ . '/uploads';
$filename = basename($_GET['file'] ?? '');
$original = basename($_GET['orig'] ?? 'download.zip');
$filePath = "$uploadDir/$filename";

if (file_exists($filePath)) {
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $original . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
}
http_response_code(404);
echo "Datei nicht gefunden.";