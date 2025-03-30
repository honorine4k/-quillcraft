<?php
session_start();
$uname = $_SESSION['user'];
$uploadDir = `./posts/{$uname}/images/`; // Make sure this directory exists and is writable
$response = [];

if ($_FILES['file']) {
    $originalName = $_FILES['file']['name'];
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $generatedName = md5($_FILES['file']['tmp_name']) . '.' . $extension;
    $filePath = $uploadDir . $generatedName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
        $response['filePath'] = $filePath;
    } else {
        $response['error'] = 'File could not be uploaded.';
    }
} else {
    $response['error'] = 'No file uploaded.';
}

echo json_encode($response);
?>
