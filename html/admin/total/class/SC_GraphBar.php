<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
$SC_GRAPHBAR_DIR = realpath(dirname( __FILE__));
require_once($SC_GRAPHBAR_DIR . "/SC_GraphLine.php");	

// ��������������饹
class SC_GraphBar extends SC_GraphLine{
    // ���󥹥ȥ饯��
	function SC_GraphLine(
		$bgw = BG_WIDTH, $bgh = BG_HEIGHT, $left = LINE_LEFT, $top = LINE_TOP,
		$area_width = LINE_AREA_WIDTH, $area_height = LINE_AREA_HEIGHT) {
		parent::SC_GraphLine($bgw, $bgh, $left, $top, $area_width, $area_height);	
	}
	
	// ����դ�����
	function drawGraph() {
		$this->drawYLine();
		$this->drawXLine(true);
		
		// ������դ�����
		for($i = 0; $i < $this->line_max; $i++) {
			$this->drawBar($i);
		}
		
		// ��٥������
		for($i = 0; $i < $this->line_max; $i++) {
			$this->drawLabel($i);
		}
		
		// ���������
		$this->drawLegend();	
	}
	
	// ������դ�����
	function drawBar($line_no) {
		$arrPointList = $this->arrPointList[$line_no];
		// �ǡ������������
		$count = count($arrPointList);
		// Ⱦ��������������
		$half_scale = intval($this->area_width / ($count + 1) / 2);
		// ��������������
		$scale_width = intval($this->area_width / ($count + 1));
		// ������դΥ����������
		$bar_width = intval(($scale_width - (BAR_PAD * 2)) / $this->line_max);
		// �����μ���
		$c_max = count($this->arrColor);
		for($i = 0; $i < $count; $i++) {
			$left = $arrPointList[$i][0] - $half_scale + BAR_PAD + ($bar_width * $line_no);
			$top = $arrPointList[$i][1];
			$right = $left + $bar_width;
			$bottom = $this->top + $this->area_height;
			
			// �Ƥ�����
			if($this->shade_on) {
				imagefilledrectangle($this->image, $left + 2, $top + 2, $right + 2, $bottom, $this->shade_color);
			}
			//imagefilledrectangle($this->image, $left, $top, $right, $bottom, $this->arrColor[($i % $c_max)]);
			imagefilledrectangle($this->image, $left, $top, $right, $bottom, $this->arrColor[$line_no]);			
			imagerectangle($this->image, $left, $top, $right, $bottom, $this->flame_color);					
		}
	}
	
	// ��٥�����褹��
	function drawLabel($line_no) {
		$arrData = $this->arrDataList[$line_no];
		$arrPointList = $this->arrPointList[$line_no];
		$count = count($arrPointList);
		for($i = 0; $i < $count; $i++) {
			$x = $arrPointList[$i][0];
			$y = $arrPointList[$i][1];
			$text_width = $this->getTextWidth(number_format($arrData[$i]), FONT_SIZE);
			$y_pos = $y - FONT_SIZE - 5;
			$x_pos = $x - $text_width / 2;
			$this->setText(FONT_SIZE, $x_pos, $y_pos, number_format($arrData[$i]));
		}
	}
	
}
?>