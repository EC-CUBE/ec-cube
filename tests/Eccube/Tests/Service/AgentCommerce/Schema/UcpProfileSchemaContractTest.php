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

namespace Eccube\Tests\Service\AgentCommerce\Schema;

use Eccube\Service\AgentCommerce\Discovery\PaymentHandlerRegistryInterface;
use Eccube\Service\AgentCommerce\Discovery\UcpProfileBuilder;
use Eccube\Service\AgentCommerce\Security\AgentCommerceMessageSignerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Layer 2 (スキーマ契約): UcpProfileBuilder が組み立てる discovery profile の `ucp` メンバが
 * UCP 公式 schema (ucp.json#/$defs/business_schema) に適合することを検証する.
 *
 * MUST 文として散文に現れない構造制約 (レジストリの値がエントリの**配列**であること等) を
 * schema そのもので機械検証する. 負例は、バリデータが当該制約を実際に評価していることの証明
 * (無視されるキーワードがあれば正常系だけでは気づけない).
 *
 * UCP schema はリポジトリに同梱せず、SchemaValidatorTrait が解決する
 * (ECCUBE_UCP_SCHEMA_DIR / var/agent-commerce-spec/ucp / specifications/ucp)。
 * schema が無い環境では markTestSkipped。取得手順は本ディレクトリ README.md 参照。
 *
 * profile 全体 (signing_keys[] を含むラッパー) の schema `profile.json` は v2026-04-08 には
 * 存在しないため、本テストは `ucp` メンバのみを対象とする. 配信文書 (空レジストリの {} 正規化を
 * 含む) は Web テスト UcpDiscoveryControllerTest::testProfileConformsToOfficialBusinessSchema で検証する.
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/ucp.json#L80 ($defs/base: services / capabilities / payment_handlers は reverse-domain キー → エントリ配列)
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/ucp.json#L174 ($defs/business_schema: services / payment_handlers 必須)
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/service.json#L58 (business_schema: transport=rest は endpoint 必須)
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/payment_handler.json#L13 (base: id 必須)
 */
final class UcpProfileSchemaContractTest extends TestCase
{
    use SchemaValidatorTrait;

    private const BUSINESS_SCHEMA_REF = 'https://ucp.dev/schemas/ucp.json#/$defs/business_schema';

    private const CATALOG_SEARCH_URL = 'https://shop.example/catalog/search';

    public function testProfileMatchesUcpBusinessSchema(): void
    {
        $ucp = $this->buildProfile()['ucp'];

        $this->assertValidUcp(
            self::BUSINESS_SCHEMA_REF,
            $ucp,
            'The ucp member of a business profile MUST satisfy ucp.json#/$defs/business_schema (registries keyed by reverse-domain name whose values are arrays of entries).'
        );
    }

    /**
     * 空の payment_handlers は JSON object {} でなければならない.
     * 連想配列経由では空の PHP 配列が [] になり検証できないため、生 JSON で検証する.
     */
    public function testProfileWithEmptyPaymentHandlersMatchesUcpBusinessSchema(): void
    {
        $ucp = $this->buildProfile(paymentHandlers: [])['ucp'];
        $ucp['payment_handlers'] = new \stdClass();

        $this->assertValidUcpJson(
            self::BUSINESS_SCHEMA_REF,
            (string) json_encode($ucp, JSON_UNESCAPED_SLASHES),
            'ucp.payment_handlers MUST be present as an (empty) JSON object {} even when no handler is advertised.'
        );
    }

    // --- 負例: バリデータが構造制約を評価していることの証明 --------------------

    /**
     * #7127 の再現形: レジストリの値をエントリの配列ではなく単一オブジェクトにすると拒否される.
     */
    public function testRegistryValueAsSingleObjectIsRejected(): void
    {
        $ucp = $this->buildProfile()['ucp'];

        foreach (['services', 'capabilities', 'payment_handlers'] as $registry) {
            $mutated = $ucp;
            foreach ($mutated[$registry] as $key => $entries) {
                // 先頭エントリだけを残して配列の包みを外す (既に単一オブジェクトならそのまま).
                $mutated[$registry][$key] = array_is_list($entries) ? $entries[0] : $entries;
            }

            $this->assertInvalidUcp(
                self::BUSINESS_SCHEMA_REF,
                $mutated,
                sprintf('ucp.%s values MUST be JSON arrays of entries; a single entry object MUST be rejected by the schema.', $registry)
            );
        }
    }

    public function testMissingPaymentHandlersRegistryIsRejected(): void
    {
        $ucp = $this->buildProfile()['ucp'];
        unset($ucp['payment_handlers']);

        $this->assertInvalidUcp(
            self::BUSINESS_SCHEMA_REF,
            $ucp,
            'ucp.payment_handlers is REQUIRED in a business profile (MUST be present even when empty).'
        );
    }

    public function testRestServiceWithoutEndpointIsRejected(): void
    {
        $ucp = $this->buildProfile()['ucp'];
        foreach ($ucp['services'] as $key => $entries) {
            // エントリ配列に正規化したうえで endpoint だけを落とし、違反箇所を endpoint 欠落に限定する.
            $ucp['services'][$key] = array_map(
                static function (array $entry): array {
                    unset($entry['endpoint']);

                    return $entry;
                },
                array_is_list($entries) ? $entries : [$entries]
            );
        }

        $this->assertInvalidUcp(
            self::BUSINESS_SCHEMA_REF,
            $ucp,
            'A service entry with transport "rest" MUST declare an endpoint (service.json#/$defs/business_schema).'
        );
    }

    public function testNonDateVersionIsRejected(): void
    {
        $ucp = $this->buildProfile()['ucp'];
        $ucp['version'] = '1.0';

        $this->assertInvalidUcp(
            self::BUSINESS_SCHEMA_REF,
            $ucp,
            'ucp.version MUST match the YYYY-MM-DD pattern (ucp.json#/$defs/version).'
        );
    }

    /**
     * 実装と同じ UcpProfileBuilder で profile を組み立てる (kernel 不要).
     * 既定では決済ハンドラ 1 件 (UcpPaymentHandlerDiscoveryRegistry と同じ最小エントリ {id, version}) を広告する.
     *
     * @param array<string, list<array<string, mixed>>>|null $paymentHandlers null なら既定の 1 件
     *
     * @return array<string, mixed>
     */
    private function buildProfile(?array $paymentHandlers = null): array
    {
        $paymentHandlers ??= [
            'dev.ucp.payment.card' => [
                ['id' => 'dev.ucp.payment.card', 'version' => UcpProfileBuilder::UCP_VERSION],
            ],
        ];

        $signer = $this->createStub(AgentCommerceMessageSignerInterface::class);
        $signer->method('getPublicJwks')->willReturn([]);

        $registry = $this->createStub(PaymentHandlerRegistryInterface::class);
        $registry->method('collect')->willReturn($paymentHandlers);

        $urlGenerator = $this->createStub(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturn(self::CATALOG_SEARCH_URL);

        return (new UcpProfileBuilder($signer, $registry, $urlGenerator))->build();
    }
}
