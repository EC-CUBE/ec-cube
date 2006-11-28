<?php

$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/../data/lib/gdthumb.php");
require_once($include_dir . "/../data/conf/conf.php");	

$objThumb = new gdthumb();

$file = $_GET["image"];
if(file_exists($file)){
	$objThumb->Main($file, $_GET["width"], $_GET["height"], "", true);
//	$objImage->saveResizeImage($file, $_GET["width"], $_GET["height"], true);
}else{
//	$objImage->saveResizeImage(NO_IMAGE_DIR, $_GET["width"], $_GET["height"], true);
	$objThumb->Main(NO_IMAGE_DIR, $_GET["width"], $_GET["height"], "", true);
}

?>
