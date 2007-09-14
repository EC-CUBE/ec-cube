<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
$SC_GRAPHLINE_DIR = realpath(dirname( __FILE__));
require_once($SC_GRAPHLINE_DIR . "/SC_GraphBase.php");	

// �ޤ���������������饹
class SC_GraphLine extends SC_GraphBase{
	var $area_width;
	var $area_height;
	var $ygrid_on;
	var $graph_max;		// ����դΥ��ꥢ������(Y��ĺ������)
	var $arrXLabel;	
	var $XLabelAngle;	// X����٥����	
	var $XTitle;		// X�������ȥ�
	var $YTitle;		// Y�������ȥ�
	var $arrDataList;	// ����եǡ������Ǽ
	var $arrPointList;	// �ޤ�����ɸ���Ǽ
	var $line_max;		// ʣ��������ξ��˲û����Ƥ���
	
	var $x_margin;
	var $y_margin;
			
    // ���󥹥ȥ饯��
	function SC_GraphLine(
		$bgw = BG_WIDTH, $bgh = BG_HEIGHT, $left = LINE_LEFT, $top = LINE_TOP,
		$area_width = LINE_AREA_WIDTH, $area_height = LINE_AREA_HEIGHT) {
		parent::SC_GraphBase($bgw, $bgh, $left, $top);	
		$this->area_width = $area_width;
		$this->area_height = $area_height;
		$this->ygrid_on = true;
		$this->line_max = 0;
		$this->graph_max = 0;
		$this->XLabelAngle = 0;
		$this->x_margin = 0;
		$this->y_margin = 0;
	}
	
	// X����٥�γ��٥��å�
	function setXLabelAngle($Angle) {
		$this->XLabelAngle = $Angle;
	}
	
	// Y�������ȥ�
	function drawYTitle() {
		// Y���˥����ȥ�������
		if($this->YTitle != "") {
			$text_width = $this->getTextWidth($this->YTitle, FONT_SIZE);
			$x_pos = $this->left - ($text_width / 2);
			$y_pos = $this->top - FONT_SIZE - LINE_YTITLE_PAD;		
			$this->setText(FONT_SIZE, $x_pos, $y_pos, $this->YTitle);
		}
	}
	
	// X�������ȥ�
	function drawXTitle() {
		// Y���˥����ȥ�������
		if($this->XTitle != "") {
			$text_width = $this->getTextWidth($this->XTitle, FONT_SIZE);
			$x_pos = $this->left + $this->area_width - ($text_width / 2) + 30;
			$y_pos = $this->top + $this->area_height + LINE_XTITLE_PAD;
			$this->setText(FONT_SIZE, $x_pos, $y_pos, $this->XTitle);
		}
	}
	
	// Y��������
	function drawYLine() {
		imageline($this->image, $this->left, $this->top, $this->left, $this->top + $this->area_height, $this->flame_color);
		// �������������(������ϼ�ư)
		$size = $this->area_height / (LINE_Y_SCALE * 2);
		// �夫�������������Ƥ���
		$pos = 0;
		for($i = 0; $i < (LINE_Y_SCALE * 2); $i++) {
			// ��������
			if(($i % 2) == 0) {
				$sw = LINE_SCALE_SIZE;
				if($this->ygrid_on) {
					imageline($this->image, $this->left, $this->top + $pos, $this->left + $this->area_width, $this->top + $pos, $this->grid_color);
				}
			} else {
				$sw = LINE_SCALE_SIZE / 2;
			}
			imageline($this->image, $this->left, $this->top + $pos, $this->left + $sw, $this->top + $pos, $this->flame_color);
			$pos += $size;
		}
		// Y�����������ͤ������
		$this->setYScale();
		$this->drawYTitle();	
	}
	
	// X��������
	function drawXLine($bar = false) {
		imageline($this->image, $this->left, $this->top + $this->area_height, $this->left + $this->area_width, $this->top + $this->area_height, $this->flame_color);
		$arrPointList = $this->arrPointList[0];
		$count = count($arrPointList);
		
		// ������դξ���Ⱦ�����ꤺ�餹
		if($bar) {
			$half_scale = intval($this->area_width / ($count + 1) / 2);
		} else {
			$half_scale = 0;
		}
		
		// ��٥��ɽ�����󥿡��Х�򻻽�
		$interval = ceil($count / LINE_XLABEL_MAX);	// �ڤ�夲				
		for($i = 0; $i < $count; $i++) {
			// X����������������
			$x = $arrPointList[$i][0];
			$pos = $this->top + $this->area_height;
			imageline($this->image, $x - $half_scale, $pos, $x - $half_scale, $pos - LINE_SCALE_SIZE,  $this->flame_color);			
			// ��٥�������
			if(($i % $interval) == 0) {
				$text_width = $this->getTextWidth($this->arrXLabel[$i], FONT_SIZE);
				$x_pos = $x;
				
				if ($bar) $bar_margin = -15;

				$this->setText(FONT_SIZE, $x_pos + $this->x_margin + $bar_margin, $pos + FONT_SIZE + $this->y_margin, $this->arrXLabel[$i], NULL, $this->XLabelAngle);
			}
		}
		
		// ������դξ��ϺǸ������������ɲä���
		if($bar) {
			imageline($this->image, $x + $half_scale, $pos, $x + $half_scale, $pos - LINE_SCALE_SIZE,  $this->flame_color);	
		}
		
		$this->drawXTitle();
	}
		
