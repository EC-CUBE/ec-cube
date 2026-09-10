<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\Pdf;

use Eccube\Service\Pdf\Font\JapaneseCidFont;
use setasign\Fpdi\Fpdi;

/**
 * FPDF を「描画命令の文字列を組み立てる部品」として使うための内部クラス.
 *
 * FPDF の protected（`_out()` `$fonts` `_newobj()` `_put()`）へ触るのはここだけ.
 * {@see PdfWriter} の private プロパティとしてのみ保持し, 外へは出さない.
 *
 * ## FPDF の Cell / MultiCell / Text は使わない
 *
 * FPDF は文字のベースラインを `y + 高さ/2 + 0.3 x フォントサイズ` に置く（係数は固定）.
 * 4.3 まで使っていた TCPDF は `y + (高さ + アセント - ディセント) / 2` で, アセント 880 /
 * ディセント -120 なら `y + 高さ/2 + 0.38 x フォントサイズ` になる.
 * この差はフォントサイズに比例し, 8pt で 0.23mm ずれる. 帳票の座標を変えないため,
 * セルの組み立ては {@see PdfWriter} が持ち, ここは命令の文字列化だけを担う.
 *
 * ## 日本語フォント
 *
 * FPDF は日本語を持たないので, Adobe-Japan1 の CID フォント（非埋め込み）を
 * `$fonts` へ直接登録し, `_putfonts()` が未知の種別を `_put<type>()` へ委譲する仕組みを使って
 * {@see _puttype0()} で書き出す. 定義は {@see JapaneseCidFont}.
 *
 * @internal {@see PdfWriter} からのみ使う
 */
class FpdfEngine extends Fpdi
{
    /** 非 null の間, _out() の出力をここへ溜める */
    private ?string $captured = null;

    /** 現在のフォントの PDF リソース番号（/F<n>） */
    private int $fontIndex = 1;

    /** 選択中のフォントサイズ(pt)。FPDF の $FontSizePt とは独立に持つ */
    private float $selectedSizePt = 12.0;

    /** 取り込んだテンプレートページの id（FPDI の $templateId とは別物） */
    private mixed $importedPageId = null;

    public function __construct()
    {
        parent::__construct();

        $this->SetAutoPageBreak(false);
        $this->SetCompression(true);
        $this->registerJapaneseFonts();
    }

    /**
     * ヘッダは使わない（帳票のヘッダはテンプレート PDF が持つ）.
     */
    #[\Override]
    public function Header(): void
    {
    }

    /**
     * フッタは {@see PdfWriter::renderFooter()} が自前で描く.
     */
    #[\Override]
    public function Footer(): void
    {
    }

    /**
     * pt を mm へ換算する.
     */
    public function toUnit(float $points): float
    {
        return $points / $this->k;
    }

    /**
     * テンプレート PDF を読み, 1 ページ目を取り込む.
     *
     * @return array{width: float, height: float} 用紙サイズ(mm)
     */
    public function loadTemplate(string $path): array
    {
        $this->setSourceFile($path);
        $this->importedPageId = $this->importPage(1);
        $size = $this->getTemplateSize($this->importedPageId);

        return ['width' => (float) $size['width'], 'height' => (float) $size['height']];
    }

    /**
     * ページを追加する.
     */
    public function beginPage(float $width, float $height): void
    {
        $this->AddPage('P', [$width, $height]);
    }

    /**
     * 取り込んだテンプレートを現在のページへ重ねる.
     */
    public function overlayTemplate(float $width, float $height): void
    {
        if ($this->importedPageId === null) {
            return;
        }

        $this->useTemplate($this->importedPageId, 0, 0, $width, $height);
    }

    /**
     * 命令列を現在のページへ書き出す.
     */
    public function addContent(string $ops): void
    {
        if ($ops === '') {
            return;
        }

        $this->_out(rtrim($ops, "\n"));
    }

    /**
     * PDF のバイナリを返す.
     */
    public function render(): string
    {
        return $this->Output('S');
    }

