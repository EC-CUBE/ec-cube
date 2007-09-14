<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
/*
$SC_GRAPHPIE_DIR = realpath(dirname( __FILE__));
require_once($SC_GRAPHPIE_DIR . "/config.php");
require_once($SC_GRAPHPIE_DIR . "/lib.php");	
*/
require_once(realpath(dirname( __FILE__)) . "/config.php");
require_once(realpath(dirname( __FILE__)) . "/lib.php");	

// SC_Graph���̥��饹
class SC_GraphBase {
	var $arrRGB;
	var $arrColor;
	var $arrDarkColor;
	var $image;
	var $left;
	var $top;
	var $shade_color;
	var $flame_color;
	var $shade_on;
	var $text_color;
	var $labelbg_color;
	var $bgw;
	var $bgh;
	var $clabelbg_color;
	var $title_color;
	var $text_top;
	var $mark_color;
	var $arrLegend;
	
	// ���󥹥ȥ饯��
	function SC_GraphBase($bgw = BG_WIDTH, $bgh = BG_HEIGHT, $left, $top) {
		global $ARR_GRAPH_RGB;
		global $ARR_BG_COLOR;
		global $ARR_SHADE_COLOR;
		global $ARR_FLAME_COLOR;
		global $ARR_TEXT_COLOR;
		global $ARR_LABELBG_COLOR;
		global $ARR_LEGENDBG_COLOR;
		global $ARR_TITLE_COLOR;
		global $ARR_GRID_COLOR;
		
		// ��������
		$this->bgw = $bgw;
		$this->bgh = $bgh;	
		$this->image = imagecreatetruecolor($bgw, $bgh);
		// ����������ꥢ��ͭ��
		if (function_exists("imageantialias")) imageantialias($this->image, true);
		// �طʿ��򥻥å�
		imagefill($this->image, 0, 0, lfGetImageColor($this->image, $ARR_BG_COLOR));
		
		// ���ѿ�������
		$this->setColorList($ARR_GRAPH_RGB);
		// �����������֤�����
		$this->left = $left;
		$this->top = $top;
		$this->shade_color = lfGetImageColor($this->image, $ARR_SHADE_COLOR);
		$this->flame_color = lfGetImageColor($this->image, $ARR_FLAME_COLOR);
		$this->text_color = lfGetImageColor($this->image, $ARR_TEXT_COLOR);
		$this->labelbg_color = lfGetImageColor($this->image, $ARR_LABELBG_COLOR);
		$this->clabelbg_color = lfGetImageColor($this->image, $ARR_LEGENDBG_COLOR);
		$this->title_color = lfGetImageColor($this->image, $ARR_TITLE_COLOR);
		$this->grid_color = lfGetImageColor($this->image, $ARR_GRID_COLOR);
			
		// �Ƥ���
		$this->shade_on = true;
    }
	
	// �ꥵ��ץ�(�������餫�˽̾�����)
	function resampled() {
		$new_width = $this->bgw * 0.8;
		$new_height = $this->bgh * 0.8;		
		$tmp_image = imagecreatetruecolor($new_width, $new_height);
		if(imagecopyresampled($tmp_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->bgw, $this->bgh)) {
			$this->image = $tmp_image;
		}
	}
	
	
	// ���֥������ȥ��顼������
	function setColorList($arrRGB) {
		$this->arrRGB = $arrRGB;
		$count = count($this->arrRGB);
		// �̾￧������
		for($i = 0; $i < $count; $i++) {
			$this->arrColor[$i] = lfGetImageColor($this->image, $this->arrRGB[$i]);
		}
		// �ſ�������
		for($i = 0; $i < $count; $i++) {
			$this->arrDarkColor[$i] = lfGetImageDarkColor($this->image, $this->arrRGB[$i]);
		}		
	}
	
	// �ƤΤ���ʤ�
	function setShadeOn($shade_on) {
		$this->shade_on = $shade_on;
	}
	
	// ��������Ϥ���
	function outputGraph($header = true, $filename = "") {
		if($header) {
			header('Content-type: image/png');
		}
		
		if ($filename != "") {
			imagepng($this->image, $filename);
		}else{
			imagepng($this->image);
		}

		imagedestroy($this->image);
	}

	// ������Υƥ������������
	function getTextWidth($text, $font_size) {
		$text_len = strlen($text);
		$ret = $font_size * $text_len * TEXT_RATE;	
		/*
			�����Τ��ͤ������Ǥ��ʤ��ä��Τ��ѻ�
			// �ƥ��������μ���
			$arrPos = imagettfbbox($font_size, 0, FONT_PATH, $text);
			$ret = $arrPos[2] - $arrPos[0];
		*/
		return $ret;
	}
	