	// ����å�ɽ��
	function setYGridOn($ygrid_on) {
		$this->ygrid_on = $ygrid_on;
	}
	
	// �ݥ���Ȥ�����
	function setMark($line_no, $left, $top, $size = LINE_MARK_SIZE) {
		// �������Ѵ����Ƥ���
		$size += $size % 2;
		$array = array(
			$left, $top - ($size / 2),
			$left + ($size / 2), $top,
			$left, $top + ($size / 2),
			$left - ($size / 2), $top,
		);		
		imagefilledpolygon($this->image, $array, 4, $this->arrColor[$line_no]);
		imagepolygon($this->image, $array, 4, $this->flame_color);
 		imagesetpixel ($this->image, $left, $top + ($size / 2), $this->flame_color);
	}	
	
	// Y����������ͤ������
	function setYScale() {
		// 1���������
		$number = intval($this->graph_max / LINE_Y_SCALE);				
		// �������������
		$size = $this->area_height / LINE_Y_SCALE;
		$pos = 0;
		for($i = 0; $i <= LINE_Y_SCALE; $i++) {
			$snumber = $number * (LINE_Y_SCALE - $i);
			$disp_number = number_format($snumber);
			$num_width = $this->getTextWidth($disp_number, FONT_SIZE);
			$this->setText(FONT_SIZE, $this->left - $num_width - 2, $this->top + $pos - (FONT_SIZE / 2), $disp_number);
			$pos += $size;
		}
	}
	
	// 
	function setMax($arrData) {
		// �ǡ����κ����ͤ�������롣
		$data_max = max($arrData);
		// 10�β��ܤ������
		$figure = strlen($data_max) - 1;
		// ���η��׻�����
		$tenval = pow(10, $figure);
		// ����վ�Ǥκ����ͤ����
		$this->graph_max = $tenval * (intval($data_max / $tenval) + 1);
		// �����ͤ�10̤���ξ����б�
		if($this->graph_max < 10) {
			$this->graph_max = 10;
		}	
	}
	
	// ����դ�����
	function drawGraph() {
		// ������طʤ�����
		$this->drawYLine();
		$this->drawXLine();
		
		// �ޤ������������
		for($i = 0; $i < $this->line_max; $i++) {
			$this->drawLine($i);
		}
		
		// �ޡ���������
		for($i = 0; $i < $this->line_max; $i++) {
			$this->drawMark($i);
		}
		
		// ��٥������
		for($i = 0; $i < $this->line_max; $i++) {
			$this->drawLabel($i);		
		}

		// ���������
		$this->drawLegend();	
	}
	
	// �饤������褹��
	function drawLine($line_no) {
		$arrPointList = $this->arrPointList[$line_no];
		
		$count = count($arrPointList);
		for($i = 0; $i < $count; $i++) {
			$x = $arrPointList[$i][0];
			$y = $arrPointList[$i][1];
			if(isset($arrPointList[$i + 1])) {
				$next_x = $arrPointList[$i + 1][0];
				$next_y = $arrPointList[$i + 1][1];
				imageline($this->image, $x, $y, $next_x, $next_y, $this->arrColor[$line_no]);
			}
		}
	}
	
	// �ޡ��������褹��
	function drawMark($line_no) {
		$arrPointList = $this->arrPointList[$line_no];
		$count = count($arrPointList);
		for($i = 0; $i < $count; $i++) {
			$x = $arrPointList[$i][0];
			$y = $arrPointList[$i][1];			
			$this->setMark($line_no, $x, $y);
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
	
	// �ǡ����򥻥åȤ���
	function setData($arrData) {
		$this->arrDataList[$this->line_max] = array_values((array)$arrData);
		$this->setMax($this->arrDataList[$this->line_max]);
		// �ͤ������Ѵ�Ψ
		$rate = $this->area_height / $this->graph_max;
		// ����Ψ��׻�
		$count = count($this->arrDataList[$this->line_max]);
		$scale_width = $this->area_width / ($count + 1);		
		$this->arrPointList[$this->line_max] = array();
		for($i = 0; $i < $count; $i++) {
			// X��ɸ�����
			$x = intval($this->left + ($scale_width * ($i + 1)));
			// Y��ɸ�����
			$y = intval($this->top + $this->area_height - ($this->arrDataList[$this->line_max][$i] * $rate));
			// XY��ɸ����¸����
			$this->arrPointList[$this->line_max][] = array($x, $y);
		}
		$this->line_max++;
	}
	
	// X����٥�򥻥åȤ���
	function setXLabel($arrXLabel) {
		$this->arrXLabel = array_values((array)$arrXLabel);
	}
	
	// X�������ȥ�򥻥åȤ���
	function setXTitle($title) {
		$this->XTitle = $title;
	}
	
	// Y�������ȥ�򥻥åȤ���
	function setYTitle($title) {
		$this->YTitle = $title;
	}	
}
?>