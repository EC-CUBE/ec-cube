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

namespace Eccube\Tests\Functions;

use PHPUnit\Framework\TestCase;

class EnvFunctionTest extends TestCase
{
    public function testEnv()
    {
        self::assertNull(env('ECCUBE_ENV_TEST'));

        putenv('ECCUBE_ENV_TEST=');
        self::assertSame('', env('ECCUBE_ENV_TEST'));

        putenv('ECCUBE_ENV_TEST=true');
        self::assertTrue(env('ECCUBE_ENV_TEST'));

        putenv('ECCUBE_ENV_TEST=false');
        self::assertFalse(env('ECCUBE_ENV_TEST'));

        putenv('ECCUBE_ENV_TEST=[]');
        self::assertSame([], env('ECCUBE_ENV_TEST'));

        putenv('ECCUBE_ENV_TEST=[1]');
        self::assertSame([1], env('ECCUBE_ENV_TEST'));

        putenv('ECCUBE_ENV_TEST=[1,2,3]');
        self::assertSame([1, 2, 3], env('ECCUBE_ENV_TEST'));

        putenv('ECCUBE_ENV_TEST={}');
        self::assertSame([], env('ECCUBE_ENV_TEST'));

        putenv('ECCUBE_ENV_TEST={"aaa":"bbb"}');
        self::assertSame(['aaa' => 'bbb'], env('ECCUBE_ENV_TEST'));

        putenv('ECCUBE_ENV_TEST={"aaa":"bbb", "ccc":"ddd"}');
        self::assertSame(['aaa' => 'bbb', 'ccc' => 'ddd'], env('ECCUBE_ENV_TEST'));
    }
}
