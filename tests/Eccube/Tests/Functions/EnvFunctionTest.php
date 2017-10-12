<?php

namespace Eccube\Tests\Functions;

class EnvFunctionTest extends \PHPUnit\Framework\TestCase
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
        self::assertSame([1,2,3], env('ECCUBE_ENV_TEST'));

        putenv('ECCUBE_ENV_TEST={}');
        self::assertSame([], env('ECCUBE_ENV_TEST'));

        putenv('ECCUBE_ENV_TEST={"aaa":"bbb"}');
        self::assertSame(['aaa' => 'bbb'], env('ECCUBE_ENV_TEST'));

        putenv('ECCUBE_ENV_TEST={"aaa":"bbb", "ccc":"ddd"}');
        self::assertSame(['aaa' => 'bbb', 'ccc' => 'ddd'], env('ECCUBE_ENV_TEST'));
    }
}
