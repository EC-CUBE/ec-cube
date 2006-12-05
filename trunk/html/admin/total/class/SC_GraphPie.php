<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
$SC_GRAPHPIE_DIR = realpath(dirname( __FILE__));
require_once($SC_GRAPHPIE_DIR . "/SC_GraphBase.php");	

// �ߥ�����������饹
class SC_GraphPie extends SC_GraphBase{
	var $cw;
	var $ch;
	var $cz;
	var $cx;
	var $cy;
	var $arrLabel;
	var $arrData;
	
    // ���󥹥ȥ饯��
	function SC_GraphPie($bgw = BG_WIDTH, $bgh = BG_HEIGHT, $left = PIE_LEFT, $top = PIE_TOP) {
		parent::SC_GraphBase($bgw, $bgh, $left, $top);
		// ����������
		$this->setSize(PIE_WIDTH, PIE_HEIGHT, PIE_THICK);
		// ��������
		$this->setPosition($this->left + ($this->cw / 2), $this->top + ($this->ch / 2));
    }
	
	// �ǡ�����360���ͤ��Ѵ�����
	function getCircleData($array) {
		if(!is_array($array)) {
			return;
		}
		$arrRet = array();
		foreach($array as $val) {
			$total += $val;			
		}
		if($total <= 0) {
			return;
		}		
		$rate = 360 / $total;
		// ��٥�ɽ����
		$p_rate = 100 / $total;
		$cnt = 0;
		foreach($array as $val) {
			$ret = round($val * $rate);
			$new_total+= $ret;
			$arrRet[] = $ret;
			// �ѡ������ɽ����
			$this->arrLabel[] = round($val * $p_rate) . " %";
			$cnt++;
		}
		// ��פ�360�ˤʤ�褦���������Ƥ���
		$arrRet[0] -= $new_total - 360;
		return $arrRet;
	}	
		
	// �ߤΰ��������Ԥ�
	function setPosition($cx, $cy) {
		$this->cx = $cx;
		$this->cy = $cy;
	}
		
	// �ߤΥ����������Ԥ�
	function setSize($cw, $ch, $cz = 0) {
		$this->cw = $cw;
		$this->ch = $ch;
		$this->cz = $cz;
	}
	
	// �Ƥ�����
	function drawShade() {
		$move = 1;
		for($i = ($this->cy + $this->cz); $i <= ($this->cy + $this->cz + ($this->cz * PIE_SHADE_IMPACT)); $i++) {
			imagefilledarc($this->image, $this->cx + $move, $i, $this->cw, $this->ch, 0, 360, $this->shade_color, IMG_ARC_PIE);
			$move += 0.5;
		}
	}
	
	// �ǡ����򥻥åȤ���
	function setData($arrData) {
		$this->arrData = array_values($arrData);
	}
	
