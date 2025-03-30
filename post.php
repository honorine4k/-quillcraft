<?php
session_start();
$uname = $_SESSION['user'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $html = $_POST['html'];
    $css = $_POST['css'];
   
    $id = uniqid('template_', true); // Generate a unique ID
    // Create a directory for the post
    $dirPath = "./posts/{$uname}/{$id}/";
    if (!is_dir($dirPath)) {
        mkdir($dirPath, 0777, true);
    }

    file_put_contents("{$dirPath}index.php", $html);
    file_put_contents("{$dirPath}styles.css", $css);
    $data = json_encode(['title' => $title, 'description' => $description, 'category' => $category,'Author' => $uname]);
    file_put_contents("{$dirPath}data.json", $data);

    // Specify the source file and the destination where the file will be copied
    $source = "./posts/recieve.php";
    $destination = "{$dirPath}/php/";

    // Create the destination directory if it does not exist
    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }
    $destination_path = "{$destination}/recieve.php";
    // Check if the source file exists
    if (!file_exists($source)) {
        die('Error: Source file does not exist.');
    }
    else{
        copy($source, $destination_path);
    }

    
    // Handle image upload
    if (!empty($_FILES['image']['tmp_name'])) {
        move_uploaded_file($_FILES['image']['tmp_name'], "{$dirPath}image.jpg");
    }

    echo json_encode(['status' => 'success', 'id' => $id]);
    exit;
}
?>
