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

namespace Eccube\Tests\Web;

use Symfony\Component\HttpFoundation\Request;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * サイト共通の構造化データ（WebSite / Organization）の出力ページを検証する.
 *
 * Google の Organization ドキュメントが「トップページか組織を説明する単一ページを推奨。
 * サイトの全ページに含める必要はない」としているため、`TwigInitializeListener` で
 * トップページと「当サイトについて」の 2 ページに限定している。
 */
final class SiteStructuredDataOutputTest extends EccubeTestCase
{
    /**
     * @return \Iterator<string, array{string, bool}>
     */
    public static function provideRoutes(): \Iterator
    {
        yield 'homepage は出力する' => ['/', true];
        yield 'help_about は出力する' => ['/help/about', true];
        yield 'product_list は出力しない' => ['/products/list', false];
        yield 'cart は出力しない' => ['/cart', false];
        yield 'help_privacy は出力しない' => ['/help/privacy', false];
    }

    #[DataProvider(methodName: 'provideRoutes')]
    public function testSiteStructuredDataIsOutputOnlyOnTargetPages(string $path, bool $expected): void
    {
        $this->client->request(Request::METHOD_GET, $path);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful(), $path.' が 2xx で応答していない');

        $html = (string) $response->getContent();
        $this->assertSame(
            $expected,
            str_contains($html, '"WebSite"'),
            $path.' の WebSite 構造化データの出力が期待と異なる'
        );
    }
}
