<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/** TTFフォントファイル */
define("FONT_PATH", DATA_PATH . "fonts/wlmaru20044.ttf");

/** フォントサイズ */
define("FONT_SIZE", 8);

/** タイトルフォントサイズ */
define("TITLE_FONT_SIZE", 11);

/** 背景幅 */
define("BG_WIDTH", 720);

/** 背景高さ */
define("BG_HEIGHT", 400);

/** 行間 */
define("LINE_PAD", 5);

/** フォント補正値(実際の描画幅/フォントサイズ) */
define("TEXT_RATE", 0.75);

// -----------------------------------------------------------------------------
// 円グラフ
// -----------------------------------------------------------------------------
/** 円グラフ位置 */
define("PIE_LEFT", 200);

/** 円グラフ位置 */
define("PIE_TOP", 150);

/** 円グラフ幅 */
define("PIE_WIDTH", 230);

/** 円グラフ高さ */
define("PIE_HEIGHT", 100);

/** 円グラフ太さ */
define("PIE_THICK", 30);

/** 円グラフのラベル位置を上にあげる */
define("PIE_LABEL_UP", 20);

/** 値が大きいほど影が長くなる */
define("PIE_SHADE_IMPACT", 0.1);

// -----------------------------------------------------------------------------
// 折れ線グラフ
// -----------------------------------------------------------------------------
/** Y軸の目盛り数 */
define("LINE_Y_SCALE", 10);

/** X軸の目盛り数 */
define("LINE_X_SCALE", 10);

/** 線グラフ位置 */
define("LINE_LEFT", 60);

/** 線グラフ位置 */
define("LINE_TOP", 50);

/** 線グラフ背景のサイズ */
define("LINE_AREA_WIDTH", 600);

/** 線グラフ背景のサイズ */
define("LINE_AREA_HEIGHT", 300);

/** 線グラフマークのサイズ */
define("LINE_MARK_SIZE", 6);

/** 目盛り幅 */
define("LINE_SCALE_SIZE", 6);

/** X軸のラベルの表示制限数 */
define("LINE_XLABEL_MAX", 30);

/** X軸のタイトルと軸の間隔 */
define("LINE_XTITLE_PAD", -5);

/** Y軸のタイトルと軸の間隔 */
define("LINE_YTITLE_PAD", 15);

// -----------------------------------------------------------------------------
//  棒グラフ
// -----------------------------------------------------------------------------
/** グラフと目盛りの間隔 */
define("BAR_PAD", 6);

// -----------------------------------------------------------------------------
//  タイトルラベル
// -----------------------------------------------------------------------------
/** 背景枠との上幅 */
define("TITLE_TOP", 10);

// -----------------------------------------------------------------------------
//  凡例
// -----------------------------------------------------------------------------
/** 背景枠との上幅 */
define("LEGEND_TOP", 10);

/** 背景枠との右幅 */
define("LEGEND_RIGHT", 10);

