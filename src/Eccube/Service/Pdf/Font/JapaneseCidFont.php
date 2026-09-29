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

namespace Eccube\Service\Pdf\Font;

/**
 * 帳票で使う日本語フォント（Adobe-Japan1 の CID フォント）の定義.
 *
 * 字形データは持たない. PDF へ埋め込まず, 書体名と文字幅とフォント記述子だけを書き出して,
 * 表示側（PDF ビューア）に Adobe-Japan1 対応のフォントで代替描画させる.
 * そのため出力サイズは増えないが, **字形は閲覧環境に依存する**.
 *
 * ## 文字幅
 *
 * 欧文 95 文字（U+0020..U+007E）だけを持ち, それ以外は {@see DEFAULT_WIDTH} を使う.
 * 和文は全角固定なのでこれで足りる. 値は EC-CUBE 2系 `data/module/fpdi/japanese.php` と同じ
 * （FPDF 公式スクリプト由来）で, KozMinPro-Regular のメトリクスにあたる.
 *
 * ゴシックも同じ幅表を使う. ゴシック専用の値は持たない.
 * 差が出るのは欧文を中央寄せ・右寄せしたときの位置だけで, 文書タイトルで最大 0.45mm,
 * 既定の日本語タイトルでは 0.0053mm に収まる.
 *
 * ## フォント記述子
 *
 * {@see ASCENT} と {@see DESCENT} は日本語フォントで共通の em 配分（IPA明朝・Noto CJK JP と同値）.
 * この 2 つは文字のベースライン位置を決めるため, 変えると帳票の全行が動く.
 * `Flags` は PDF 仕様のビットフラグで, 明朝はセリフ体（2）＋シンボリック（4）, ゴシックはシンボリックのみ.
 * 残り（`StemV` `CapHeight` `FontBBox`）は代替フォント選択のヒントにすぎないため一般値を置く.
 */
final class JapaneseCidFont
{
    /** 明朝のフォントキー（{@see \Eccube\Service\OrderPdfService::FONT_SJIS} と対応） */
    public const MINCHO = 'kozminproregular';

    /** ゴシックのフォントキー（{@see \Eccube\Service\OrderPdfService::FONT_GOTHIC} と対応） */
    public const GOTHIC = 'kozgopromedium';

    /** 幅表に無い文字の幅（和文は全角固定） */
    public const DEFAULT_WIDTH = 1000;

    /** アセント. 日本語フォント共通の em 配分 */
    public const ASCENT = 880;

    /** ディセント. 同上 */
    public const DESCENT = -120;

    /** 縦ステムの幅. 和文の標準的な太さ */
    public const STEM_V_REGULAR = 90;

    /** 縦ステムの幅（ボールド）. 表示側が太字の代替フォントを選ぶための値 */
    public const STEM_V_BOLD = 160;

    /**
     * 欧文 95 文字（U+0020..U+007E）の幅. 単位は 1/1000 em.
     *
     * @var array<int, int> Unicode コードポイント => 幅
     */
    public const LATIN_WIDTHS = [
        32 => 278, 33 => 299, 34 => 353, 35 => 614, 36 => 614, 37 => 721, 38 => 735, 39 => 216,
        40 => 323, 41 => 323, 42 => 449, 43 => 529, 44 => 219, 45 => 306, 46 => 219, 47 => 453,
        48 => 614, 49 => 614, 50 => 614, 51 => 614, 52 => 614, 53 => 614, 54 => 614, 55 => 614,
        56 => 614, 57 => 614, 58 => 219, 59 => 219, 60 => 529, 61 => 529, 62 => 529, 63 => 486,
        64 => 744, 65 => 646, 66 => 604, 67 => 617, 68 => 681, 69 => 567, 70 => 537, 71 => 647,
        72 => 738, 73 => 320, 74 => 433, 75 => 637, 76 => 566, 77 => 904, 78 => 710, 79 => 716,
        80 => 605, 81 => 716, 82 => 623, 83 => 517, 84 => 601, 85 => 690, 86 => 668, 87 => 990,
        88 => 681, 89 => 634, 90 => 578, 91 => 316, 92 => 614, 93 => 316, 94 => 529, 95 => 500,
        96 => 387, 97 => 509, 98 => 566, 99 => 478, 100 => 565, 101 => 503, 102 => 337, 103 => 549,
        104 => 580, 105 => 275, 106 => 266, 107 => 544, 108 => 276, 109 => 854, 110 => 579, 111 => 550,
        112 => 578, 113 => 566, 114 => 410, 115 => 444, 116 => 340, 117 => 575, 118 => 512, 119 => 760,
        120 => 503, 121 => 529, 122 => 453, 123 => 326, 124 => 380, 125 => 326, 126 => 387,
    ];

    /**
     * 書体ごとの PostScript 名と `Flags`.
     *
     * @var array<string, array{psName: string, flags: int}>
     */
    private const FACES = [
        self::MINCHO => ['psName' => 'KozMinPro-Regular-Acro', 'flags' => 6],
        self::GOTHIC => ['psName' => 'KozGoPro-Medium-Acro', 'flags' => 4],
    ];

    /**
     * 登録すべき書体を「フォントキー => 定義」で返す.
     *
     * ボールドは PostScript 名へ `,Bold` を付けたものを別書体として登録する.
     * キーの末尾 `B` は FPDF が `strtolower(family).strtoupper(style)` で作るものに合わせている.
     *
     * @return array<string, array{name: string, desc: array<string, int|string>}>
     */
    public static function faces(): array
    {
        $faces = [];
        foreach (self::FACES as $key => $face) {
            foreach (['' => '', 'B' => ',Bold'] as $style => $suffix) {
                $faces[$key.$style] = [
                    'name' => $face['psName'].$suffix,
                    'desc' => [
                        'Ascent' => self::ASCENT,
                        'Descent' => self::DESCENT,
                        'CapHeight' => self::ASCENT,
                        'Flags' => $face['flags'],
                        'FontBBox' => '[0 -200 1000 900]',
                        'ItalicAngle' => 0,
                        // 表示側は StemV を見て代替フォントの太さを決める.
                        // ボールドで通常と同じ値を出すと, 太字が細く描かれる.
                        'StemV' => $style === 'B' ? self::STEM_V_BOLD : self::STEM_V_REGULAR,
                    ],
                ];
            }
        }

        return $faces;
    }

    /**
     * 文字列の幅を 1/1000 em で返す.
     */
    public static function stringWidth(string $text): int
    {
        $width = 0;
        foreach (self::codePoints($text) as $codePoint) {
            $width += self::LATIN_WIDTHS[$codePoint] ?? self::DEFAULT_WIDTH;
        }

        return $width;
    }

    /**
     * UTF-8 の文字列をコードポイントの配列へ分解する.
     *
     * @return list<int>
     */
    public static function codePoints(string $text): array
    {
        if ($text === '') {
            return [];
        }

        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        if ($chars === false) {
            return [];
        }

        return array_map(static fn (string $char): int => mb_ord($char, 'UTF-8'), $chars);
    }
}
