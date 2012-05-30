<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// 円グラフ生成クラス
class SC_Graph_Pie extends SC_Graph_Base_Ex{
    var $cw;
    var $ch;
    var $cz;
    var $cx;
    var $cy;
    var $arrLabel;
    var $arrData;

    // コンストラクタ
    function __construct($bgw = BG_WIDTH, $bgh = BG_HEIGHT, $left = PIE_LEFT, $top = PIE_TOP) {
        parent::__construct($bgw, $bgh, $left, $top);
        // サイズ設定
        $this->setSize(PIE_WIDTH, PIE_HEIGHT, PIE_THICK);
        // 位置設定
        $this->setPosition($this->left + ($this->cw / 2), $this->top + ($this->ch / 2));
    }

    // データを360°値に変換する
    function getCircleData($array) {
        $total = '';
        $new_total = '';
        if (!is_array($array)) {
            return;
        }
        $arrRet = array();
        foreach ($array as $val) {
            $total += $val;
        }
        if ($total <= 0) {
            return;
        }
        $rate = 360 / $total;
        // ラベル表示用
        $p_rate = 100 / $total;
        $cnt = 0;
        foreach ($array as $val) {
            $ret = round($val * $rate);
            $new_total+= $ret;
            $arrRet[] = $ret;
            // パーセント表示用
            $this->arrLabel[] = round($val * $p_rate) . ' %';
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
        for ($i = ($this->cy + $this->cz); $i <= ($this->cy + $this->cz + ($this->cz * PIE_SHADE_IMPACT)); $i++) {
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
        if ($rd_max <= 0) {
            return;
        }

        // 影の描画
        if ($this->shade_on) {
            $this->drawShade();
        }

        // 色数の取得
        $c_max = count($this->arrColor);
        $dc_max = count($this->arrDarkColor);

        // 側面の描画
        for ($i = ($y + $z - 1); $i >= $y; $i--) {
            $start = 0;
            for ($j = 0; $j < $rd_max; $j++) {
                // 角度が0度以上の場合のみ側面を描画する。
                if ($arrRad[$j] > 0) {
                    $end = $start + $arrRad[$j];
                    if ($start == 0 && $end == 360) {
                        // -90~270で指定