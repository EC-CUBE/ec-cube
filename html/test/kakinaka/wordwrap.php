<?php

//$str = "ああああああああああああああああああああああああああああああああああああああああああああああああああああああああinjyuあああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△injyuあああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ";
$str = "aあ";

//$Message_tmp = wordwrap($str,2,"<br>", 1);

//$Message_tmp = mbsplit();

print($Message_tmp);


$str = array();
$test = "abあcdか1さ0たなはbgfまやらわ";
$cut_len = 10;

$len = mb_strlen($test);
while($len > $cut_len){
	$str[] = mb_substr($test,0,10);
	$tmp = mb_substr($test, 10, $len);
	$len = mb_strlen($test);
}

print_r($str);

?>