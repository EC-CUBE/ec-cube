<?php

require_once("../../require.php");

$objView = new SC_UserView("./templates/");
$objImage = new SC_Image(IMAGE_TEMP_DIR);


$file = IMAGE_TEMP_DIR . "kaki.png";

sfprintr(pathinfo($file));
$path = $objImage->saveResizeImage($file, 0.5);

sfprintr($path);

?>
