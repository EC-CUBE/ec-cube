<?php

$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/../data/class/SC_Image.php");

$objImage = new SC_Image(IMAGE_TEMP_DIR);

$file = $_GET["image"];
if(file_exists($file)){
	$objImage->saveResizeImage($file, $_GET["width"], $_GET["height"], true);
}else{
	header("Content-Type: image/gif");
	$image = ImageCreateFromGIF(NO_IMAGE_URL);
	imagecopyresampled($dst_im, $src_im, 0, 0, 0,0, $zip_width, $zip_height, $from_w, $from_h);
	Imagegif($image);
	ImageDestroy($image);
	ImageGIF($image);
	ImageDestroy($image);
}

?>
