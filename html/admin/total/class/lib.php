<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
// �ߤ��濴����ľ�¤���̤ν�ü��ɸ�򻻽Ф��롣
/*
	$cx	: �濴��X��ɸ
	$cy	: �濴��Y��ɸ
	$r	: Ⱦ��
	$e	: ����
*/
function lfGetArcPos($cx, $cy, $cw, $ch, $e) {
	// ���Ѵؿ��Ѥγ��٤����
	$s = 90 - $e;
	$r = $cw / 2;
	// ���֤����
	$x = $cx + ($r * cos(deg2rad($s)));
	$y = $cy - (($r * sin(deg2rad($s))) * ($ch / $cw));		
	return array(round($x), round($y));
}

/* �����˥ƥ����Ȥ����褹�� */
function lfImageText($dst_image, $text, $font_size, $left, $top, $font, $arrRGB) {
	$color = ImageColorAllocate($dst_image, $arrRGB[0], $arrRGB[1], $arrRGB[2]);
	$text = mb_convert_encoding($text, "UTF-8", CHAR_CODE);
	// ɽ������	
	$angle = 0;
	// �ƥ���������
	ImageTTFText($dst_image, $font_size, $angle, $left, $top, $color, $font, $text);
}

// ɽ�����μ���
function lfGetImageColor($image, $array) {
	if(count($array) != 3) {
		return NULL;
	}
	$ret = imagecolorallocate($image, $array[0], $array[1], $array[2]);
	return $ret;
}

// ����ɽ�����μ���
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