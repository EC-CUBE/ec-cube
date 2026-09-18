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
        // 既に ' で始まる値は二重化する(エスケープ文字自身をエスケープし往復を可逆にする)
        yield ["'=SUM(A1)", "''=SUM(A1)"];
        yield ["'foo", "''foo"];
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
     * 先頭の ' を 1 つだけ剥がす.
     *
     * @return \Iterator<int, array{string, string}>
     */
    public static function unescapeProvider(): \Iterator
    {
        // escape 済みの値は元に戻す
        yield ["'=SUM(A1:A2)", '=SUM(A1:A2)'];
        yield ["'+1+1", '+1+1'];
        yield ["'-1+1", '-1+1'];
        yield ["'@SUM(1)", '@SUM(1)'];
        yield ["'\tfoo", "\tfoo"];
        yield ["'\rfoo", "\rfoo"];
        // 二重化された先頭 ' (直後が ') は 1 つだけ剥がす
        yield ["''=SUM(A1)", "'=SUM(A1)"];
        yield ["''foo", "'foo"];
        // EC-CUBE 以外が作成した CSV の正規の先頭 ' (直後が非トリガ) は剥がさず保持する
        yield ["'foo", "'foo"];
        yield ["'Hello", "'Hello"];
        yield ["'07012345678", "'07012345678"];
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
        yield ["\tfoo"];
        yield ["\rfoo"];
        yield ['foo'];
        yield ['山田太郎'];
        yield ['user@example.com'];
        // 先頭 ' を含む利用者入力も往復で保存される(回帰: 旧実装では import 時に削れていた)
        yield ["'=1+1"];
        yield ["'foo"];
        yield ["'"];
    }

    #[DataProvider(methodName: 'roundTripProvider')]
    public function testRoundTripPreservesValue(string $value): void
    {
        $exported = CsvFormulaGuard::escape($value);
        $this->assertIsString($exported);
        $this->assertSame($value, CsvFormulaGuard::unescape($exported));
    }

    /**
     * 既に ' で始まる値は escape で二重化され, unescape で 1 つ剥がして元に戻る.
     * これにより「' + 数式トリガ」を入力した値も往復で保存される.
     */
    public function testEscapeDoublesLeadingQuoteForReversibleRoundTrip(): void
    {
        $this->assertSame("''=SUM(A1)", CsvFormulaGuard::escape("'=SUM(A1)"));
        $this->assertSame("'=SUM(A1)", CsvFormulaGuard::unescape("''=SUM(A1)"));
    }
}