	// �ߥ���դ����褹��
	function drawGraph() {
		$x = $this->cx;
		$y = $this->cy;
		$z = $this->cz;
		$h = $this->ch;
		$w = $this->cw;
		
		// �ǡ����γ��٤��������
		$arrRad = $this->getCircleData($this->arrData);
		$rd_max = count($arrRad);
		
		// �ǡ�����¸�ߤ��ʤ����
		if($rd_max <= 0) {
			return;
		}
		
		// �Ƥ�����
		if($this->shade_on) {
			$this->drawShade();
		}
			
		// �����μ���
		$c_max = count($this->arrColor);
		$dc_max = count($this->arrDarkColor);
		
		// ¦�̤�����		
		for ($i = ($y + $z - 1); $i >= $y; $i--) {
			$start = 0;
			for($j = 0; $j < $rd_max; $j++) {
				// ���٤�0�ٰʾ�ξ��Τ�¦�̤����褹�롣
				if($arrRad[$j] > 0) {
					$end = $start + $arrRad[$j];
					if($start == 0 && $end == 360) {
						// -90���270�ǻ��ꤹ��ȱߤ�����Ǥ��ʤ��Τ�0���360�˻���
						imagearc($this->image, $x, $i, $w, $h, 0, 360, $this->arrDarkColor[($j % $dc_max)]);
					} else {
						// -90���12���ΰ��֤��鳫�Ϥ���褦���������Ƥ���
						imagearc($this->image, $x, $i, $w, $h, $start - 90, $end - 90, $this->arrDarkColor[($j % $dc_max)]);	
					}			
					$start = $end;
				}
			}
		}
		// ���̤�����
		imagearc($this->image, $x, $y + $z, $w, $h, 0, 180 , $this->flame_color);

		// ���̤�����
		$start = 0;
		for($i = 0; $i < $rd_max; $i++) {
			$end = $start + $arrRad[$i];
			if($start == 0 && $end == 360) {
				// -90���270�ǻ��ꤹ��ȱߤ�����Ǥ��ʤ��Τ�0���360�˻���
				imagefilledarc($this->image, $x, $y, $w, $h, 0, 360, $this->arrColor[($i % $c_max)], IMG_ARC_PIE);			
			} else {
				// -90���12���ΰ��֤��鳫�Ϥ���褦���������Ƥ��롣		
				imagefilledarc($this->image, $x, $y, $w, $h, $start - 90, $end - 90, $this->arrColor[($i % $c_max)], IMG_ARC_PIE);
			}
			$start = $end;
		}

		// ���̤α���
		$start = 0;
		for($i = 0; $i < $rd_max; $i++) {
			$end = $start + $arrRad[$i];
			if($start == 0 && $end == 360) {
				// -90���270�ǻ��ꤹ��ȱߤ�����Ǥ��ʤ��Τ�0���360�˻���
				imagearc($this->image, $x, $y, $w, $h, 0, 360 , $this->flame_color);
			}
			// -90���12���ΰ��֤��鳫�Ϥ���褦���������Ƥ��롣
			imagefilledarc($this->image, $x, $y, $w, $h, $start - 90, $end - 90, $this->flame_color, IMG_ARC_EDGED|IMG_ARC_NOFILL);
			$start = $end;
		}

		// ¦�̤α���
		imageline($this->image, $x + ($w / 2), $y, $x + ($w / 2), $y + $z, $this->flame_color);
		imageline($this->image, $x - ($w / 2), $y, $x - ($w / 2), $y + $z, $this->flame_color);
		$start = 0;
		for($i = 0; $i < $rd_max; $i++) {
			$end = $start + $arrRad[$i];
			// ���̤Τ�
			if($end > 90 && $end < 270) {
				list($ax, $ay) = lfGetArcPos($x, $y, $w, $h, $end);
				// �饤��Τ������������
				if($end > 180) {
					$ax = $ax + 1;
				}
				imageline($this->image, $ax, $ay, $ax, $ay + $z, $this->flame_color);
			}
			$start = $end;	
		}
				
		// ��٥������
		$this->drawLabel($arrRad);
		// ���������
		$this->drawLegend(count($this->arrData));			
	}
	
	// �ߥ���դΥ�٥�����褹��
	function drawLabel($arrRad) {
		$rd_max = count($arrRad);
		$start = 0;
		for($i = 0; $i < $rd_max; $i++) {
			$center = $start + ($arrRad[$i] / 2);
			$end = $start + $arrRad[$i];
			list($sx, $sy) = lfGetArcPos($this->cx, $this->cy, ($this->cw / 1.5), ($this->ch / 1.5), $center);
			list($ex, $ey) = lfGetArcPos($this->cx, $this->cy, ($this->cw * 1.5), ($this->ch * 1.5), $center);
			// �ؼ���������
			imageline($this->image, $sx, $sy, $ex + 2, $ey - PIE_LABEL_UP, $this->flame_color);
			$this->setText(FONT_SIZE, $ex - 10, $ey - PIE_LABEL_UP - FONT_SIZE, $this->arrLabel[$i], NULL, 0, true);
			$start = $end;
		}
	}	
}

?>