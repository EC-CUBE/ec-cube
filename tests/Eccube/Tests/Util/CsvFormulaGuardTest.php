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
     * @return \Iterator<int, array{string, string}>
     */
    public static function escapeProvider(): \Iterator
    {
        // 数式評価され得る先頭文字には ' を付与する
        yield ['=SUM(A1:A2)', "'=SUM(A1:A2)"];
        yield ['+1+1', "'+1+1"];
        yield ['-1+cmd|\'/c calc\'!A1', "'-1+cmd|'/c calc'!A1"];
        yield ['@SUM(1)', "'@SUM(1)"];
        yield ["\tfoo", "'\tfoo"];
        yield ["\rfoo", "'\rfoo"];
        // 通常の文字列は変更しない
        yield ['foo', 'foo'];
        yield ['山田太郎', '山田太郎'];
        yield ['user@example.com', 'user@example.com'];
        // 空文字は変更しない
        yield ['', ''];
    }

    #[DataProvider(methodName: 'escapeProvider')]
    public function testEscape(string $input, string $expected): void
    {
        $this->assertSame($expected, CsvFormulaGuard::escape($input));
    }

    public function testEscapePassesThroughNonString(): void
    {
        $this->assertSame(100, CsvFormulaGuard::escape(100));
        $this->assertNull(CsvFormulaGuard::escape(null));
    }

    /**
     * escape() で付与した ' のみ剥がし, 利用者由来の先頭 ' は維持する.
     *
     * @return \Iterator<int, array{string, string}>
     */
    public static function unescapeProvider(): \Iterator
    {
        // escape 済みの値は元に戻す
        yield ["'=SUM(A1:A2)", '=SUM(A1:A2)'];
        yield ["'+1+1", '+1+1'];
        yield ["'@SUM(1)", '@SUM(1)'];
        // 後続が数式トリガでない先頭 ' は利用者由来とみなし維持する
        yield ["'foo", "'foo"];
        yield ["'", "'"];
        // ' で始まらない値は変更しない
        yield ['=SUM(A1:A2)', '=SUM(A1:A2)'];
        yield ['foo', 'foo'];
    }

    #[DataProvider(methodName: 'unescapeProvider')]
    public function testUnescape(string $input, string $expected): void
    {
        $this->assertSame($expected, CsvFormulaGuard::unescape($input));
    }

    /**
     * 出力(escape)→取込(unescape)の往復で値が変わらないこと.
     *
     * @return \Iterator<int, array{string}>
     */
    public static function roundTripProvider(): \Iterator
    {
        yield ['=SUM(A1:A2)'];
        yield ['+1+1'];
        yield ['-100'];
        yield ['@SUM(1)'];
        yield ['foo'];
        yield ['山田太郎'];
        yield ['user@example.com'];
    }

    #[DataProvider(methodName: 'roundTripProvider')]
    public function testRoundTripPreservesValue(string $value): void
    {
        $exported = CsvFormulaGuard::escape($value);
        $this->assertIsString($exported);
        $this->assertSame($value, CsvFormulaGuard::unescape($exported));
    }

    /**
     * 値先頭の ' が付与由来か利用者由来かは区別できないため,
     * 利用者が元から「' + 数式トリガ」を入力していた場合は取込時に ' が剥がれる.
     * これは ' を可逆マーカーに用いる方式の構造的限界であり, 意図された挙動.
     */
    public function testUnescapeStripsUserSuppliedLeadingQuoteBeforeFormula(): void
    {
        // export は先頭 ' を数式トリガとみなさないため素通しする
        $this->assertSame("'=SUM(A1)", CsvFormulaGuard::escape("'=SUM(A1)"));
        // import は付与由来と区別できず先頭 ' を剥がす
        $this->assertSame('=SUM(A1)', CsvFormulaGuard::unescape("'=SUM(A1)"));
    }
}
