<?php
$filename = 'templates.json';

if (file_exists($filename)) {
    $jsonData = file_get_contents($filename);
    $templates = json_decode($jsonData, true);
    echo json_encode($templates);
} else {
    echo json_encode([]);
}
?>
 