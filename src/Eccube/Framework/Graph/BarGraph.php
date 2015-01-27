<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework\Graph;

// 棒グラフ生成クラス
class BarGraph extends LineGraph
{
    // コンストラクタ
    public function __construct(
        $bgw = BG_WIDTH, $bgh = BG_HEIGHT, $left = LINE_LEFT, $top = LINE_TOP,
        $area_width = LINE_AREA_WIDTH, $area_height = LINE_AREA_HEIGHT) {
        parent::__construct($bgw, $bgh, $left, $top, $area_width, $area_height);
    }

    // グラフの描画
    public function drawGraph()
    {
        $this->drawYLine();
        $this->drawXLine(true);

        // 棒グラフの描画
        for ($i = 0; $i < $this->line_max; $i++) {
            $this->drawBar($i);
        }

        // ラベルの描画
        for ($i = 0; $i < $this->line_max; $i++) {
            $this->drawLabel($i);
        }

        // 凡例の描画
        $this->drawLegend();
    }

    // 棒グラフの描画

    /**
     * @param integer $line_no
     */
    public function drawBar($line_no)
    {
        $arrPointList = $this->arrPointList[$line_no];
        // データ数を数える
        $count = count($arrPointList);
        // 半目盛りの幅を求める
        $half_scale = intval($this->area_width / ($count + 1) / 2);
        // 目盛りの幅を求める
        $scale_width = intval($this->area_width / ($count + 1));
        // 棒グラフのサイズを求める
        $bar_width = intval(($scale_width - (BAR_PAD * 2)) / $this->line_max);
        // 色数の取得
        $c_max = count($this->arrColor);
        for ($i = 0; $i < $count; $i++) {
            $left = $arrPointList[$i][0] - $half_scale + BAR_PAD + ($bar_width * $line_no);
            $top = $arrPointList[$i][1];
            $right = $left + $bar_width;
            $bottom = $this->top + $this->area_height;

            // 影の描画
            if ($this->shade_on) {
                imagefilledrectangle($this->image, $left + 2, $top + 2, $right + 2, $bottom, $this->shade_color);
            }
            //imagefilledrectangle($this->image, $left, $top, $right, $bottom, $this->arrColor[($i % $c_max)]);
            imagefilledrectangle($this->image, $left, $top, $right, $bottom, $this->arrColor[$line_no]);
            imagerectangle($this->image, $left, $top, $right, $bottom, $this->flame_color);
        }
    }

    // ラベルを描画する

    /**
     * @param integer $line_no
     */
    public function drawLabel($line_no)
    {
        $arrData = $this->arrDataList[$line_no];
        $arrPointList = $this->arrPointList[$line_no];
        $count = count($arrPointList);
        for ($i = 0; $i < $count; $i++) {
            $x = $arrPointList[$i][0];
            $y = $arrPointList[$i][1];
            $text_width = $this->getTextWidth(number_format($arrData[$i]), FONT_SIZE);
            $y_pos = $y - FONT_SIZE - 5;
            $x_pos = $x - $text_width / 2;
            $this->setText(FONT_SIZE, $x_pos, $y_pos, number_format($arrData[$i]));
        }
    }
}