	// �ƥ����Ȥ���Ϥ���
	function setText($font_size, $left, $top, $text, $color = NULL, $angle = 0, $labelbg = false) {
		// ���ײ��˳��٤��ѹ�
		$angle = -$angle;		
		// ��٥��ط�
		if($labelbg) {
			$text_width = $this->getTextWidth($text, $font_size);
			imagefilledrectangle($this->image, $left - 2, $top - 2, $left + $text_width + 2, $top + $font_size + 2, $this->labelbg_color);
		}
		//$text = mb_convert_encoding($text, "UTF-8", CHAR_CODE);
		$text = mb_convert_encoding($text, CHAR_CODE);
		if($color != NULL) {
			ImageTTFText($this->image, $font_size, $angle, $left, $top + $font_size, $color, FONT_PATH, $text);
		} else {
			ImageTTFText($this->image, $font_size, $angle, $left, $top + $font_size, $this->text_color, FONT_PATH, $text);			
		}
	}
	
	// �����ȥ����Ϥ���
	function drawTitle($text, $font_size = TITLE_FONT_SIZE) {
		// ���ϰ��֤λ���
		$text_width = $this->getTextWidth($text, $font_size);
		$left = ($this->bgw - $text_width) / 2;
		$top = TITLE_TOP;
		$this->setText($font_size, $left, $top, $text, $this->title_color);		
	}
	
	// ������Ϥ���
	function debugPrint($text) {
		$text = mb_convert_encoding($text, "UTF-8", CHAR_CODE);
		if(!isset($this->text_top)) {
			$this->text_top = FONT_SIZE + LINE_PAD;
		}		
		// �ƥ���������
		ImageTTFText($this->image, FONT_SIZE, 0, LINE_PAD, $this->text_top, $this->text_color, FONT_PATH, $text);
		$this->text_top += FONT_SIZE + LINE_PAD;
	}
		
	// ���顼��٥������
	function drawLegend($legend_max = "", $clabelbg = true) {
		// ���㤬��Ͽ����Ƥ��ʤ�������
		if(count($this->arrLegend) <= 0) {
			return;
		}		
		
		if($legend_max != "") {
			$label_max = $legend_max;
		} else {
			$label_max = count($this->arrLegend);
		}

		$height_max = 0;
		$text_max = 0;
		$width_max = 0;
		
		// ����ʸ������¿����Τ����
		for($i = 0; $i < $label_max; $i++) {
			$text_len = strlen($this->arrLegend[$i]);
			if($text_max < $text_len) {
				$text_max = $text_len;
			}
			$height_max += FONT_SIZE + LINE_PAD;
		}
		$width_max = FONT_SIZE * $text_max * TEXT_RATE;		

		// 	���顼���������ʸ���֤�ޤ᤿��
		$width_max += FONT_SIZE + (LINE_PAD * 2);	
		$left = $this->bgw - $width_max - LEGEND_RIGHT;
		$top = LEGEND_TOP;
		// ���顼��٥��طʤ�����
		if($clabelbg) {
			$this->drawClabelBG($left - LINE_PAD, $top, $left + $width_max, $top + $height_max + LINE_PAD);
		}
		$top += LINE_PAD;
				
		// �����μ���
		$c_max = count($this->arrColor);
		for($i = 0; $i < $label_max; $i++) {			
			// ���顼���������ɽ��
			imagerectangle($this->image, $left, $top, $left + FONT_SIZE, $top + FONT_SIZE, $this->flame_color);
			imagefilledrectangle($this->image, $left + 1, $top + 1, $left + FONT_SIZE - 1, $top + FONT_SIZE - 1, $this->arrColor[($i % $c_max)]);
			// ��٥��ɽ��
			$this->setText(FONT_SIZE, $left + FONT_SIZE + LINE_PAD, $top, $this->arrLegend[$i]);
			$top += FONT_SIZE + LINE_PAD;
		}
	}
	
	// ���顼��٥��طʤ�����
	function drawClabelBG($left, $top, $right, $bottom) {
		// �Ƥ�����
		if($this->shade_on) {
			imagefilledrectangle($this->image, $left + 2, $top + 2, $right + 2, $bottom + 2, $this->shade_color);
		}
		// ���顼��٥��طʤ�����
		imagefilledrectangle($this->image, $left, $top, $right, $bottom, $this->clabelbg_color);
		imagerectangle($this->image, $left, $top, $right, $bottom, $this->flame_color);
	}
	
	// ����򥻥åȤ���
	function setLegend($arrLegend) {
		$this->arrLegend = array_values((array)$arrLegend);
	}

}

?>