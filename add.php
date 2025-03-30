<?php
$folderPath = './posts/images';
$images = array_diff(scandir($folderPath), array('..', '.'));
echo json_encode($images);
?>
