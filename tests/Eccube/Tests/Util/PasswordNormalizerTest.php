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

use Eccube\Util\PasswordNormalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PasswordNormalizerTest extends TestCase
{
    #[DataProvider(methodName: 'normalizeProvider')]
    public function testNormalize(?string $input, ?string $expected): void
    {
        $this->assertSame($expected, PasswordNormalizer::normalize($input));
    }

    /**
     * @return array<array{0: string|null, 1: string|null}>
     */
    public static function normalizeProvider(): \Iterator
    {
        // null・空文字はそのまま返す
        yield [null, null];
        yield ['', ''];
        // ASCII は不変(既存パスワードに影響しない)
        yield ['Password12345678', 'Password12345678'];
        // 半角カナは全角へ正規化される
        yield ['ﾊﾟｽﾜｰﾄﾞ', 'パスワード'];
        // 全角英数字は半角へ正規化される
        yield ['ＡＢＣ１２３', 'ABC123'];
    }
}
