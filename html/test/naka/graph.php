<?php

require_once("../../require.php");

require_once(HTML_PATH . "admin/total/class/SC_GraphPie.php");
require_once(HTML_PATH . "admin/total/class/SC_GraphLine.php");
require_once(HTML_PATH . "admin/total/class/SC_GraphBar.php");
		
		$objGraphPie = new SC_GraphPie(400, 250, 80, 70);
		
		/* �ǥХå�ɽ���� by naka
		foreach($arrList as $key => $val) {
			$objGraphPie->debugPrint("key:$key val:$val");
		}
		*/
		
		$arrList = array(
			'����A' => 11,
			'����B' => 32,
			'����C' => 48
		);
		
		// �ǡ����򥻥åȤ���
		$objGraphPie->setData($arrList);
		// ����򥻥åȤ���
		$objGraphPie->setLegend(array_keys($arrList));
		
		// �ߥ��������
		//$objGraphPie->drawGraph();
		
		$x = $objGraphPie->cx;
		$y = $objGraphPie->cy;
		$z = $objGraphPie->cz;
		$h = $objGraphPie->ch;
		$w = $objGraphPie->cw;
		
		// �ǡ����γ��٤��������
		$arrRad = $objGraphPie->getCircleData($objGraphPie->arrData);
		$rd_max = count($arrRad);
		
		// �ǡ�����¸�ߤ��ʤ����
		if($rd_max <= 0) {
			return;
		}
		
		// �Ƥ�����
		if($objGraphPie->shade_on) {
			$objGraphPie->drawShade();
		}
		
		// �����μ���
		$c_max = count($objGraphPie->arrColor);
		$dc_max = count($objGraphPie->arrDarkColor);
		
		// ¦�̤�����		
		for ($i = ($y + $z - 1); $i >= $y; $i--) {
			$start = 0;
			for($j = 0; $j < $rd_max; $j++) {
				// ���٤�0�ٰʾ�ξ��Τ�¦�̤����褹�롣
				if($arrRad[$j] > 0) {
					$end = $start + $arrRad[$j];
					if($start == 0 && $end == 360) {
						// -90���270�ǻ��ꤹ��ȱߤ�����Ǥ��ʤ��Τ�0���360�˻���
						imagearc($objGraphPie->image, $x, $i, $w, $h, 0, 360, $objGraphPie->arrDarkColor[($j % $dc_max)]);
					} else {
						// -90���12���ΰ��֤��鳫�Ϥ���褦���������Ƥ���
						imagearc($objGraphPie->image, $x, $i, $w, $h, $start - 90, $end - 90, $objGraphPie->arrDarkColor[($j % $dc_max)]);	
					}			
					$start = $end;
				}
			}
		}
		

		
		// ���̤�����
		$start = 0;
		for($i = 0; $i < $rd_max; $i++) {
			$end = $start + $arrRad[$i];
			if($start == 0 && $end == 360) {
				// -90���270�ǻ��ꤹ��ȱߤ�����Ǥ��ʤ��Τ�0���360�˻���
				imagefilledarc($objGraphPie->image, $x, $y, $w, $h, 0, 360, $objGraphPie->arrColor[($i % $c_max)], IMG_ARC_PIE);			
			} else {
				// -90���12���ΰ��֤��鳫�Ϥ���褦���������Ƥ��롣		
				imagefilledarc($objGraphPie->image, $x, $y, $w, $h, $start - 90, $end - 90, $objGraphPie->arrColor[($i % $c_max)], IMG_ARC_PIE);
			}
			$start = $end;
		}
		/*
		// ���̤�����
		imagearc($objGraphPie->image, $x, $y + $z, $w, $h, 0, 180 , $objGraphPie->flame_color);
		
		// ���̤α���
		$start = 0;
		for($i = 0; $i < $rd_max; $i++) {
			$end = $start + $arrRad[$i];
			if($start == 0 && $end == 360) {
				// -90���270�ǻ��ꤹ��ȱߤ�����Ǥ��ʤ��Τ�0���360�˻���
				imagearc($objGraphPie->image, $x, $y, $w, $h, 0, 360 , $objGraphPie->flame_color);
			}
			// -90���12���ΰ��֤��鳫�Ϥ���褦���������Ƥ��롣
			imagefilledarc($objGraphPie->image, $x, $y, $w, $h, $start - 90, $end - 90, $objGraphPie->flame_color, IMG_ARC_EDGED|IMG_ARC_NOFILL);
			$start = $end;
		}
		
		// ¦�̤α���
		imageline($objGraphPie->image, $x + ($w / 2), $y, $x + ($w / 2), $y + $z, $objGraphPie->flame_color);
		imageline($objGraphPie->image, $x - ($w / 2), $y, $x - ($w / 2), $y + $z, $objGraphPie->flame_color);
		
		
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
				imageline($objGraphPie->image, $ax, $ay, $ax, $ay + $z, $objGraphPie->flame_color);
			}
			$start = $end;	
		}
		
		// ��٥������
		$objGraphPie->drawLabel($arrRad);
		// ���������
		$objGraphPie->drawLegend(count($objGraphPie->arrData));
		*/
		
		$objGraphPie->resampled();
		
		$objGraphPie->outputGraph();
		exit();
?>