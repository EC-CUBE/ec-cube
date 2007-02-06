<?php

require_once("../../require.php");

require_once(HTML_PATH . "admin/total/class/SC_GraphPie.php");
require_once(HTML_PATH . "admin/total/class/SC_GraphLine.php");
require_once(HTML_PATH . "admin/total/class/SC_GraphBar.php");
		
		$objGraphPie = new SC_GraphPie(400, 250, 80, 70);
		
		/* デバッグ表示用 by naka
		foreach($arrList as $key => $val) {
			$objGraphPie->debugPrint("key:$key val:$val");
		}
		*/
		
		$arrList = array(
			'練習A' => 11,
			'練習B' => 32,
			'練習C' => 48
		);
		
		// データをセットする
		$objGraphPie->setData($arrList);
		// 凡例をセットする
		$objGraphPie->setLegend(array_keys($arrList));
		
		// 円グラフ描画
		//$objGraphPie->drawGraph();
		
		$x = $objGraphPie->cx;
		$y = $objGraphPie->cy;
		$z = $objGraphPie->cz;
		$h = $objGraphPie->ch;
		$w = $objGraphPie->cw;
		
		// データの角度を取得する
		$arrRad = $objGraphPie->getCircleData($objGraphPie->arrData);
		$rd_max = count($arrRad);
		
		// データが存在しない場合
		if($rd_max <= 0) {
			return;
		}
		
		// 影の描画
		if($objGraphPie->shade_on) {
			$objGraphPie->drawShade();
		}
		
		// 色数の取得
		$c_max = count($objGraphPie->arrColor);
		$dc_max = count($objGraphPie->arrDarkColor);
		
		// 側面の描画		
		for ($i = ($y + $z - 1); $i >= $y; $i--) {
			$start = 0;
			for($j = 0; $j < $rd_max; $j++) {
				// 角度が0度以上の場合のみ側面を描画する。
				if($arrRad[$j] > 0) {
					$end = $start + $arrRad[$j];
					if($start == 0 && $end == 360) {
						// -90~270で指定すると円が描画できないので0~360に指定
						imagearc($objGraphPie->image, $x, $i, $w, $h, 0, 360, $objGraphPie->arrDarkColor[($j % $dc_max)]);
					} else {
						// -90°は12時の位置から開始するように補正している
						imagearc($objGraphPie->image, $x, $i, $w, $h, $start - 90, $end - 90, $objGraphPie->arrDarkColor[($j % $dc_max)]);	
					}			
					$start = $end;
				}
			}
		}
		

		
		// 上面の描画
		$start = 0;
		for($i = 0; $i < $rd_max; $i++) {
			$end = $start + $arrRad[$i];
			if($start == 0 && $end == 360) {
				// -90~270で指定すると円が描画できないので0~360に指定
				imagefilledarc($objGraphPie->image, $x, $y, $w, $h, 0, 360, $objGraphPie->arrColor[($i % $c_max)], IMG_ARC_PIE);			
			} else {
				// -90°は12時の位置から開始するように補正している。		
				imagefilledarc($objGraphPie->image, $x, $y, $w, $h, $start - 90, $end - 90, $objGraphPie->arrColor[($i % $c_max)], IMG_ARC_PIE);
			}
			$start = $end;
		}
		/*
		// 底面の描画
		imagearc($objGraphPie->image, $x, $y + $z, $w, $h, 0, 180 , $objGraphPie->flame_color);
		
		// 上面の縁取り
		$start = 0;
		for($i = 0; $i < $rd_max; $i++) {
			$end = $start + $arrRad[$i];
			if($start == 0 && $end == 360) {
				// -90~270で指定すると円が描画できないので0~360に指定
				imagearc($objGraphPie->image, $x, $y, $w, $h, 0, 360 , $objGraphPie->flame_color);
			}
			// -90°は12時の位置から開始するように補正している。
			imagefilledarc($objGraphPie->image, $x, $y, $w, $h, $start - 90, $end - 90, $objGraphPie->flame_color, IMG_ARC_EDGED|IMG_ARC_NOFILL);
			$start = $end;
		}
		
		// 側面の縁取り
		imageline($objGraphPie->image, $x + ($w / 2), $y, $x + ($w / 2), $y + $z, $objGraphPie->flame_color);
		imageline($objGraphPie->image, $x - ($w / 2), $y, $x - ($w / 2), $y + $z, $objGraphPie->flame_color);
		
		
		$start = 0;
		for($i = 0; $i < $rd_max; $i++) {
			$end = $start + $arrRad[$i];
			// 前面のみ
			if($end > 90 && $end < 270) {
				list($ax, $ay) = lfGetArcPos($x, $y, $w, $h, $end);
				// ラインのずれを補正する
				if($end > 180) {
					$ax = $ax + 1;
				}
				imageline($objGraphPie->image, $ax, $ay, $ax, $ay + $z, $objGraphPie->flame_color);
			}
			$start = $end;	
		}
		
		// ラベルの描画
		$objGraphPie->drawLabel($arrRad);
		// 凡例の描画
		$objGraphPie->drawLegend(count($objGraphPie->arrData));
		*/
		
		$objGraphPie->resampled();
		
		$objGraphPie->outputGraph();
		exit();
?>