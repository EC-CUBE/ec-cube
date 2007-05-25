<?php

$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/define.php");

require_once($include_dir . HTML2DATA_DIR. "lib/gdthumb.php");
require_once($include_dir . HTML2DATA_DIR. "conf/conf.php");	

$objThumb = new gdthumb();

$file = $_GET["image"];
if(file_exists($file)){
	$objThumb->Main($file, $_GET["width"], $_GET["height"], "", true);
}else{
	$objThumb->Main(NO_IMAGE_DIR, $_GET["width"], $_GET["height"], "", true);
}

?>
