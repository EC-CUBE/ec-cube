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

namespace Eccube\Framework\Helper;

// japanese.php のバグ回避
$GLOBALS['SJIS_widths'] = $SJIS_widths;

class FpdiHelper extends \PDF_Japanese
{
    /** SJIS 変換を有効とするか */
    public $enable_conv_sjis = true;

    /**
     * PDF_Japanese の明朝フォントに加えゴシックフォントを追加定義
     *
     * @return void
     */
    public function AddSJISFont()
    {
        parent::AddSJISFont();
        $cw = $GLOBALS['SJIS_widths'];
        $c_map = '90msp-RKSJ-H';
        $registry = array('ordering'=>'Japan1','supplement'=>2);
        $this->AddCIDFonts('Gothic', 'KozGoPro-Medium-Acro,MS-PGothic,Osaka', $cw, $c_map, $registry);
    }

    public function SJISMultiCell()
    {
        $arrArg = func_get_args();

        // $text
        $arrArg[2] = $this->lfConvSjis($arrArg[2]);

        $bak = $this->enable_conv_sjis;
        $this->enable_conv_sjis = false;

        call_user_func_array(array(parent, 'SJISMultiCell'), $arrArg);

        $this->enable_conv_sjis = $bak;
    }

    /**
     * Colored table
     *
     * FIXME: 後の列の高さが大きい場合、表示が乱れる。
     */
    public function FancyTable($header, $data, $w)
    {
        $base_x = $this->x;
        // Colors, line width and bold font
        $this->SetFillColor(216, 216, 216);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');
        // Header
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(235, 235, 235);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        $h = 4;
        foreach ($data as $row) {
            $x = $base_x;
            $h = 4;
            $i = 0;
            // XXX この処理を消すと2ページ目以降でセルごとに改ページされる。
            $this->Cell(0, $h, '', 0, 0, '', 0, '');
            foreach ($row as $col) {
                // 列位置
                $this->x = $x;
                // FIXME 汎用的ではない処理。この指定は呼び出し元で行うようにしたい。
                if ($i == 0) {
                    $align = 'L';
                } else {
                    $align = 'R';
                }
                $y_before = $this->y;
                $h = $this->SJISMultiCell($w[$i], $h, $col, 1, $align, $fill, 0);
                $h = $this->y - $y_before;
                $this->y = $y_before;
                $x += $w[$i];
                $i++;
            }
            $this->Ln();
            $fill = !$fill;
        }
        $this->SetFillColor(255);
        $this->x = $base_x;
    }

    /**
     * @param integer $x
     * @param integer $y
     */
    public function Text($x, $y, $txt)
    {
        parent::Text($x, $y, $this->lfConvSjis($txt));
    }

    public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        parent::Cell($w, $h, $this->lfConvSjis($txt), $border, $ln, $align, $fill, $link);
    }

    // 文字コードSJIS変換 -> japanese.phpで使用出来る文字コードはSJIS-winのみ
    public function lfConvSjis($conv_str)
    {
        if ($this->enable_conv_sjis) {
            $conv_str = mb_convert_encoding($conv_str, 'SJIS-win', CHAR_CODE);
        }

        return $conv_str;
    }
}
