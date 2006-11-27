<?php

$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/../data/class/SC_Image.php");

$objImage = new SC_Image(IMAGE_TEMP_DIR);

$file = $_GET["image"];

$objImage->saveResizeImage($file, 100, 100, true);

?>