/**
 * SC_Graph共通クラス.
 *
 * @package Graph
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_GraphBase {

    // {{{ properties

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

    /** グラフ背景 */
    var $ARR_GRAPH_RGB;

    /** 背景色 */
    var $ARR_BG_COLOR;

    /** 影の色 */
    var $ARR_SHADE_COLOR;

    /** 縁の色 */
    var $ARR_FLAME_COLOR;

    /** 文字色 */
    var $ARR_TEXT_COLOR;

    /** ラベル背景 */
    var $ARR_LABELBG_COLOR;

    /** 凡例背景 */
    var $ARR_LEGENDBG_COLOR;

    /** タイトル文字色 */
    var $ARR_TITLE_COLOR;

    /** グリッド線色 */
    var $ARR_GRID_COLOR;

    // コンストラクタ
    function SC_GraphBase($bgw = BG_WIDTH, $bgh = BG_HEIGHT, $left, $top) {
        $this->init();
        // 画像作成
        $this->bgw = $bgw;
        $this->bgh = $bgh;
        $this->image = imagecreatetruecolor($bgw, $bgh);
        // アンチエイリアス有効
        if (function_exists("imageantialias")) imageantialias($this->image, true);
        // 背景色をセット
        imagefill($this->image, 0, 0, $this->lfGetImageColor($this->image, $this->ARR_BG_COLOR));

        // 使用色の生成
        $this->setColorList($this->ARR_GRAPH_RGB);
        // グラフ描画位置の設定
        $this->left = $left;
        $this->top = $top;
        $this->shade_color = $this->lfGetImageColor($this->image, $this->ARR_SHADE_COLOR);
        $this->flame_color = $this->lfGetImageColor($this->image, $this->ARR_FLAME_COLOR);
        $this->text_color = $this->lfGetImageColor($this->image, $this->ARR_TEXT_COLOR);
        $this->labelbg_color = $this->lfGetImageColor($this->image, $this->ARR_LABELBG_COLOR);
        $this->clabelbg_color = $this->lfGetImageColor($this->image, $this->ARR_LEGENDBG_COLOR);
        $this->title_color = $this->lfGetImageColor($this->image, $this->ARR_TITLE_COLOR);
        $this->grid_color = $this->lfGetImageColor($this->image, $this->ARR_GRID_COLOR);

        // 影あり
        $this->shade_on = true;
    }

    // リサンプル(画像を滑らかに縮小する)
    function resampled() {
        $new_width = $this->bgw * 0.8;
        $new_height = $this->bgh * 0.8;
        $tmp_image = imagecreatetruecolor($new_width, $new_height);
        if(imagecopyresampled($tmp_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->bgw, $this->bgh)) {
            $this->image = $tmp_image;
        }
    }


    // オブジェクトカラーの設定
    function setColorList($arrRGB) {
        $this->arrRGB = $arrRGB;
        $count = count($this->arrRGB);
        // 通常色の設定
        for($i = 0; $i < $count; $i++) {
            $this->arrColor[$i] = $this->lfGetImageColor($this->image, $this->arrRGB[$i]);
        }
        // 暗色の設定
        for($i = 0; $i < $count; $i++) {
            $this->arrDarkColor[$i] = $this->lfGetImageDarkColor($this->image, $this->arrRGB[$i]);
        }
    }

    // 影のありなし
    function setShadeOn($shade_on) {
        $this->shade_on = $shade_on;
    }

    // 画像を出力する
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

    // 描画時のテキスト幅を求める
    function getTextWidth($text, $font_size) {
        $text_len = strlen($text);
        $ret = $font_size * $text_len * TEXT_RATE;
        /*
            ※正確な値が取得できなかったので廃止
            // テキスト幅の取得
            $arrPos = imagettfbbox($font_size, 0, FONT_PATH, $text);
            $ret = $arrPos[2] - $arrPos[0];
        */
        return $ret;
    }

    // テキストを出力する
    function setText($font_size, $left, $top, $text, $color = NULL, $angle = 0, $labelbg = false) {
        // 時計回りに角度を変更
        $angle = -$angle;
        // ラベル背景
        if($labelbg) {
            $text_width = $this->getTextWidth($text, $font_size);
            imagefilledrectangle($this->image, $left - 2, $top - 2, $left + $text_width + 2, $top + $font_size + 2, $this->labelbg_color);
        }
        /*
         * XXX EUC-JP にしないと Warning がでる.
         *     --enable-gd-jis-conv も関係していそうだが, このオプションを
         *     つけなくても出る.
         *
         *     Warning: imagettftext() [function.imagettftext]:
         *     any2eucjp(): something happen in
         *
         *     http://www.php.net/imagettftext を見ると, UTF-8 にしろと
         *     書いてあるのに何故？
         */
        $text = mb_convert_encoding($text, "EUC-JP", CHAR_CODE);
        //$text = mb_convert_encoding($text, CHAR_CODE);
        if($color != NULL) {
            ImageTTFText($this->image, $font_size, $angle, $left, $top + $font_size, $color, FONT_PATH, $text);
        } else {
            ImageTTFText($this->image, $font_size, $angle, $left, $top + $font_size, $this->text_color, FONT_PATH, $text);
        }
    }

    // タイトルを出力する
    function drawTitle($text, $font_size = TITLE_FONT_SIZE) {
        // 出力位置の算出
        $text_width = $this->getTextWidth($text, $font_size);
        $left = ($this->bgw - $text_width) / 2;
        $top = TITLE_TOP;
        $this->setText($font_size, $left, $top, $text, $this->title_color);
    }

    // ログを出力する
    function debugPrint($text) {
        $text = mb_convert_encoding($text, "UTF-8", CHAR_CODE);
        if(!isset($this->text_top)) {
            $this->text_top = FONT_SIZE + LINE_PAD;
        }
        // テキスト描画
        ImageTTFText($this->image, FONT_SIZE, 0, LINE_PAD, $this->text_top, $this->text_color, FONT_PATH, $text);
        $this->text_top += FONT_SIZE + LINE_PAD;
    }

    // カラーラベルを描画
    function drawLegend($legend_max = "", $clabelbg = true) {
        // 凡例が登録されていなければ中止
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

        // 一番文字数が多いものを取得
        for($i = 0; $i < $label_max; $i++) {
            $text_len = strlen($this->arrLegend[$i]);
            if($text_max < $text_len) {
                $text_max = $text_len;
            }
            $height_max += FONT_SIZE + LINE_PAD;
        }
        $width_max = FONT_SIZE * $text_max * TEXT_RATE;

        //  カラーアイコンと文字間を含めた幅
        $width_max += FONT_SIZE + (LINE_PAD * 2);
        $left = $this->bgw - $width_max - LEGEND_RIGHT;
        $top = LEGEND_TOP;
        // カラーラベル背景の描画
        if($clabelbg) {
            $this->drawClabelBG($left - LINE_PAD, $top, $left + $width_max, $top + $height_max + LINE_PAD);
        }
        $top += LINE_PAD;

        // 色数の取得
        $c_max = count($this->arrColor);
        for($i = 0; $i < $label_max; $i++) {
            // カラーアイコンの表示
            imagerectangle($this->image, $left, $top, $left + FONT_SIZE, $top + FONT_SIZE, $this->flame_color);
            imagefilledrectangle($this->image, $left + 1, $top + 1, $left + FONT_SIZE - 1, $top + FONT_SIZE - 1, $this->arrColor[($i % $c_max)]);
            // ラベルの表示
            $this->setText(FONT_SIZE, $left + FONT_SIZE + LINE_PAD, $top, $this->arrLegend[$i]);
            $top += FONT_SIZE + LINE_PAD;
        }
    }

    // カラーラベル背景の描画
    function drawClabelBG($left, $top, $right, $bottom) {
        // 影の描画
        if($this->shade_on) {
            imagefilledrectangle($this->image, $left + 2, $top + 2, $right + 2, $bottom + 2, $this->shade_color);
        }
        // カラーラベル背景の描画
        imagefilledrectangle($this->image, $left, $top, $right, $bottom, $this->clabelbg_color);
        imagerectangle($this->image, $left, $top, $right, $bottom, $this->flame_color);
    }

    // 凡例をセットする
    function setLegend($arrLegend) {
        $this->arrLegend = array_values((array)$arrLegend);
    }

    // }}}
    // {{{ protected functions

    /**
     * クラスの初期化を行う.
     *
     * 表示色をメンバ変数にセットする.
     *
     * @access protected
     * @return void
     */
    function init() {
        // 凡例背景
        $this->ARR_LEGENDBG_COLOR = array(245,245,245);
        // ラベル背景
        $this->ARR_LABELBG_COLOR = array(255,255,255);
        // グラフカラー
        $this->ARR_GRAPH_RGB = array(
                               array(200,50,50),
                               array(50,50,200),
                               array(50,200,50),
                               array(255,255,255),
                               array(244,200,200),
                               array(200,200,255),
                               array(50,200,50),
                               array(255,255,255),
                               array(244,244,244),
                               );
        // 影の色
        $this->ARR_SHADE_COLOR = array(100,100,100);
        // 縁の色
        $this->ARR_FLAME_COLOR = array(0, 0, 0);
        // 文字色
        $this->ARR_TEXT_COLOR = array(0, 0, 0);
        // 背景カラー
        $this->ARR_BG_COLOR = array(255,255,255);
        // タイトル文字色
        $this->ARR_TITLE_COLOR = array(0, 0, 0);
        // グリッド線色
        $this->ARR_GRID_COLOR = array(200, 200, 200);
        // マークの色
        $this->ARR_MARK_COLOR = array(130, 130, 255);
    }

    /**
     * 円の中心点と直径から弧の終端座標を算出する.
     *
     * @param integer $cx 中心点X座標
     * @param integer $cy 中心点Y座標
     * @param integer $r 半径
     * @param integer $e 角度
     * @return array 円の中心点と直径から弧の終端座標の配列
     */
    function lfGetArcPos($cx, $cy, $cw, $ch, $e) {
        // 三角関数用の角度を求める
        $s = 90 - $e;
        $r = $cw / 2;
        // 位置を求める
        $x = $cx + ($r * cos(deg2rad($s)));
        $y = $cy - (($r * sin(deg2rad($s))) * ($ch / $cw));
        return array(round($x), round($y));
    }

    /** 画像にテキストを描画する */
    function lfImageText($dst_image, $text, $font_size, $left, $top, $font, $arrRGB) {
        $color = ImageColorAllocate($dst_image, $arrRGB[0], $arrRGB[1], $arrRGB[2]);
        $text = mb_convert_encoding($text, "UTF-8", CHAR_CODE);
        // 表示角度
        $angle = 0;
        // テキスト描画
        ImageTTFText($dst_image, $font_size, $angle, $left, $top, $color, $font, $text);
    }

    /** 表示色の取得 */
    function lfGetImageColor($image, $array) {
        if(count($array) != 3) {
            return NULL;
        }
        $ret = imagecolorallocate($image, $array[0], $array[1], $array[2]);
        return $ret;
    }

    /** 影用表示色の取得 */
    function lfGetImageDarkColor($image, $array) {
        if(count($array) != 3) {
            return NULL;
        }
        $i = 0;
        foreach($array as $val) {
            $dark[$i] = $val - 45;
            if($dark[$i] < 0) {
                $dark[$i] = 0;
            }
            $i++;
        }
        $ret = imagecolorallocate($image, $dark[0], $dark[1], $dark[2]);
        return $ret;
    }
}
?>
