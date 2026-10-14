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

use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\AgentCommerce\Security\AgentCommerceMessageSignerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * UCP discovery profile (/.well-known/ucp) のドキュメントを組み立てる.
 *
 * profile.json 構造:
 *   { "ucp": { version, services, payment_handlers, capabilities }, "signing_keys": [JWK...] }
 *
 * - services / capabilities / payment_handlers は reverse-domain 名をキーとし、エントリの配列を値とするレジストリ (ucp.json の $defs.base).
 * - services は `dev.ucp.shopping` の REST バインディング 1 本。その `endpoint` (本実装では `/ucp`) を基底に、
 *   platform は `/catalog/search` や `/checkout-sessions` を相対解決する (catalog/rest.md・checkout-rest.md「Base URL」)。
 * - endpoint (絶対 URL) はパスをハードコードせず UrlGenerator (RequestContext) から動的生成する.
 * - capabilities: catalog (search / lookup) は公開商品データのため常時広告、checkout は BaseInfo の
 *   `ucp_checkout_enabled` が有効なときだけ広告する (UcpCheckoutController の 404 ゲートと一致させる)。
 * - signing_keys[] は EC 公開鍵 JWK のみ (秘密鍵パラメータ非混入). UcpMessageSigner の戻りをそのまま使う.
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/ucp.json#L174 ($defs/business_schema)
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/docs/specification/checkout-rest.md#L24 (Base URL: rest.endpoint からの相対解決)
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/docs/specification/catalog/rest.md#L34 (services: dev.ucp.shopping の宣言例)
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/source/schemas/profile.json
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
     * Checkout capability の reverse-domain キー.
     */
    public const CHECKOUT_CAPABILITY = 'dev.ucp.shopping.checkout';

    /**
     * Shopping service の reverse-domain キー (services レジストリ). catalog / checkout の REST
     * エンドポイントはこの service の endpoint からの相対パスで定義される.
     */
    public const SHOPPING_SERVICE_KEY = 'dev.ucp.shopping';

    public function __construct(
        private readonly AgentCommerceMessageSignerInterface $messageSigner,
        private readonly PaymentHandlerRegistryInterface $paymentHandlerRegistry,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly BaseInfoRepository $baseInfoRepository,
    ) {
    }

    /**
     * UCP discovery profile ドキュメント (連想配列) を組み立てる.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        // Catalog は公開商品データのため常時公開・常時広告する。checkout は ucp_checkout_enabled で切り替える。
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
     * services レジストリを組み立てる. Shopping service の REST バインディングを常時宣言する.
     *
     * endpoint は基底 URL のみ宣言し、個別の操作パス (`/catalog/search`, `/checkout-sessions` 等) は
     * 仕様側で endpoint からの相対パスとして定義される。catalog は常時公開のため service 自体は
     * checkout の有効/無効に関わらず宣言する (checkout が無効なら capability を載せないだけ)。
     *
     * @return array<string, list<array<string, mixed>>> reverse-domain キーのレジストリ (値は service エントリの配列)
     */
    private function buildServices(): array
    {
        // 基底はルートに直接対応しないため、常時存在する catalog search ルートの絶対 URL から
        // 仕様上の相対パス "/catalog/search" を取り除いて導く (RequestContext のホスト・ベースパスを反映).
        $searchUrl = $this->urlGenerator->generate(
            'agent_ucp_catalog_search',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $endpoint = preg_replace('#/catalog/search$#', '', $searchUrl) ?? $searchUrl;

        return [
            self::SHOPPING_SERVICE_KEY => [
                [
                    'version' => self::UCP_VERSION,
                    'transport' => 'rest',
                    'endpoint' => $endpoint,
                ],
            ],
        ];
    }

    /**
     * capabilities レジストリを組み立てる. Catalog の search/lookup を常時宣言し、
     * checkout は ucp_checkout_enabled のときだけ宣言する.
     *
     * @return array<string, list<array<string, mixed>>> reverse-domain キーのレジストリ (値は capability エントリの配列)
     */
    private function buildCapabilities(): array
    {
        $capabilities = [
            self::CATALOG_SEARCH_CAPABILITY => [
                [
                    'version' => self::UCP_VERSION,
                    'schema' => 'https://ucp.dev/schemas/shopping/catalog_search.json',
                ],
            ],
            self::CATALOG_LOOKUP_CAPABILITY => [
                [
                    'version' => self::UCP_VERSION,
                    'schema' => 'https://ucp.dev/schemas/shopping/catalog_lookup.json',
                ],
            ],
        ];

        // UcpCheckoutController は ucp_checkout_enabled=false のとき 404 を返す。広告だけ先行すると
        // platform が存在しないエンドポイントへ誘導されるため、ゲートを controller と一致させる。
        if ($this->baseInfoRepository->get()->isUcpCheckoutEnabled()) {
            $capabilities[self::CHECKOUT_CAPABILITY] = [
                [
                    'version' => self::UCP_VERSION,
                    'schema' => 'https://ucp.dev/schemas/shopping/checkout.json',
                ],
            ];
        }

        return $capabilities;
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
