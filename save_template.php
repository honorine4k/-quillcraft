<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $templateData = $_POST['template'];
    $styleData = $_POST['style'];

    $id = uniqid(); // Generate a unique ID
    $dirPath = "./templates/{$id}/";
    if (!file_exists($dirPath)) {
        mkdir($dirPath, 0777, true);
    }

    file_put_contents("{$dirPath}template.json", json_encode(['html' => $templateData, 'css' => $styleData, 'description' => $description]));

    if (isset($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
        move_uploaded_file($_FILES['image']['tmp_name'], "{$dirPath}image.jpg");
    }

    echo json_encode(['status' => 'success', 'id' => $id]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
