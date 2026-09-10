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

namespace Eccube\Tests\Service\Pdf\Font;

use Eccube\Service\Pdf\Font\JapaneseCidFont;
use PHPUnit\Framework\TestCase;

/**
 * 納品書の字送りとベースライン位置を決める値を固定する.
 *
 * 文字幅が変わると欧文の整列と折り返しが動き, アセント・ディセントが変わると
 * 帳票の全行が縦に動く. どちらも 4.3 までの TCPDF と同じ値でなければならない.
 */
final class JapaneseCidFontTest extends TestCase
{
    /**
     * 文字幅は欧文 95 文字（U+0020..U+007E）を隙間なく持つこと.
     */
    public function testLatinWidthsCoverPrintableAscii(): void
    {
        $widths = JapaneseCidFont::LATIN_WIDTHS;

        $this->assertCount(95, $widths);
        $this->assertSame(range(0x20, 0x7E), array_keys($widths));
        $this->assertSame(278, $widths[0x20], '半角スペース');
        $this->assertSame(387, $widths[0x7E], 'チルダ');
    }

    /**
     * アセント・ディセントは日本語フォント共通の em 配分であること.
     *
     * ベースラインは `y + (セル高 + アセント - ディセント) / 2` で決まるため,
     * ここが変わると帳票の全行がフォントサイズに比例してずれる.
     */
    public function testEmMetricsAreFixed(): void
    {
        $this->assertSame(880, JapaneseCidFont::ASCENT);
        $this->assertSame(JapaneseCidFont::DESCENT, -120);
        $this->assertSame(1000, JapaneseCidFont::DEFAULT_WIDTH);
    }

    /**
     * 明朝・ゴシックの通常とボールドで 4 書体を返すこと.
     */
    public function testFacesRegisterFourStyles(): void
    {
        $faces = JapaneseCidFont::faces();

        $this->assertSame([
            'kozminproregular',
            'kozminproregularB',
            'kozgopromedium',
            'kozgopromediumB',
        ], array_keys($faces));

        $this->assertSame('KozMinPro-Regular-Acro', $faces['kozminproregular']['name']);
        $this->assertSame('KozMinPro-Regular-Acro,Bold', $faces['kozminproregularB']['name']);
        $this->assertSame('KozGoPro-Medium-Acro', $faces['kozgopromedium']['name']);
        $this->assertSame('KozGoPro-Medium-Acro,Bold', $faces['kozgopromediumB']['name']);
    }

    /**
     * Flags は明朝がセリフ体, ゴシックがサンセリフ体であること.
     *
     * 2 = Serif / 4 = Symbolic（PDF 仕様のビットフラグ）.
     */
    public function testFlagsDistinguishSerif(): void
    {
        $faces = JapaneseCidFont::faces();

        $this->assertSame(6, $faces['kozminproregular']['desc']['Flags'], '明朝は Serif + Symbolic');
        $this->assertSame(4, $faces['kozgopromedium']['desc']['Flags'], 'ゴシックは Symbolic のみ');
    }

    /**
     * 文字幅は欧文が字幅表, それ以外が既定値になること.
     */
    public function testStringWidthUsesTableForLatinAndDefaultForOthers(): void
    {
        // 'A' 646 + 'B' 604
        $this->assertSame(1250, JapaneseCidFont::stringWidth('AB'));
        // 和文は全角固定
        $this->assertSame(2000, JapaneseCidFont::stringWidth('あい'));
        // 半角カナも字幅表に無いので既定値（4.3 までの TCPDF と同じ扱い）
        $this->assertSame(1000, JapaneseCidFont::stringWidth('ｱ'));
        $this->assertSame(0, JapaneseCidFont::stringWidth(''));
    }

    /**
     * 文書タイトルの中央寄せに使う幅が, 想定どおりの値になること.
     */
    public function testStringWidthOfDefaultTitle(): void
    {
        // 全角 10 文字 + 半角括弧 2 つ（323 x 2）
        $this->assertSame(10000 + 646, JapaneseCidFont::stringWidth('お買上げ明細書(納品書)'));
    }

    public function testCodePoints(): void
    {
        $this->assertSame([0x41, 0x3042], JapaneseCidFont::codePoints('Aあ'));
        $this->assertSame([], JapaneseCidFont::codePoints(''));
    }
}
