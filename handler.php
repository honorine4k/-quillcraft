<?php
$uploadDir = './posts/images/'; // Directory where files will be stored
$jsonFile = './assets.json'; // JSON file to store asset data

// Ensure upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$response = ['assets' => []];

// Handle each file uploaded
foreach ($_FILES['files']['name'] as $key => $name) {
    $tmp_name = $_FILES['files']['tmp_name'][$key];
    $filename = basename($name);
    $uploadPath = $uploadDir . $filename;

    // Move the uploaded file to the new location
    if (move_uploaded_file($tmp_name, $uploadPath)) {
        $response['assets'][] = [
            'type'=>'image',
            'src' => $uploadDir.$filename,
            'name' => $filename
            
        ];
    } else {
        $response['error'] = "Failed to move uploaded file.";
    }
}

// Save or update the JSON file with new asset data
$currentData = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
$newData = array_merge($currentData, $response['assets']);
file_put_contents($jsonFile, json_encode($newData, JSON_PRETTY_PRINT));

echo json_encode($response);
?>