    /**
     * フォントを選び, アセント・ディセント(pt)を返す.
     *
     * 命令は出さない（{@see getTextLine()} が毎回 `Tf` を含めるため）.
     *
     * @return array{ascent: float, descent: float}
     */
    public function selectFont(string $family, string $style, float $sizePt): array
    {
        $key = strtolower($family).strtoupper($style);
        if (!isset($this->fonts[$key])) {
            $this->Error(sprintf('未登録のフォントです: %s (%s)', $family, $style));
        }

        $this->fontIndex = (int) $this->fonts[$key]['i'];
        $this->selectedSizePt = $sizePt;

        return [
            'ascent' => JapaneseCidFont::ASCENT * $sizePt / 1000,
            'descent' => JapaneseCidFont::DESCENT * $sizePt / 1000,
        ];
    }

    /**
     * 文字列の幅(pt)を返す.
     */
    public function getStringWidthPt(string $text): float
    {
        return JapaneseCidFont::stringWidth($text) * $this->selectedSizePt / 1000;
    }

    /**
     * 1 行のテキストを描く命令を返す.
     *
     * 座標は mm・左上原点. `$baseline` はベースラインの y.
     * フォント選択を同じ `q ... Q` の中に含めることで, 命令列を単独で完結させる.
     */
    public function getTextLine(string $text, float $x, float $baseline, string $colorHex): string
    {
        if ($text === '') {
            return '';
        }

        $encoded = mb_convert_encoding($text, 'UTF-16BE', 'UTF-8');

        return sprintf(
            "q %s BT /F%d %.2F Tf %.6F %.6F Td (%s) Tj ET Q\n",
            self::fillColorCmd($colorHex),
            $this->fontIndex,
            $this->selectedSizePt,
            $x * $this->k,
            ($this->h - $baseline) * $this->k,
            $this->_escape($encoded)
        );
    }

    /**
     * 矩形を描く命令を返す.
     *
     * @param string               $mode  'f' = 塗り / 'S' = 線
     * @param array<string, mixed> $style 'fillColor' / 'lineColor' / 'lineWidth'
     */
    public function getBasicRect(float $x, float $y, float $width, float $height, string $mode, array $style = []): string
    {
        $shape = sprintf(
            '%.6F %.6F %.6F %.6F re %s',
            $x * $this->k,
            ($this->h - $y) * $this->k,
            $width * $this->k,
            -$height * $this->k,
            $mode
        );

        return sprintf("q %s%s Q\n", $this->getStyleCmd($mode, $style), $shape);
    }

    /**
     * 線を描く命令を返す.
     *
     * @param array<string, mixed> $style
     */
    public function getLine(float $x1, float $y1, float $x2, float $y2, array $style = []): string
    {
        $shape = sprintf(
            '%.6F %.6F m %.6F %.6F l S',
            $x1 * $this->k,
            ($this->h - $y1) * $this->k,
            $x2 * $this->k,
            ($this->h - $y2) * $this->k
        );

        return sprintf("q %s%s Q\n", $this->getStyleCmd('S', $style), $shape);
    }

    /**
     * 画像を配置する命令を返す.
     *
     * PNG/JPEG の解析・アルファの分離・XObject の登録は FPDF に任せ,
     * ページへ直接書かれる命令だけを取り出す.
     */
    public function getSetImage(string $file, float $x, float $y, float $width, float $height, string $type = ''): string
    {
        return $this->capture(function () use ($file, $x, $y, $width, $height, $type): void {
            $this->Image($file, $x, $y, $width, $height, $type);
        });
    }

    /**
     * $draw の実行中に FPDF が出した命令列を返す（ページへは書かない）.
     */
    private function capture(callable $draw): string
    {
        $previous = $this->captured;
        $this->captured = '';

        try {
            $draw();
            $ops = $this->captured;
        } finally {
            $this->captured = $previous;
        }

        return $ops;
    }

    /**
     * FPDF の唯一の出力口. capture 中はページへ書かずに溜める.
     *
     * FPDI の `FpdfTpl` が public で上書きしているため public のままにする.
     *
     * 親（FPDF）が型宣言を持たないため, 引数の型は PHPDoc で示す.
     *
     * @param string $s
     *
     * @internal FPDF の内部呼び出し用. 外から使わない（命令の書き出しは {@see addContent()}）
     */
    #[\Override]
    public function _out($s): void
    {
        if ($this->captured !== null) {
            $this->captured .= $s."\n";

            return;
        }

        parent::_out($s);
    }

