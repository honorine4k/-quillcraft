<?php

session_start();

$user = $_SESSION['user'];
// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);
$id = uniqid();
// Check if the necessary data is present
if (!isset($data['html']) || !isset($data['css'])) {
    echo json_encode(['message' => 'Invalid data provided']);
    exit;
}

// File paths
$htmlFile = './edits/index.html';
$cssFile = './edits/styles.css';

// Write HTML and CSS to files
file_put_contents($htmlFile, $data['html']);
file_put_contents($cssFile, $data['css']);

// Response back to client
echo json_encode(['message' => 'Files saved successfully']);
?>
