<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
// 円の中心点と直径から弧の終端座標を算出する。
/*
	$cx	: 中心点X座標
	$cy	: 中心点Y座標
	$r	: 半径
	$e	: 角度
*/
function lfGetArcPos($cx, $cy, $cw, $ch, $e) {
	// 三角関数用の角度を求める
	$s = 90 - $e;
	$r = $cw / 2;
	// 位置を求める
	$x = $cx + ($r * cos(deg2rad($s)));
	$y = $cy - (($r * sin(deg2rad($s))) * ($ch / $cw));		
	return array(round($x), round($y));
}

/* 画像にテキストを描画する */
function lfImageText($dst_image, $text, $font_size, $left, $top, $font, $arrRGB) {
	$color = ImageColorAllocate($dst_image, $arrRGB[0], $arrRGB[1], $arrRGB[2]);
	$text = mb_convert_encoding($text, "UTF-8", CHAR_CODE);
	// 表示角度	
	$angle = 0;
	// テキスト描画
	ImageTTFText($dst_image, $font_size, $angle, $left, $top, $color, $font, $text);
}

// 表示色の取得
function lfGetImageColor($image, $array) {
	if(count($array) != 3) {
		return NULL;
	}
	$ret = imagecolorallocate($image, $array[0], $array[1], $array[2]);
	return $ret;
}

// 影用表示色の取得
function lfGetImageDarkColor($image, $array) {
	if(count($array) != 3) {
		return NULL;
	}
	$i = 0;
	foreach($array as $val) {
		$dark[$i] = $val - 45;
		if($dark[$i] < 0) {
			$dark[$i] = 0;
		}
		$i++;
	}
	$ret = imagecolorallocate($image, $dark[0], $dark[1], $dark[2]);
	return $ret;
}
?>