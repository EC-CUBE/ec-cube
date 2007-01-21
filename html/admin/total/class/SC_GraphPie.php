<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
$SC_GRAPHPIE_DIR = realpath(dirname( __FILE__));
require_once($SC_GRAPHPIE_DIR . "/SC_GraphBase.php");	

// 円グラフ生成クラス
class SC_GraphPie extends SC_GraphBase{
	var $cw;
	var $ch;
	var $cz;
	var $cx;
	var $cy;
	var $arrLabel;
	var $arrData;
	
    // コンストラクタ
	function SC_GraphPie($bgw = BG_WIDTH, $bgh = BG_HEIGHT, $left = PIE_LEFT, $top = PIE_TOP) {
		parent::SC_GraphBase($bgw, $bgh, $left, $top);
		// サイズ設定
		$this->setSize(PIE_WIDTH, PIE_HEIGHT, PIE_THICK);
		// 位置設定
		$this->setPosition($this->left + ($this->cw / 2), $this->top + ($this->ch / 2));
    }
	
	// データを360°値に変換する
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
		// ラベル表示用
		$p_rate = 100 / $total;
		$cnt = 0;
		foreach($array as $val) {
			$ret = round($val * $rate);
			$new_total+= $ret;
			$arrRet[] = $ret;
			// パーセント表示用
			$this->arrLabel[] = round($val * $p_rate) . " %";
			$cnt++;
		}
		// 合計が360になるように補正しておく
		$arrRet[0] -= $new_total - 360;
		return $arrRet;
	}	
		
	// 円の位置設定を行う
	function setPosition($cx, $cy) {
		$this->cx = $cx;
		$this->cy = $cy;
	}
		
	// 円のサイズ設定を行う
	function setSize($cw, $ch, $cz = 0) {
		$this->cw = $cw;
		$this->ch = $ch;
		$this->cz = $cz;
	}
	
	// 影の描画
	function drawShade() {
		$move = 1;
		for($i = ($this->cy + $this->cz); $i <= ($this->cy + $this->cz + ($this->cz * PIE_SHADE_IMPACT)); $i++) {
			imagefilledarc($this->image, $this->cx + $move, $i, $this->cw, $this->ch, 0, 360, $this->shade_color, IMG_ARC_PIE);
			$move += 0.5;
		}
	}
	
	// データをセットする
	function setData($arrData) {
		$this->arrData = array_values($arrData);
	}
	
	// 円グラフを描画する
	function drawGraph() {
		$x = $this->cx;
		$y = $this->cy;
		$z = $this->cz;
		$h = $this->ch;
		$w = $this->cw;
		
		// データの角度を取得する
		$arrRad = $this->getCircleData($this->arrData);
		$rd_max = count($arrRad);
		
		// データが存在しない場合
		if($rd_max <= 0) {
			return;
		}
		
		// 影の描画
		if($this->shade_on) {
			$this->drawShade();
		}
			
		// 色数の取得
		$c_max = count($this->arrColor);
		$dc_max = count($this->arrDarkColor);
		
		// 側面の描画		
		for ($i = ($y + $z - 1); $i >= $y; $i--) {
			$start = 0;
			for($j = 0; $j < $rd_max; $j++) {
				// 角度が0度以上の場合のみ側面を描画する。
				if($arrRad[$j] > 0) {
					$end = $start + $arrRad[$j];
					if($start == 0 && $end == 360) {
						// -90~270で指定すると円が描画できないので0~360に指定
						imagearc($this->image, $x, $i, $w, $h, 0, 360, $this->arrDarkColor[($j % $dc_max)]);
					} else {
						// -90°は12時の位置から開始するように補正している
						imagearc($this->image, $x, $i, $w, $h, $start - 90, $end - 90, $this->arrDarkColor[($j % $dc_max)]);	
					}			
					$start = $end;
				}
			}
		}
		// 底面の描画
		imagearc($this->image, $x, $y + $z, $w, $h, 0, 180 , $this->flame_color);

		// 上面の描画
		$start = 0;
		for($i = 0; $i < $rd_max; $i++) {
			$end = $start + $arrRad[$i];
			if($start == 0 && $end == 360) {
				// -90~270で指定すると円が描画できないので0~360に指定
				imagefilledarc($this->image, $x, $y, $w, $h, 0, 360, $this->arrColor[($i % $c_max)], IMG_ARC_PIE);			
			} else {
				// -90°は12時の位置から開始するように補正している。		
				imagefilledarc($this->image, $x, $y, $w, $h, $start - 90, $end - 90, $this->arrColor[($i % $c_max)], IMG_ARC_PIE);
			}
			$start = $end;
		}

		// 上面の縁取り
		$start = 0;
		for($i = 0; $i < $rd_max; $i++) {
			$end = $start + $arrRad[$i];
			if($start == 0 && $end == 360) {
				// -90~270で指定すると円が描画できないので0~360に指定
				imagearc($this->image, $x, $y, $w, $h, 0, 360 , $this->flame_color);
			}
			// -90°は12時の位置から開始するように補正している。
			imagefilledarc($this->image, $x, $y, $w, $h, $start - 90, $end - 90, $this->flame_color, IMG_ARC_EDGED|IMG_ARC_NOFILL);
			$start = $end;
		}

		// 側面の縁取り
		imageline($this->image, $x + ($w / 2), $y, $x + ($w / 2), $y + $z, $this->flame_color);
		imageline($this->image, $x - ($w / 2), $y, $x - ($w / 2), $y + $z, $this->flame_color);
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
				imageline($this->image, $ax, $ay, $ax, $ay + $z, $this->flame_color);
			}
			$start = $end;	
		}
				
		// ラベルの描画
		$this->drawLabel($arrRad);
		// 凡例の描画
		$this->drawLegend(count($this->arrData));			
	}
	
	// 円グラフのラベルを描画する
	function drawLabel($arrRad) {
		$rd_max = count($arrRad);
		$start = 0;
		for($i = 0; $i < $rd_max; $i++) {
			$center = $start + ($arrRad[$i] / 2);
			$end = $start + $arrRad[$i];
			list($sx, $sy) = lfGetArcPos($this->cx, $this->cy, ($this->cw / 1.5), ($this->ch / 1.5), $center);
			list($ex, $ey) = lfGetArcPos($this->cx, $this->cy, ($this->cw * 1.5), ($this->ch * 1.5), $center);
			// 指示線の描画
			imageline($this->image, $sx, $sy, $ex + 2, $ey - PIE_LABEL_UP, $this->flame_color);
			$this->setText(FONT_SIZE, $ex - 10, $ey - PIE_LABEL_UP - FONT_SIZE, $this->arrLabel[$i], NULL, 0, true);
			$start = $end;
		}
	}	
}

?>