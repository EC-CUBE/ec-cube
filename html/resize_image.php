<?php

$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/../data/class/SC_Image.php");
require_once($include_dir . "/../data/conf/conf.php");	

$objImage = new SC_Image(IMAGE_TEMP_DIR);

$file = $_GET["image"];
if(file_exists($file)){
	$objImage->saveResizeImage($file, $_GET["width"], $_GET["height"], true);
}else{
	
	$objImage->saveResizeImage(NO_IMAGE_DIR, $_GET["width"], $_GET["height"], true);
}

?>
