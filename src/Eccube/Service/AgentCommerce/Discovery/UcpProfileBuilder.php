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

namespace Eccube\Service\AgentCommerce\Discovery;

use Eccube\Service\AgentCommerce\Security\AgentCommerceMessageSignerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * UCP discovery profile (/.well-known/ucp) のドキュメントを組み立てる.
 *
 * profile.json 構造:
 *   { "ucp": { version, services, payment_handlers, capabilities }, "signing_keys": [JWK...] }
 *
 * - services / capabilities / payment_handlers のキーは reverse-domain.
 * - endpoint (絶対 URL) はパスをハードコードせず UrlGenerator (RequestContext) から動的生成する.
 * - signing_keys[] は EC 公開鍵 JWK のみ (秘密鍵パラメータ非混入). UcpMessageSigner の戻りをそのまま使う.
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/profile.json
 */
class UcpProfileBuilder
{
    /**
     * UCP プロトコルバージョン (date-based, YYYY-MM-DD).
     */
    public const UCP_VERSION = '2026-04-08';

    /**
     * Catalog capability の reverse-domain キー (search).
     */
    private const CATALOG_SEARCH_CAPABILITY = 'dev.ucp.shopping.catalog.search';

    /**
     * Catalog capability の reverse-domain キー (lookup).
     */
    private const CATALOG_LOOKUP_CAPABILITY = 'dev.ucp.shopping.catalog.lookup';

    /**
     * Catalog service の reverse-domain キー (services レジストリ).
     */
    private const CATALOG_SERVICE_KEY = 'dev.ucp.shopping.catalog';

    public function __construct(
        private readonly AgentCommerceMessageSignerInterface $messageSigner,
        private readonly PaymentHandlerRegistryInterface $paymentHandlerRegistry,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * UCP discovery profile ドキュメント (連想配列) を組み立てる.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        // Catalog は公開商品データのため常時公開・常時広告する。
        // checkout capability は ucp_checkout_enabled に応じて #6574 で追加する。
        $ucp = [
            'version' => self::UCP_VERSION,
            'services' => $this->buildServices(),
            'payment_handlers' => $this->buildPaymentHandlers(),
            'capabilities' => $this->buildCapabilities(),
        ];

        $profile = [
            'ucp' => $ucp,
        ];

        $signingKeys = $this->messageSigner->getPublicJwks();
        if ($signingKeys !== []) {
            $profile['signing_keys'] = $signingKeys;
        }

        return $profile;
    }

    /**
     * services レジストリを組み立てる. Catalog REST service を常時宣言する.
     *
     * @return array<string, array<string, mixed>> reverse-domain キーのレジストリ
     */
    private function buildServices(): array
    {
        // endpoint はベースパスのみ宣言する (個別 RPC パスは capability/schema 側で定義).
        // catalog の各エンドポイントは同一プレフィックス配下のため search ルートから親パスを導出する.
        $searchUrl = $this->urlGenerator->generate(
            'agent_ucp_catalog_search',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        // ".../catalog/search" から service ベース ".../catalog" を導く.
        $endpoint = preg_replace('#/search$#', '', $searchUrl) ?? $searchUrl;

        return [
            self::CATALOG_SERVICE_KEY => [
                'version' => self::UCP_VERSION,
                'transport' => 'rest',
                'endpoint' => $endpoint,
            ],
        ];
    }

    /**
     * capabilities レジストリを組み立てる. Catalog の search/lookup を常時宣言する.
     *
     * @return array<string, array<string, mixed>> reverse-domain キーのレジストリ
     */
    private function buildCapabilities(): array
    {
        return [
            self::CATALOG_SEARCH_CAPABILITY => [
                'version' => self::UCP_VERSION,
                'schema' => 'https://ucp.dev/schemas/shopping/catalog_search.json',
            ],
            self::CATALOG_LOOKUP_CAPABILITY => [
                'version' => self::UCP_VERSION,
                'schema' => 'https://ucp.dev/schemas/shopping/catalog_lookup.json',
            ],
        ];
    }

    /**
     * payment_handlers レジストリを組み立てる (寄与が無ければ空オブジェクト {}).
     *
     * @return array<string, list<array<string, mixed>>>
     */
    private function buildPaymentHandlers(): array
    {
        return $this->paymentHandlerRegistry->collect();
    }
}
