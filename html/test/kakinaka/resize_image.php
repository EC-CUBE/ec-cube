<?php

require_once("../../require.php");

$objView = new SC_UserView("./templates/");
$objImage = new SC_Image(IMAGE_TEMP_DIR);


$file = "/html/upload/temp_image/kaki.jpg";
$path = $objImage->saveResizeImage($file, 1);

sfprintr($path);

?>
