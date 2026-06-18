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

namespace Eccube\Tests\Util;

use Eccube\Util\CsvFormulaGuard;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CsvFormulaGuardTest extends TestCase
{
    /**
     * @return array<int, array{string, string}>
     */
    public static function escapeProvider(): array
    {
        return [
            // 数式評価され得る先頭文字には ' を付与する
            ['=SUM(A1:A2)', "'=SUM(A1:A2)"],
            ['+1+1', "'+1+1"],
            ['-1+cmd|\'/c calc\'!A1', "'-1+cmd|'/c calc'!A1"],
            ['@SUM(1)', "'@SUM(1)"],
            ["\tfoo", "'\tfoo"],
            ["\rfoo", "'\rfoo"],
            // 通常の文字列は変更しない
            ['foo', 'foo'],
            ['山田太郎', '山田太郎'],
            ['user@example.com', 'user@example.com'],
            // 空文字は変更しない
            ['', ''],
        ];
    }

    #[DataProvider('escapeProvider')]
    public function testEscape(string $input, string $expected): void
    {
        self::assertSame($expected, CsvFormulaGuard::escape($input));
    }

    public function testEscapePassesThroughNonString(): void
    {
        self::assertSame(100, CsvFormulaGuard::escape(100));
        self::assertNull(CsvFormulaGuard::escape(null));
    }

    /**
     * escape() で付与した ' のみ剥がし, 利用者由来の先頭 ' は維持する.
     *
     * @return array<int, array{string, string}>
     */
    public static function unescapeProvider(): array
    {
        return [
            // escape 済みの値は元に戻す
            ["'=SUM(A1:A2)", '=SUM(A1:A2)'],
            ["'+1+1", '+1+1'],
            ["'@SUM(1)", '@SUM(1)'],
            // 後続が数式トリガでない先頭 ' は利用者由来とみなし維持する
            ["'foo", "'foo"],
            ["'", "'"],
            // ' で始まらない値は変更しない
            ['=SUM(A1:A2)', '=SUM(A1:A2)'],
            ['foo', 'foo'],
        ];
    }

    #[DataProvider('unescapeProvider')]
    public function testUnescape(string $input, string $expected): void
    {
        self::assertSame($expected, CsvFormulaGuard::unescape($input));
    }

    /**
     * 出力(escape)→取込(unescape)の往復で値が変わらないこと.
     *
     * @return array<int, array{string}>
     */
    public static function roundTripProvider(): array
    {
        return [
            ['=SUM(A1:A2)'],
            ['+1+1'],
            ['-100'],
            ['@SUM(1)'],
            ['foo'],
            ['山田太郎'],
            ['user@example.com'],
        ];
    }

    #[DataProvider('roundTripProvider')]
    public function testRoundTripPreservesValue(string $value): void
    {
        $exported = CsvFormulaGuard::escape($value);
        self::assertIsString($exported);
        self::assertSame($value, CsvFormulaGuard::unescape($exported));
    }

    /**
     * 値先頭の ' が付与由来か利用者由来かは区別できないため,
     * 利用者が元から「' + 数式トリガ」を入力していた場合は取込時に ' が剥がれる.
     * これは ' を可逆マーカーに用いる方式の構造的限界であり, 意図された挙動.
     */
    public function testUnescapeStripsUserSuppliedLeadingQuoteBeforeFormula(): void
    {
        // export は先頭 ' を数式トリガとみなさないため素通しする
        self::assertSame("'=SUM(A1)", CsvFormulaGuard::escape("'=SUM(A1)"));
        // import は付与由来と区別できず先頭 ' を剥がす
        self::assertSame('=SUM(A1)', CsvFormulaGuard::unescape("'=SUM(A1)"));
    }
}
