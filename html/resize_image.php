<?php

$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/../data/class/SC_Image.php");

$objImage = new SC_Image(IMAGE_TEMP_DIR);

$file = $_GET["image"];
if(file_exists($file)){
	$objImage->saveResizeImage($file, $_GET["width"], $_GET["height"], true);
}


?>
