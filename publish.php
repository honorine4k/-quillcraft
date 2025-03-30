<?php

// This assumes you receive the data as JSON
$content = json_decode(file_get_contents("php://input"), true);

$html = $content['html'] ?? '';
$css = $content['css'] ?? '';

// Define paths
$htmlFilePath = "./edits/index.html";
$cssFilePath = "./edits/styles.css";

// Write HTML file
file_put_contents($htmlFilePath, $html);
// Write CSS file
file_put_contents($cssFilePath, $css);

// Response back to the client
echo json_encode(['message' => 'Files have been published successfully!']);
?>