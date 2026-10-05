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

use Eccube\Util\IdUtil;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class IdUtilTest extends TestCase
{
    #[DataProvider(methodName: 'provideToId')]
    public function testToId(mixed $value, int $max, ?int $expected): void
    {
        $this->assertSame($expected, IdUtil::toId($value, $max));
    }

    /**
     * @return \Iterator<string, array{mixed, int, (int | null)}>
     */
    public static function provideToId(): \Iterator
    {
        yield 'int' => [1, IdUtil::INTEGER_MAX, 1];
        yield 'zero' => [0, IdUtil::INTEGER_MAX, 0];
        yield 'numeric string' => ['123', IdUtil::INTEGER_MAX, 123];
        yield 'leading zeros' => ['007', IdUtil::INTEGER_MAX, 7];
        yield 'integer max' => ['2147483647', IdUtil::INTEGER_MAX, 2147483647];
        yield 'integer max + 1' => ['2147483648', IdUtil::INTEGER_MAX, null];
        yield 'smallint max' => ['32767', IdUtil::SMALLINT_MAX, 32767];
        yield 'smallint max + 1' => ['32768', IdUtil::SMALLINT_MAX, null];
        yield 'over PHP_INT_MAX' => ['99999999999999999999', PHP_INT_MAX, null];
        yield 'negative' => ['-1', IdUtil::INTEGER_MAX, null];
        yield 'negative int' => [-1, IdUtil::INTEGER_MAX, null];
        yield 'plus sign' => ['+1', IdUtil::INTEGER_MAX, null];
        yield 'alpha' => ['abc', IdUtil::INTEGER_MAX, null];
        yield 'mixed' => ['1abc', IdUtil::INTEGER_MAX, null];
        yield 'empty' => ['', IdUtil::INTEGER_MAX, null];
        yield 'space' => [' 1', IdUtil::INTEGER_MAX, null];
        yield 'trailing newline' => ["1\n", IdUtil::INTEGER_MAX, null];
        yield 'float' => [1.0, IdUtil::INTEGER_MAX, null];
        yield 'decimal string' => ['1.0', IdUtil::INTEGER_MAX, null];
        yield 'null' => [null, IdUtil::INTEGER_MAX, null];
        yield 'array' => [['1'], IdUtil::INTEGER_MAX, null];
        yield 'bool' => [true, IdUtil::INTEGER_MAX, null];
    }

    public function testMaxForType(): void
    {
        $this->assertSame(IdUtil::SMALLINT_MAX, IdUtil::maxForType('smallint'));
        $this->assertSame(IdUtil::INTEGER_MAX, IdUtil::maxForType('integer'));
        $this->assertSame(PHP_INT_MAX, IdUtil::maxForType('bigint'));
        $this->assertNull(IdUtil::maxForType('string'));
        $this->assertNull(IdUtil::maxForType(null));
    }
}
