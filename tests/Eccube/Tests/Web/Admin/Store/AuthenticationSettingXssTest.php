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

namespace Eccube\Tests\Web\Admin\Store;

use Eccube\Entity\BaseInfo;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Runtime\EscaperRuntime;

/**
 * 認証設定画面(authentication_setting.twig)の JS コンテキスト XSS 回帰テスト.
 *
 * 店名(shop_name)は JavaScript 文字列リテラル内に出力されるため,
 * `escape('js')` でエスケープされていないと JS 文字列を脱出して任意スクリプトを実行できる.
 * テンプレートの `{{ eccubeShopName|escape('js') }}` が効いていることを検証する.
 */
final class AuthenticationSettingXssTest extends AbstractAdminWebTestCase
{
    private ?BaseInfoRepository $baseInfoRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseInfoRepository = $this->entityManager->getRepository(BaseInfo::class);
    }

    public function testShopNameIsJsEscapedInAuthenticationSetting(): void
    {
        // JS 文字列リテラルを脱出しようとするペイロードを店名に設定する
        $payload = '";alert(1)//';
        $BaseInfo = $this->baseInfoRepository->get();
        $BaseInfo->setShopName($payload);
        $this->entityManager->persist($BaseInfo);
        $this->entityManager->flush();

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_store_authentication_setting')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $content = (string) $this->client->getResponse()->getContent();

        // escape('js') が適用された期待値を Twig 環境から実際に算出する（エスケープ仕様変更にも追従させる）
        /** @var Environment $twig */
        $twig = static::getContainer()->get(Environment::class);
        $expectedEscaped = $twig->getRuntime(EscaperRuntime::class)->escape($payload, 'js');

        // ペイロードに二重引用符が含まれているため, JS エスケープすると元の並びとは変わるはず
        $this->assertNotSame($payload, $expectedEscaped, '前提: ペイロードは escape(js) で変化するべき');

        // 生のペイロード（JS 文字列を脱出する `";alert(1)` の並び）が出力されていないこと
        $this->assertStringNotContainsString($payload, $content, 'JS 文字列を脱出する生ペイロードが出力されてはならない');
        // escape('js') 適用後の文字列が出力されていること
        $this->assertStringContainsString($expectedEscaped, $content, "escape('js') 適用後の文字列が出力されるべき");
    }
}
