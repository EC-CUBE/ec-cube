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

namespace Symfony\Component\HttpFoundation\Tests\Session\Storage\Handler;

use PHPUnit\Framework\TestCase;

/**
 * @requires PHP 7.0
 *
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
    public function testSecureSession($fixture, $user_agent, $shouldSendSameSiteNone)
    {
        $context = [
            'http' => [
                'header' => "Cookie: sid=123abc\r\nX-Forwarded-proto: https",
                'user_agent' => $user_agent,
            ],
        ];
        $context = stream_context_create($context);
        $result = file_get_contents(sprintf('http://localhost:8053/%s.php', $fixture), false, $context);

        if ($shouldSendSameSiteNone) {
            if (PHP_VERSION_ID < 70300) {
                // PHP7.3未満は互換用 cookie
                $this->assertStringEqualsFile(sprintf(self::FIXTURES_DIR.'/%s.samesite-compat.expected', $fixture), $result);
            } else {
                $this->assertStringEqualsFile(sprintf(self::FIXTURES_DIR.'/%s.samesite.expected', $fixture), $result);
            }
        } else {
            $this->assertStringEqualsFile(sprintf(self::FIXTURES_DIR.'/%s.secure.expected', $fixture), $result);
        }
    }

    /**
     * Secure 属性が付与されない場合は, SameSite 属性も付与されない(ブラウザのデフォルト値)
     *
     * @dataProvider provideSession
     */
    public function testNonSecureSession($fixture, $user_agent, $shouldSendSameSiteNone)
    {
        $context = [
            'http' => [
                'header' => "Cookie: sid=123abc\r\n",
                'user_agent' => $user_agent,
            ],
        ];
        $context = stream_context_create($context);
        $result = file_get_contents(sprintf('http://localhost:8053/%s.php', $fixture), false, $context);

        $this->assertStringEqualsFile(sprintf(self::FIXTURES_DIR.'/%s.expected', $fixture), $result);
    }

    /**
     * @see https://github.com/skorp/detect-incompatible-samesite-useragents/blob/master/tests/UserAgents.php
     */
    public function provideSession()
    {
        $userAgents = [
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130' => true,
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.3945.130' => false,
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3945.130' => false,
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.1 Safari/605.1.15' => false,
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1 Safari/605.1.15' => true,
            'Mozilla/5.0 (iPhone; CPU iPhone OS 12_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 EdgiOS/44.8.0 Mobile/15E148 Safari/605.1.15' => false,
            'Mozilla/5.0 (iPhone; CPU iPhone OS 13_1_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.1 Mobile/15E148 Safari/604.1' => true,
            'Mozilla/5.0 (Linux; U; Android 4.1.2; en-us; SM-T210R Build/JZO54K) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Safari/534.30 UCBrowser/2.3.2.300' => false,
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3) AppleWebKit/534.31 (KHTML, like Gecko) Chrome/17.0.558.0 Safari/534.31 UCBrowser/3.0.0.357' => false,
        ];

        foreach (glob(self::FIXTURES_DIR.'/*.php') as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            if ($name == 'common') {
                continue;
            }
            if ($name == 'storage') {
                // TODO Mock が動作しないためスキップ
                continue;
            }

            foreach ($userAgents as $user_agent => $shouldSendSameSiteNone) {
                yield [$name, $user_agent, $shouldSendSameSiteNone];
            }
        }
    }
}
