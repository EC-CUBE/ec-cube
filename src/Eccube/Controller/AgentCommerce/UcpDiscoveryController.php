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

namespace Eccube\Controller\AgentCommerce;

use Eccube\Controller\AbstractController;
use Eccube\Service\AgentCommerce\Discovery\UcpProfileBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Attribute\Route;

/**
 * UCP discovery profile (/.well-known/ucp) を配信するエンドポイント.
 *
 * 一次仕様 (v2026-04-08) の配信要件:
 *   - HTTPS 必須・3xx 禁止 (リバースプロキシ前提のため本実装はリダイレクトせずヘッダのみ設定).
 *   - Cache-Control: public, max-age >= 60 (no-store/no-cache/private は禁止).
 *   - well-known はオリジンルート固定・1 オリジン 1 枚 (RFC 8615).
 *
 * discovery は公開して害がないため常時公開する (checkout の有無は capability 宣言で表現する).
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/profile.json
 */
class UcpDiscoveryController extends AbstractController
{
    public function __construct(
        private readonly UcpProfileBuilder $profileBuilder,
    ) {
    }

    /**
     * GET /.well-known/ucp — UCP discovery profile を JSON で返す.
     */
    #[Route(path: '/.well-known/ucp', name: 'agent_ucp_discovery', methods: ['GET'])]
    public function index(): Response
    {
        $profile = $this->profileBuilder->build();

        // services / capabilities / payment_handlers は UCP profile schema 上 object 必須.
        // 空配列は JSON では [] になるため、JSON_FORCE_OBJECT に頼らず stdClass へ正規化して {} を保証する.
        $body = json_encode(
            $this->normalizeForJson($profile),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );

        $response = new Response($body, JsonResponse::HTTP_OK, [
            'Content-Type' => 'application/json',
            // public, max-age >= 60 (no-store/no-cache/private 禁止)
            'Cache-Control' => 'public, max-age=300',
        ]);

        // EC-CUBE は全リクエストでセッションを開始するため、Symfony の AbstractSessionListener が
        // 既定で Cache-Control を private, must-revalidate へ上書きする。discovery 文書は
        // 機密を含まない公開ドキュメント (RFC 8615) であり public キャッシュが必須 (no-store/no-cache/private 禁止)
        // のため、このマーカーヘッダで自動上書きを抑止する (リスナがレスポンス送出前に除去する)。
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        return $response;
    }

    /**
     * profile の object 必須フィールド (空のとき) を stdClass に置換し {} 出力を保証する.
     *
     * @param array<string, mixed> $profile
     *
     * @return array<string, mixed>
     */
    private function normalizeForJson(array $profile): array
    {
        if (isset($profile['ucp']) && is_array($profile['ucp'])) {
            foreach (['services', 'capabilities', 'payment_handlers'] as $objectKey) {
                if (isset($profile['ucp'][$objectKey]) && $profile['ucp'][$objectKey] === []) {
                    $profile['ucp'][$objectKey] = new \stdClass();
                }
            }
        }

        return $profile;
    }
}
