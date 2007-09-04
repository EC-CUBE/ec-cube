<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
$SC_GRAPHLINE_DIR = realpath(dirname( __FILE__));
require_once($SC_GRAPHLINE_DIR . "/SC_GraphBase.php");

// 折れ線グラフ生成クラス
class SC_GraphLine extends SC_GraphBase{
    var $area_width;
    var $area_height;
    var $ygrid_on;
    var $graph_max;     // グラフのエリア最大値(Y軸頂点の値)
    var $arrXLabel;
    var $XLabelAngle;   // X軸ラベル角度
    var $XTitle;        // X軸タイトル
    var $YTitle;        // Y軸タイトル
    var $arrDataList;   // グラフデータを格納
    var $arrPointList;  // 折れ線座標を格納
    var $line_max;      // 複数の描画の場合に加算していく

    var $x_margin;
    var $y_margin;

    // コンストラクタ
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

    // X軸ラベルの角度セット
    function setXLabelAngle($Angle) {
        $this->XLabelAngle = $Angle;
    }

    // Y軸タイトル
    function drawYTitle() {
        // Y軸にタイトルを入れる
        if($this->YTitle != "") {
            $text_width = $this->getTextWidth($this->YTitle, FONT_SIZE);
            $x_pos = $this->left - ($text_width / 2);
            $y_pos = $this->top - FONT_SIZE - LINE_YTITLE_PAD;
            $this->setText(FONT_SIZE, $x_pos, $y_pos, $this->YTitle);
        }
    }

    // X軸タイトル
    function drawXTitle() {
        // Y軸にタイトルを入れる
        if($this->XTitle != "") {
            $text_width = $this->getTextWidth($this->XTitle, FONT_SIZE);
            $x_pos = $this->left + $this->area_width - ($text_width / 2) + 30;
            $y_pos = $this->top + $this->area_height + LINE_XTITLE_PAD;
            $this->setText(FONT_SIZE, $x_pos, $y_pos, $this->XTitle);
        }
    }

    // Y軸の描画
    function drawYLine() {
        imageline($this->image, $this->left, $this->top, $this->left, $this->top + $this->area_height, $this->flame_color);
        // 目盛り幅を求める(中間点は自動)
        $size = $this->area_height / (LINE_Y_SCALE * 2);
        // 上から目盛りを入れていく
        $pos = 0;
        for($i = 0; $i < (LINE_Y_SCALE * 2); $i++) {
            // 目盛り幅
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
        // Y軸に目盛り値を入れる
        $this->setYScale();
        $this->drawYTitle();
    }

    // X軸の描画
    function drawXLine($bar = false) {
        imageline($this->image, $this->left, $this->top + $this->area_height, $this->left + $this->area_width, $this->top + $this->area_height, $this->flame_color);
        $arrPointList = $this->arrPointList[0];
        $count = count($arrPointList);

        // 棒グラフの場合は半目盛りずらす
        if($bar) {
            $half_scale = intval($this->area_width / ($count + 1) / 2);
        } else {
            $half_scale = 0;
        }

        // ラベルの表示インターバルを算出
        $interval = ceil($count / LINE_XLABEL_MAX); // 切り上げ
        for($i = 0; $i < $count; $i++) {
            // X軸に目盛りを入れる
            $x = $arrPointList[$i][0];
            $pos = $this->top + $this->area_height;
            imageline($this->image, $x - $half_scale, $pos, $x - $half_scale, $pos - LINE_SCALE_SIZE,  $this->flame_color);
            // ラベルを入れる
            if(($i % $interval) == 0) {
                $text_width = $this->getTextWidth($this->arrXLabel[$i], FONT_SIZE);
                $x_pos = $x;

                if ($bar) $bar_margin = -15;

                $this->setText(FONT_SIZE, $x_pos + $this->x_margin + $bar_margin, $pos + FONT_SIZE + $this->y_margin, $this->arrXLabel[$i], NULL, $this->XLabelAngle);
            }
        }

        // 棒グラフの場合は最後の目盛りを一つ追加する
        if($bar) {
            imageline($this->image, $x + $half_scale, $pos, $x + $half_scale, $pos - LINE_SCALE_SIZE,  $this->flame_color);
        }

        $this->drawXTitle();
    }

    // グリッド表示
    function setYGridOn($ygrid_on) {
        $this->ygrid_on = $ygrid_on;
    }

    // ポイントの描画
    function setMark($line_no, $left, $top, $size = LINE_MARK_SIZE) {
        // 偶数に変換しておく
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

    // Y軸目盛りに値を入れる
    function setYScale() {
        // 1目盛りの値
        $number = intval($this->graph_max / LINE_Y_SCALE);
        // 目盛り幅を求める
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
        // データの最大値を取得する。
        $data_max = max($arrData);
        // 10の何倍かを取得
        $figure = strlen($data_max) - 1;
        // 次の桁を計算する
        $tenval = pow(10, $figure);
        // グラフ上での最大値を求める
        $this->graph_max = $tenval * (intval($data_max / $tenval) + 1);
        // 最大値が10未満の場合の対応
        if($this->graph_max < 10) {
            $this->graph_max = 10;
        }
    }

    // グラフの描画
    function drawGraph() {
        // グラフ背景を描画
        $this->drawYLine();
        $this->drawXLine();

        // 折れ線グラフ描画
        for($i = 0; $i < $this->line_max; $i++) {
            $this->drawLine($i);
        }

        // マークを描画
        for($i = 0; $i < $this->line_max; $i++) {
            $this->drawMark($i);
        }

        // ラベルを描画
        for($i = 0; $i < $this->line_max; $i++) {
            $this->drawLabel($i);
        }

        // 凡例の描画
        $this->drawLegend();
    }

    // ラインを描画する
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

    // マークを描画する
    function drawMark($line_no) {
        $arrPointList = $this->arrPointList[$line_no];
        $count = count($arrPointList);
        for($i = 0; $i < $count; $i++) {
            $x = $arrPointList[$i][0];
            $y = $arrPointList[$i][1];
            $this->setMark($line_no, $x, $y);
        }
    }

    // ラベルを描画する
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

    // データをセットする
    function setData($arrData) {
        $this->arrDataList[$this->line_max] = array_values((array)$arrData);
        $this->setMax($this->arrDataList[$this->line_max]);
        // 値の描画変換率
        $rate = $this->area_height / $this->graph_max;
        // 描画率を計算
        $count = count($this->arrDataList[$this->line_max]);
        $scale_width = $this->area_width / ($count + 1);
        $this->arrPointList[$this->line_max] = array();
        for($i = 0; $i < $count; $i++) {
            // X座標を求める
            $x = intval($this->left + ($scale_width * ($i + 1)));
            // Y座標を求める
            $y = intval($this->top + $this->area_height - ($this->arrDataList[$this->line_max][$i] * $rate));
            // XY座標を保存する
            $this->arrPointList[$this->line_max][] = array($x, $y);
        }
        $this->line_max++;
    }

    // X軸ラベルをセットする
    function setXLabel($arrXLabel) {
        $this->arrXLabel = array_values((array)$arrXLabel);
    }

    // X軸タイトルをセットする
    function setXTitle($title) {
        $this->XTitle = $title;
    }

    // Y軸タイトルをセットする
    function setYTitle($title) {
        $this->YTitle = $title;
    }
}
?>
