<?php

require_once("../../require.php");

$objView = new SC_UserView("./templates/");
$objImage = new SC_Image(IMAGE_TEMP_DIR);


$file = IMAGE_TEMP_DIR . "kaki.jpg";
$path = $objImage->saveResizeImage($file, 2);

sfprintr($path);

?>
