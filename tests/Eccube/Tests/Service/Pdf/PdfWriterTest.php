<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Service\Pdf;

use Eccube\Service\Pdf\PdfWriter;
use PHPUnit\Framework\TestCase;

/**
 * 帳票の座標が動いていないことを検証する.
 *
 * 納品書は座標をハードコードした帳票で, セルの高さ・ベースライン・パディングの
 * どれか一つが変わるだけで印字位置がずれる. ここではその計算規則を固定する.
 */
final class PdfWriterTest extends TestCase
{
    /** 1mm あたりのポイント数 */
    private const POINTS_PER_MM = 72 / 25.4;

    /** テストで使うフォントのアセント・ディセント(千分率). kozgopromedium / kozminproregular 共通 */
    private const FONT_ASCENT = 0.88;
    private const FONT_DESCENT = 0.12;

    private static function projectDir(): string
    {
        return \dirname(__DIR__, 5);
    }

    private function createWriter(): PdfWriter
    {
        return new PdfWriter();
    }

    /**
     * 用紙サイズはテンプレート PDF に合わせること.
     *
     * 納品書のテンプレートは A4 ちょうど(210x297mm)ではない. ここが A4 固定に戻ると
     * 紙面下端を基準にする描画（フッタ・改ページ）が全てずれる.
     */
    public function testPageSizeFollowsTemplate(): void
    {
        $writer = $this->createWriter();
        $writer->setTemplateFile(self::projectDir().'/html/template/admin/assets/pdf/nouhinsyo.pdf');

        // テンプレートの MediaBox は 595.2 x 841.68pt
        $this->assertEqualsWithDelta(209.9733, $writer->getPageWidth(), 0.001);
        $this->assertEqualsWithDelta(296.9260, $writer->getPageHeight(), 0.001);
        // 自動改ページは用紙下端から 20mm
        $this->assertEqualsWithDelta(276.9260, $writer->getPageBreakTrigger(), 0.001);
    }

    /**
     * 文字はセル内で上下中央に置かれ, 左は既定のパディング分だけ内側へ入ること.
     */
    public function testCellPlacesTextAtVerticalCenter(): void
    {
        $writer = $this->createWriter();
        $writer->setMargins(15, 20);
        $writer->setFont('kozgopromedium', '', 15);
        $writer->addPage();
        $writer->setXY(15, 20);
        $writer->cell(80, 10, 'あ', 0, 0, 'L');

        [$x, $y] = $this->firstTextPosition($writer);

        $this->assertEqualsWithDelta(15 + PdfWriter::CELL_PADDING_X, $x, 0.01);
        $this->assertEqualsWithDelta(20 + $this->baselineOffset(10.0, 15.0), $y, 0.01);
    }

    /**
     * 右寄せは「セル右端 - パディング - 文字幅」に揃うこと.
     */
    public function testCellAlignsRight(): void
    {
        $writer = $this->createWriter();
        $writer->setMargins(15, 20);
        $writer->setFont('kozgopromedium', '', 15);
        $writer->addPage();
        $writer->setXY(15, 20);
        $writer->cell(80, 10, 'あ', 0, 0, 'R');

        [$x] = $this->firstTextPosition($writer);

        $expected = 15 + 80 - PdfWriter::CELL_PADDING_X - $writer->getStringWidth('あ');
        $this->assertEqualsWithDelta($expected, $x, 0.01);
    }

    /**
     * セルの高さは指定値とフォントサイズ由来の下限の大きい方になること.
     */
    public function testCellHeightHasFontSizedMinimum(): void
    {
        $writer = $this->createWriter();
        $writer->setMargins(15, 20);
        $writer->setFont('kozgopromedium', '', 15);
        $writer->addPage();
        $writer->setXY(15, 20);

        // 指定 0 でも フォントサイズ x CELL_HEIGHT_RATIO まで広がる
        $writer->cell(80, 0, '', 0, 2);
        $minimum = ($writer->getFontSize() * PdfWriter::CELL_HEIGHT_RATIO);
        $this->assertEqualsWithDelta($minimum, $writer->getLastCellHeight(), 0.001);
        $this->assertEqualsWithDelta(20 + $minimum, $writer->getY(), 0.001);
    }

    /**
     * カーソルの進み方: 0=右へ / 1=次行の左端へ / 2=真下へ.
     */
    public function testCursorAdvance(): void
    {
        $writer = $this->createWriter();
        $writer->setMargins(15, 20);
        $writer->setFont('kozgopromedium', '', 15);
        $writer->addPage();

        $writer->setXY(30, 50);
        $writer->cell(40, 10, '', 0, 0);
        $this->assertSame([70.0, 50.0], [$writer->getX(), $writer->getY()]);

        $writer->setXY(30, 50);
        $writer->cell(40, 10, '', 0, 1);
        $this->assertSame([15.0, 60.0], [$writer->getX(), $writer->getY()]);

        $writer->setXY(30, 50);
        $writer->cell(40, 10, '', 0, 2);
        $this->assertSame([30.0, 60.0], [$writer->getX(), $writer->getY()]);
    }

