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
     * @return array<string, array{0: string, 1: bool}>
     */
    public static function provideRoutes(): array
    {
        return [
            'homepage は出力する' => ['/', true],
            'help_about は出力する' => ['/help/about', true],
            'product_list は出力しない' => ['/products/list', false],
            'cart は出力しない' => ['/cart', false],
            'help_privacy は出力しない' => ['/help/privacy', false],
        ];
    }

    #[DataProvider('provideRoutes')]
    public function testSiteStructuredDataIsOutputOnlyOnTargetPages(string $path, bool $expected): void
    {
        $this->client->request('GET', $path);
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
