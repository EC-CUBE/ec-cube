<?php

$str = "ああああああああああああああああああああああああああああああああああああああああああああああああああああああああinjyuあああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△injyuあああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ";
//$str = "ああああああああああああああああああああああああああああああああああああああああああああああああああああああああinjyuあああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ△△△";
//$str = "aあ";
print $str;

//$Message_tmp = wordwrap($str,2,"<br>", 1);

//$Message_tmp = mbsplit();

print($Message_tmp);

$test = $str;// "abあcdか1さ0たなはbgfまやらわ";

$str = array();
$cut_len = 10;

$len = mb_strlen($test);
while($len > $cut_len){
	$str[] = mb_substr($test,0,$cut_len);
	$tmp = mb_substr($test, $cut_len, $len);
	$len = mb_strlen($tmp);
	print_r($str);
	MyFlush();
}
$str[] = $tmp;
print_r($str);

function MyFlush() {
	flush();
	ob_end_flush();
	ob_start();
	sleep(1);
}

?>