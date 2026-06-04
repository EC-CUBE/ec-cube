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

use Eccube\Service\AgentCommerce\MinorUnitConverter;
use Eccube\Service\AgentCommerce\Security\KeyStoreInterface;
use Eccube\Service\AgentCommerce\Security\UcpMessageSigner;
use PHPUnit\Framework\TestCase;

/**
 * Layer 0 (conformance) tests for the agent-commerce common base.
 *
 * These assert a handful of cross-protocol MUST requirements shared by ACP and
 * UCP. Requirements not yet implementable in the base are marked incomplete.
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/tree/main/spec/2026-04-17
 * @see https://github.com/Universal-Commerce-Protocol/ucp
 */
final class AgentCommerceBaseConformanceTest extends TestCase
{
    /**
     * MUST: monetary amounts are represented as integer minor units (smallest
     * currency unit), not floats. Verified through MinorUnitConverter for a
     * zero-decimal and a two-decimal currency.
     *
     * @see ACP spec/2026-04-17 — amounts are integers in the currency's minor unit
     */
    public function testMonetaryAmountsAreIntegerMinorUnits(): void
    {
        $converter = new MinorUnitConverter();

        $jpy = $converter->toMinorUnits('1000', 'JPY');
        $this->assertIsInt($jpy, 'MUST: monetary amounts are integer minor units (JPY, zero-decimal)');
        $this->assertSame(1000, $jpy, 'MUST: a JPY amount of 1000 is 1000 minor units');

        $usd = $converter->toMinorUnits('10.99', 'USD');
        $this->assertIsInt($usd, 'MUST: monetary amounts are integer minor units (USD, two-decimal)');
        $this->assertSame(1099, $usd, 'MUST: a USD amount of 10.99 is 1099 minor units (cents)');
    }

    /**
     * MUST: published signing keys advertise public key material only. The
     * private key parameter "d" MUST NOT appear in any discovery JWK.
     *
     * @see UCP /.well-known/ucp signing_keys[] — EC public-key JWKs only
     */
    public function testPublishedSigningKeysContainPublicMaterialOnly(): void
    {
        $store = new class implements KeyStoreInterface {
            /** @var array<string, string> */
            private array $store = [];

            public function read(string $purpose): ?string
            {
                return $this->store[$purpose] ?? null;
            }

            public function write(string $purpose, string $pem): void
            {
                $this->store[$purpose] = $pem;
            }
        };

        $signer = new UcpMessageSigner($store, 'ucp_signing');
        $jwks = $signer->getPublicJwks();

        $this->assertNotEmpty($jwks, 'MUST: at least one signing key is advertised for discovery');
        foreach ($jwks as $jwk) {
            $this->assertSame('EC', $jwk['kty'] ?? null, 'MUST: UCP signing keys are EC keys');
            $this->assertArrayNotHasKey('d', $jwk, 'MUST NOT: discovery JWKs must not contain the private parameter d');
        }
    }

    /**
     * Cross-protocol two-tier error model (protocol errors as HTTP 4xx/5xx vs
     * business errors as HTTP 200 + messages[]) is enforced at the controller
     * layer, which is out of scope for the common base.
     */
    public function testTwoTierErrorModelIsDeferredToControllerLayer(): void
    {
        self::markTestIncomplete('Two-tier error model (HTTP errors vs messages[]) is verified in the ACP/UCP checkout controller tracks, not in the common base.');
    }
}
