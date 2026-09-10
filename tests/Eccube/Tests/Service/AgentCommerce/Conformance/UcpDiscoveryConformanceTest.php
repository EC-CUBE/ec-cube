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

namespace Eccube\Tests\Service\AgentCommerce\Conformance;

use Eccube\Service\AgentCommerce\Discovery\UcpProfileBuilder;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

/**
 * Layer 0 (仕様適合性) — UCP discovery profile (/.well-known/ucp).
 *
 * UCP v2026-04-08 の規範要件 (MUST / MUST NOT) を 1 テスト = 1 要件でトレースする。
 * profile を組み立てる {@see UcpProfileBuilder} は BaseInfo / 署名鍵 / UrlGenerator を
 * 必要とするためコンテナ依存だが、本クラスでは純粋に検証可能な不変条件
 * (version 形式・宣言する reverse-domain capability キーの妥当性・公開鍵限定) を
 * 定数および profile.json#/$defs/jwk_public_key 制約に対してトレースし、配信ヘッダ
 * (HTTPS / 3xx 禁止 / Cache-Control) はコントローラ層 (Layer 3') の Web テストへ委譲する。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/docs/specification/overview.md#L1090
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/source/schemas/profile.json#L9
 */
final class UcpDiscoveryConformanceTest extends TestCase
{
    /**
     * profile.json#/$defs/jwk_public_key が "not" で禁止する秘密鍵パラメータ。
     */
    private const PRIVATE_JWK_PARAMS = ['d', 'p', 'q', 'dp', 'dq', 'qi', 'oth', 'k'];

    /**
     * reverse-domain name の正規表現 (common/types/reverse_domain_name.json)。
     */
    private const REVERSE_DOMAIN_PATTERN = '/^[a-z][a-z0-9]*(?:\.[a-z][a-z0-9_]*)+$/';

    /**
     * UcpProfileBuilder が宣言する capability / service の reverse-domain キー一覧。
     * profile に載せる前段で必ず reverse-domain 形式でなければならない。
     */
    private const ADVERTISED_REVERSE_DOMAIN_KEYS = [
        'dev.ucp.shopping.catalog',
        'dev.ucp.shopping.catalog.search',
        'dev.ucp.shopping.catalog.lookup',
    ];

    /**
     * MUST: profile の ucp.version は YYYY-MM-DD 形式の UCP バージョン (ここでは 2026-04-08)。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/source/schemas/ucp.json#L8
     */
    public function testProfileVersionIsDateBasedYyyyMmDd(): void
    {
        $version = UcpProfileBuilder::UCP_VERSION;

        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}$/',
            $version,
            'MUST: ucp.version is in YYYY-MM-DD format.'
        );
        $this->assertSame(
            '2026-04-08',
            $version,
            'MUST: this implementation advertises UCP version 2026-04-08.'
        );
    }

    /**
     * MUST: services / capabilities / payment_handlers のレジストリキーは reverse-domain 命名。
     * UcpProfileBuilder が宣言する Catalog 系キーがいずれもパターンに適合することを検証する。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/source/schemas/common/types/reverse_domain_name.json#L7
     */
    public function testAdvertisedRegistryKeysUseReverseDomainNaming(): void
    {
        foreach (self::ADVERTISED_REVERSE_DOMAIN_KEYS as $key) {
            $this->assertMatchesRegularExpression(
                self::REVERSE_DOMAIN_PATTERN,
                $key,
                sprintf('MUST: registry keys MUST use reverse-domain naming (got "%s").', $key)
            );
        }
    }

    /**
     * MUST: services と payment_handlers レジストリは空であっても profile に存在しなければならない。
     * (Catalog API 無効時でも空オブジェクトとして必ず出力される。)
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/docs/specification/overview.md#L360
     */
    #[DoesNotPerformAssertions]
    public function testServicesAndPaymentHandlersRegistriesArePresentEvenWhenEmpty(): void
    {
        self::markTestIncomplete('MUST: services と payment_handlers MUST be present even when empty. profile 全体の組み立ては UcpProfileBuilder のコンテナ依存 (BaseInfo/UrlGenerator) を要するため、空オブジェクト保証はコンテナ駆動の discovery Web テスト (Layer 3\') で検証する。');
    }

    /**
     * MUST NOT: published signing_keys は EC 公開鍵 JWK のみ。秘密鍵パラメータ (d,p,q,...) を
     * profile に含めてはならない。本要件のラウンドトリップ検証は鍵素材を必要とするため
     * 共通基盤の署名テストに委ねるが、禁止パラメータ集合を不変条件として固定する。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/source/schemas/profile.json#L44
     */
    public function testSigningKeyPrivateParametersAreForbidden(): void
    {
        // profile.json#/$defs/jwk_public_key の "not" 制約が禁止する集合と一致することを固定。
        $this->assertSame(
            self::PRIVATE_JWK_PARAMS,
            ['d', 'p', 'q', 'dp', 'dq', 'qi', 'oth', 'k'],
            'MUST NOT: private key material (d,p,q,dp,dq,qi,oth,k) MUST NOT appear in a profile signing_keys[] JWK.'
        );
    }

    /**
     * MUST: profile は HTTPS で配信し、3xx リダイレクトを返してはならず、Cache-Control に
     * public + max-age>=60 を含め private/no-store/no-cache を使ってはならない。
     * これらは配信ヘッダの要件であり、discovery コントローラの応答 (Layer 3') で検証する。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/docs/specification/overview.md#L1090
     */
    #[DoesNotPerformAssertions]
    public function testProfileDeliveryHttpsNoRedirectAndCacheControlAreVerifiedAtWebLayer(): void
    {
        self::markTestIncomplete('MUST: profile served over HTTPS, MUST NOT use 3xx redirects, Cache-Control MUST be "public, max-age>=60" (not private/no-store/no-cache). これらの配信ヘッダ要件は discovery コントローラの Web テスト (Layer 3\') で検証する。');
    }
}
