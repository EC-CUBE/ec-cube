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

namespace Eccube\Tests\Command;

use Eccube\Command\EccubeCliToolCommand;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * `EccubeCliToolCommand::cast()` の型変換分岐のユニット検証。 DB / kernel / Api44 不要。
 *
 * cast() は $this に触れない純粋な private ヘルパなので、 invoker 依存を持つコンストラクタを通さず
 * リフレクションで直接叩く (McpCliToolInvoker / Builder が final でモック不可なため)。 必須検証・
 * 配列オプション・整形出力までの通し確認は Api44 前提の結合テスト {@see McpCliCommandTest} が担う。
 */
final class EccubeCliToolCommandTest extends TestCase
{
    #[DataProvider(methodName: 'castCases')]
    public function testCast(string $value, string $type, int|float|bool|string|null $expected): void
    {
        $this->assertSame($expected, $this->cast($value, $type));
    }

    /**
     * @return iterable<string, array{string, string, int|float|bool|string|null}>
     */
    public static function castCases(): iterable
    {
        // integer: 不正は黙って 0 にせず null (=失敗)。 0 は失敗でなく成功値として保持。
        yield 'integer' => ['42', 'integer', 42];
        yield 'integer zero is a value, not failure' => ['0', 'integer', 0];
        yield 'integer invalid is failure' => ['abc', 'integer', null];

        // number: 整数らしい入力も float を保つ。 不正は null。
        yield 'number float' => ['1.5', 'number', 1.5];
        yield 'number integer-like stays float' => ['2', 'number', 2.0];
        yield 'number invalid is failure' => ['abc', 'number', null];

        // boolean: 認識できる真偽トークンのみ受理。 それ以外は false 化せず null (=失敗)。
        yield 'boolean true token' => ['yes', 'boolean', true];
        yield 'boolean false token' => ['off', 'boolean', false];
        yield 'boolean invalid is failure' => ['maybe', 'boolean', null];

        // 未知の型は素通し (数値らしい文字列を勝手に int 化しない)。
        yield 'unknown type passes through as string' => ['00123', 'object', '00123'];
    }

    private function cast(string $value, string $type): int|float|bool|string|null
    {
        $command = (new \ReflectionClass(EccubeCliToolCommand::class))->newInstanceWithoutConstructor();

        /** @var int|float|bool|string|null $result */
        $result = (new \ReflectionMethod(EccubeCliToolCommand::class, 'cast'))->invoke($command, $value, $type);

        return $result;
    }
}
