<?php

namespace Symfony\Component\HttpFoundation\Tests\Session\Storage\Handler;

use PHPUnit\Framework\TestCase;

/**
 * @requires PHP 7.0
 * @see https://github.com/symfony/symfony/blob/3.4/src/Symfony/Component/HttpFoundation/Tests/Session/Storage/Handler/AbstractSessionHandlerTest.php
 */
class SameSiteNoneCompatSessionHandlerTest extends TestCase
{
    private static $server;
    const FIXTURES_DIR = __DIR__.'/../../../../../Fixtures/session';

    public static function setUpBeforeClass()
    {
        $spec = [
            1 => ['file', '/dev/null', 'w'],
            2 => ['file', '/dev/null', 'w'],
        ];
        if (!self::$server = @proc_open('exec php -S localhost:8053', $spec, $pipes, self::FIXTURES_DIR)) {
            self::markTestSkipped('PHP server unable to start.');
        }
        sleep(1);
    }

    public static function tearDownAfterClass()
    {
        if (self::$server) {
            proc_terminate(self::$server);
            proc_close(self::$server);
        }
    }

    /**
     * @dataProvider provideSession
     */
    public function testSession($fixture)
    {
        $context = ['http' => ['header' => "Cookie: sid=123abc\r\n"]];
        $context = stream_context_create($context);
        $result = file_get_contents(sprintf('http://localhost:8053/%s.php', $fixture), false, $context);

        $this->assertStringEqualsFile(sprintf(self::FIXTURES_DIR.'/%s.expected', $fixture), $result);
    }

    public function provideSession()
    {
        foreach (glob(self::FIXTURES_DIR.'/*.php') as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            if ($name == 'common') continue;
            yield [$name];
        }
    }
}
