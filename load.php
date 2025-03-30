<?php
$uploadDir = './posts/images/'; // Target directory
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); // Create directory if it does not exist
}

$response = [];

if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $filename = basename($file['name']);
    $uploadPath = $uploadDir . uniqid() . '_' . $filename; // Generate unique file name

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $response['asset'] = [
            'src' => $uploadPath,
            'name' => $filename,
            'type' => 'image'
        ];
    } else {
        $response['error'] = 'Failed to upload image.';
    }
} else {
    $response['error'] = 'No file uploaded.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
