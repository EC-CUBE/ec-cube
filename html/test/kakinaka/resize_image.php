<?php

require_once("../../require.php");

$objView = new SC_UserView("./templates/");
$objImage = new SC_Image(IMAGE_TEMP_DIR);


$file = IMAGE_TEMP_DIR . "kaki.jpg";

//sfprintr(pathinfo($file));
$path = $objImage->saveResizeImage($file, 1000, 1000, true);

//sfprintr($path);

?>
