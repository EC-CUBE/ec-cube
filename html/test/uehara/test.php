<?php
function sfCutStringByte($str, $len) {
	if(strlen($str) > $len) {
		$ret = $str;
		for($i = 1; $len >= $i; $i++) {
			$ret = mb_substr($ret, 0, $len - $i);
			if(strlen($ret) <= $len) break;
		}
	} else {
		$ret = $str;
	}

	return $ret;
}

$str = "ああああああああああああああああああああああああああああああああああああああああああああああああああ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;ああああああああああああああああああああああああああああああああああああああああああ";

echo sfCutStringByte($str, 100);
?>