    /**
     * グラフィック状態（色・線幅・線端）の命令を返す.
     *
     * 線端は TCPDF の既定と同じ square（`2 J`）を明示する.
     * butt にすると罫線の両端が線幅の半分ずつ短くなり, 表の角が欠ける.
     *
     * @param array<string, mixed> $style
     */
    private function getStyleCmd(string $mode, array $style): string
    {
        $cmd = '';
        if ($mode === 'f' && isset($style['fillColor'])) {
            $cmd .= self::fillColorCmd((string) $style['fillColor']);
        }
        if ($mode === 'S') {
            $cmd .= '2 J ';
            if (isset($style['lineColor'])) {
                $cmd .= self::strokeColorCmd((string) $style['lineColor']);
            }
            if (isset($style['lineWidth'])) {
                $cmd .= sprintf('%.6F w ', (float) $style['lineWidth'] * $this->k);
            }
        }

        return $cmd;
    }

    /**
     * 塗り色の命令（`rg`）.
     */
    private static function fillColorCmd(string $hex): string
    {
        [$r, $g, $b] = self::rgb($hex);

        return sprintf('%.3F %.3F %.3F rg ', $r, $g, $b);
    }

    /**
     * 線色の命令（`RG`）.
     */
    private static function strokeColorCmd(string $hex): string
    {
        [$r, $g, $b] = self::rgb($hex);

        return sprintf('%.3F %.3F %.3F RG ', $r, $g, $b);
    }

    /**
     * `#rrggbb` を 0..1 の三つ組へ.
     *
     * @return array{float, float, float}
     */
    private static function rgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6) {
            return [0.0, 0.0, 0.0];
        }

        return [
            hexdec(substr($hex, 0, 2)) / 255,
            hexdec(substr($hex, 2, 2)) / 255,
            hexdec(substr($hex, 4, 2)) / 255,
        ];
    }

    /**
     * 日本語フォント（CID0・非埋め込み）を FPDF のフォント表へ登録する.
     */
    private function registerJapaneseFonts(): void
    {
        foreach (JapaneseCidFont::faces() as $key => $face) {
            $this->fonts[$key] = [
                'i' => count($this->fonts) + 1,
                'type' => 'Type0',
                'name' => $face['name'],
                'desc' => $face['desc'],
                'up' => -120,
                'ut' => 40,
                'cw' => JapaneseCidFont::LATIN_WIDTHS,
                'subsetted' => false,
            ];
        }
    }

    /**
     * CID フォント（Adobe-Japan1・非埋め込み）を書き出す.
     *
     * FPDF の `_putfonts()` が未知のフォント種別を `_put<type>()` へ委譲するのを使う.
     * `/W` は Adobe-Japan1 の CID 1..95 が U+0020..U+007E に対応することを前提に,
     * 欧文 95 文字の幅を並べる. それ以外は `/DW` が使われる.
     *
     * @param array<string, mixed> $font
     */
    protected function _puttype0(array $font): void
    {
        $name = (string) $font['name'];

        // 親フォント（Type0）
        $this->_newobj();
        $this->_put('<</Type /Font');
        $this->_put('/Subtype /Type0');
        $this->_put('/BaseFont /'.$name.'-UniJIS-UCS2-H');
        $this->_put('/Encoding /UniJIS-UCS2-H');
        $this->_put('/DescendantFonts ['.($this->n + 1).' 0 R]');
        $this->_put('>>');
        $this->_put('endobj');

        // 子フォント（CIDFontType0）
        $this->_newobj();
        $this->_put('<</Type /Font');
        $this->_put('/Subtype /CIDFontType0');
        $this->_put('/BaseFont /'.$name);
        $this->_put('/CIDSystemInfo <</Registry (Adobe) /Ordering (Japan1) /Supplement 4>>');
        $this->_put('/FontDescriptor '.($this->n + 1).' 0 R');
        $this->_put('/DW '.JapaneseCidFont::DEFAULT_WIDTH);
        $this->_put('/W [1 ['.implode(' ', $font['cw']).']]');
        $this->_put('>>');
        $this->_put('endobj');

        // フォント記述子
        $this->_newobj();
        $descriptor = '<</Type /FontDescriptor /FontName /'.$name;
        foreach ($font['desc'] as $key => $value) {
            $descriptor .= ' /'.$key.' '.$value;
        }
        $this->_put($descriptor.'>>');
        $this->_put('endobj');
    }
}
