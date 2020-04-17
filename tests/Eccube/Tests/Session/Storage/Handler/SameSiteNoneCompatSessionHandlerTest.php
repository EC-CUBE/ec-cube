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
    public function testSession($fixture, $user_agent)
    {
        $context = [
            'http' => [
                'header' => "Cookie: sid=123abc\r\n",
                'user_agent' => $user_agent
            ]
        ];
        $context = stream_context_create($context);
        $result = file_get_contents(sprintf('http://localhost:8053/%s.php', $fixture), false, $context);

        $this->assertStringEqualsFile(sprintf(self::FIXTURES_DIR.'/%s.expected', $fixture), $result);
    }

    public function provideSession()
    {
        $userAgents = [
            'Mozilla/5.0 (iPhone; CPU iPhone OS 13_1_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.1 Mobile/15E148 Safari/604.1',
        ];

        foreach (glob(self::FIXTURES_DIR.'/*.php') as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            if ($name == 'common') continue;
            if ($name == 'storage') {
                // TODO Mock が動作しないためスキップ
                continue;
            }
            foreach ($userAgents as $user_agent) {
                yield [$name, $user_agent];
            }
        }
    }
}