    /**
     * 折り返しセルの高さは行数ぶんに広がること. 末尾の改行は行数に数えない.
     */
    public function testMultiCellHeightGrowsWithLineCount(): void
    {
        $writer = $this->createWriter();
        $writer->setMargins(15, 20);
        $writer->setFont('kozminproregular', '', 8);
        $writer->addPage();
        $lineHeight = $writer->getFontSize() * PdfWriter::CELL_HEIGHT_RATIO;

        $writer->setXY(15, 20);
        $writer->multiCell(80, 4, "1行目\n2行目\n3行目", 0, 'L', false, 1);
        $this->assertEqualsWithDelta(3 * $lineHeight, $writer->getLastCellHeight(), 0.001);

        // 末尾の改行で 1 行増えてはいけない（4.3 までの挙動）
        $writer->setXY(15, 20);
        $writer->multiCell(80, 4, "1行目\n", 0, 'L', false, 1);
        $this->assertEqualsWithDelta(4.0, $writer->getLastCellHeight(), 0.001, '1行なので最小高さのまま');
    }

    /**
     * 幅に収まらない文字列は折り返され, 行数ぶん高さが増えること.
     */
    public function testMultiCellWrapsLongText(): void
    {
        $writer = $this->createWriter();
        $writer->setMargins(15, 20);
        $writer->setFont('kozminproregular', '', 8);
        $writer->addPage();
        $lineHeight = $writer->getFontSize() * PdfWriter::CELL_HEIGHT_RATIO;

        $writer->setXY(15, 20);
        // 全角 20 文字 = 8pt x 20 で約 56.4mm. 幅 30mm なら 2 行以上になる
        $writer->multiCell(30, 4, str_repeat('あ', 20), 0, 'L', false, 1);

        $this->assertGreaterThan(2 * $lineHeight, $writer->getLastCellHeight());
    }

    /**
     * 折り返しセルの高さを, 描画も改ページもせずに測れること.
     *
     * 納品書の明細表は行の高さを先に測ってから改ページする. 測定が描画を兼ねると
     * その途中で自動改ページが起き, 退避しておいた座標が使えなくなる（文字は分割されて
     * 送られる一方, 罫線だけが新しいページの下端に取り残される）.
     */
    public function testMeasureMultiCellHeightNeitherDrawsNorBreaksPage(): void
    {
        $writer = $this->createWriter();
        $writer->setTemplateFile(self::projectDir().'/html/template/admin/assets/pdf/nouhinsyo.pdf');
        $writer->setMargins(15, 20);
        $writer->setFont('kozminproregular', '', 8);
        $writer->addPage();
        $lineHeight = $writer->getFontSize() * PdfWriter::CELL_HEIGHT_RATIO;

        // 明細表の事前判定が確保する 4 行ぶん(16mm)しか残っていない位置に置く
        $writer->setXY(15, $writer->getPageBreakTrigger() - 16.0);
        $pageCount = $writer->getPageCount();
        $y = $writer->getY();

        // 商品名列は 110.3mm 幅・8pt なので 1 行あたり約 38 全角文字
        $height = $writer->measureMultiCellHeight(110.3, 4.0, str_repeat('あ', 38 * 5));

        $this->assertGreaterThan(4 * $lineHeight, $height, '5 行に折り返すので 16mm を超える');
        $this->assertSame($pageCount, $writer->getPageCount(), '測定で改ページしてはいけない');
        $this->assertSame($y, $writer->getY(), '測定でカーソルが動いてはいけない');
    }

    /**
     * 測定した高さが multiCell() が実際に使うセル高さと一致すること.
     */
    public function testMeasureMultiCellHeightMatchesRenderedHeight(): void
    {
        $writer = $this->createWriter();
        $writer->setMargins(15, 20);
        $writer->setFont('kozminproregular', '', 8);
        $writer->addPage();

        foreach (['1行', "1行目\n2行目\n3行目", str_repeat('あ', 60)] as $text) {
            $measured = $writer->measureMultiCellHeight(30, 4.0, $text);
            $writer->setXY(15, 20);
            $writer->multiCell(30, 4.0, $text, 0, 'L', false, 1);

            $this->assertEqualsWithDelta($measured, $writer->getLastCellHeight(), 0.001, $text);
        }
    }

    /**
     * セル上端から文字ベースラインまでの距離(mm).
     */
    private function baselineOffset(float $cellHeight, float $fontSizePt): float
    {
        $ascent = self::FONT_ASCENT * $fontSizePt / self::POINTS_PER_MM;
        $descent = self::FONT_DESCENT * $fontSizePt / self::POINTS_PER_MM;

        return ($cellHeight + $ascent - $descent) / 2;
    }

    /**
     * 出力 PDF から最初のテキスト描画位置(mm, 用紙左上原点)を取り出す.
     *
     * @return array{0: float, 1: float}
     */
    private function firstTextPosition(PdfWriter $writer): array
    {
        $pdf = $writer->output();
        $this->assertStringStartsWith('%PDF', $pdf);

        foreach ($this->contentStreams($pdf) as $content) {
            if (preg_match('/([-\d.]+)\s+([-\d.]+)\s+Td\s*\(/', $content, $matches) === 1) {
                return [
                    (float) $matches[1] / self::POINTS_PER_MM,
                    $writer->getPageHeight() - ((float) $matches[2] / self::POINTS_PER_MM),
                ];
            }
        }

        self::fail('テキストの描画命令が見つからない');
    }

    /**
     * @return \Generator<int, string>
     */
    private function contentStreams(string $pdf): \Generator
    {
        $offset = 0;
        while (($start = strpos($pdf, "stream\n", $offset)) !== false) {
            $start += 7;
            $end = strpos($pdf, 'endstream', $start);
            if ($end === false) {
                return;
            }
            $raw = rtrim(substr($pdf, $start, $end - $start), "\r\n");
            $inflated = @gzuncompress($raw);
            // 'endstream' の中の 'stream' を次のストリーム開始と誤認しないよう読み飛ばす
            $offset = $end + 9;
            yield $inflated === false ? $raw : $inflated;
        }
    }
}
