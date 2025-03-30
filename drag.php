<?php
$uploadDir = './posts/images/'; // Directory where files will be stored

// Ensure upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle file upload
if ($_FILES['files']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['files']['tmp_name'];
    $filename = basename($_FILES['files']['name']);
    $uploadPath = $uploadDir . $filename;

    // Move the uploaded file to the new location
    if (move_uploaded_file($tmp_name, $uploadPath)) {
        echo json_encode(['status' => 'success', 'url' => $uploadPath]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'File upload failed.']);
}
?>
