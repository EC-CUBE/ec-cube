<?php
require DATA_REALDIR . 'module/fpdf/fpdf.php';
require DATA_REALDIR . 'module/fpdi/japanese.php';

// japanese.php のバグ回避
$GLOBALS[SJIS_widths] = $SJIS_widths;

class SC_Helper_FPDI extends PDF_Japanese {
    /**
     * PDF_Japanese の明朝フォントに加えゴシックフォントを追加定義
     *
     * @return void
     */
    function AddSJISFont() {
        parent::AddSJISFont();
        $cw = $GLOBALS['SJIS_widths'];
        $c_map = '90msp-RKSJ-H';
        $registry = array('ordering'=>'Japan1','supplement'=>2);
        $this->AddCIDFonts('Gothic', 'KozGoPro-Medium-Acro,MS-PGothic,Osaka', $cw, $c_map, $registry);
    }

    /**
     * FancyTable から利用するための SJISMultiCell
     *
     * PDF_Japanese#SJISMultiCell をベースにカスタマイズ。
     */
    function SJISMultiCellForFancyTable($w, $h, $txt, $border = 0, $align = 'L', $fill = 0) {
        $y = $this->y;

        // ここで SJIS に変換する。そのため、このメソッドの中では、PDF_Japanese#Cell を直接呼ぶ。
        $txt = $this->lfConvSjis($txt);
        // Output text with automatic or explicit line breaks
        $cw =& $this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $b = 0;
        if ($border) {
            if ($border == 1) {
                $border = 'LTRB';
                $b = 'LRT';
                $b2 = 'LR';
            }
            else {
                $b2 = '';
                if (is_int(strpos($border, 'L')))
                    $b2 .= 'L';
                if (is_int(strpos($border, 'R')))
                    $b2 .= 'R';
                $b = is_int(strpos($border, 'T')) ? $b2.'T' : $b2;
            }
        }
        $sep =- 1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        $rise_h = $h; // 高さ計算用

        while ($i < $nb) {
            // Get next character
            $c = $s{$i};
            $o = ord($c);
            if ($o == 10) {
                // Explicit line break
                parent::Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                $rise_h += $h; // 高さ計算用
                if ($border && $nl == 2) {
                    $b=$b2;
                }
                continue;
            }
            if ($o < 128) {
                // ASCII
                $l += $cw[$c];
                $n = 1;
                if ($o == 32) {
                    $sep=$i;
                }
            }
            elseif($o >= 161 && $o <= 223) {
                // Half-width katakana
                $l += 500;
                $n = 1;
                $sep = $i;
            }
            else {
                // Full-width character
                $l += 1000;
                $n = 2;
                $sep = $i;
            }
            if ($l > $wmax) {
                // Automatic line break
                if ($sep == -1 || $i == $j) {
                    if ($i == $j) {
                        $i += $n;
                    }
                    parent::Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
                }
                else {
                    parent::Cell($w, $h, substr($s, $j, $sep - $j), $b, 2, $align, $fill);
                    $i = ($s[$sep] == ' ') ? $sep + 1 : $sep;
                }
                $rise_h += $h; // 高さ計算用
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                if ($border && $nl == 2) {
                    $b = $b2;
                }
            }
            else {
                $i += $n;
                if ($o >= 128) {
                    $sep = $i;
                }
            }
        }
        // Last chunk
        if ($border && is_int(strpos($border, 'B'))) {
            $b .= 'B';
        }
        parent::Cell($w, $h, substr($s, $j, $i - $j), $b, 0, $align, $fill);
        // メソッド内でY軸を増す操作を行う場合があるので戻す。
        $this->y = $y;

        return $rise_h;
    }

    /**
     * Colored table
     *
     * FIXME: 後の列の高さが大きい場合、表示が乱れる。
     */
    function FancyTable($header, $data, $w) {
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
        $fill = 0;
        $h = 4;
        foreach ($data as $row) {
            $h = 4;
            $i = 0;
            $y = $this->y;
            $this->Cell(5, $h, '', 0, 0, '', 0, '');
            foreach ($row as $col) {
                $this->y = $y;
                // FIXME 汎用的ではない処理。この指定は呼び出し元で行うようにしたい。
                if ($i == 0) {
                    $align = 'L';
                } else {
                    $align = 'R';
                }
                $h = $this->SJISMultiCellForFancyTable($w[$i], $h, $col, 1, $align, $fill, 0);
                $i++;
            }
            $this->Ln();
            $fill = !$fill;
        }
        $this->Cell(5, $h, '', 0, 0, '', 0, '');
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->SetFillColor(255);
    }

    function Text($x, $y, $txt) {
        parent::Text($x, $y, $this->lfConvSjis($txt));
    }

    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
        parent::Cell($w, $h, $this->lfConvSjis($txt), $border, $ln, $align, $fill, $link);
    }

    // 文字コードSJIS変換 -> japanese.phpで使用出来る文字コードはSJIS-winのみ
    function lfConvSjis($conv_str) {
        return mb_convert_encoding($conv_str, 'SJIS-win', CHAR_CODE);
    }
}
