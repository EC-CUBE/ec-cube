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
use Symfony\Component\HttpFoundation\Response;

/**
 * trusted_hosts による Host ヘッダインジェクション対策の回帰テスト.
 *
 * Symfony は trusted_hosts が設定されている場合, リクエストの Host ヘッダが
 * 許可パターンに一致しないと SuspiciousOperationException を投げ, 400 を返す
 * (ValidateRequestListener が毎リクエストで Request::getHost() を呼ぶ).
 *
 * trusted_hosts は static な Request::$trustedHostPatterns で管理されるため,
 * テスト内で setTrustedHosts() を用いて設定状態を再現し, 元の値を tearDown で復元する.
 */
final class TrustedHostsTest extends AbstractWebTestCase
{
    /**
     * @var string[]|null テスト前の trusted host パターン（復元用）
     */
    private ?array $originalTrustedHosts = null;

    protected function setUp(): void
    {
        parent::setUp();
        // 既存の trusted host パターンを退避する
        $this->originalTrustedHosts = Request::getTrustedHosts();
        // 許可ホストを設定する（管理画面の trusted_hosts 設定と同じ正規表現形式）
        Request::setTrustedHosts(['^127\.0\.0\.1$', '^localhost$']);
    }

    protected function tearDown(): void
    {
        // static 状態を元に戻し, 他テストへ影響させない
        // setTrustedHosts は `{...}i` 形式に包むため, 退避済みの値から元の生パターンへ戻す
        $restored = array_map(
            static fn (string $pattern): string => preg_replace('/^\{(.*)\}i$/', '$1', $pattern),
            $this->originalTrustedHosts ?? []
        );
        Request::setTrustedHosts($restored);

        parent::tearDown();
    }

    /**
     * 許可されたホスト(localhost)からのアクセスは正常に処理される.
     */
    public function testTrustedHostIsAllowed(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('homepage'),
            [],
            [],
            ['HTTP_HOST' => 'localhost']
        );

        $this->assertTrue(
            $this->client->getResponse()->isSuccessful(),
            '許可ホスト localhost からのアクセスは成功するべき. Status='.$this->client->getResponse()->getStatusCode()
        );
    }

    /**
     * 許可されていないホスト(evil.com)からのアクセスは 400 で拒否される.
     */
    public function testUntrustedHostIsRejected(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('homepage'),
            [],
            [],
            ['HTTP_HOST' => 'evil.com']
        );

        $this->assertSame(
            Response::HTTP_BAD_REQUEST,
            $this->client->getResponse()->getStatusCode(),
            '信頼できない Host ヘッダ(evil.com)は 400 で拒否されるべき'
        );
    }
}
