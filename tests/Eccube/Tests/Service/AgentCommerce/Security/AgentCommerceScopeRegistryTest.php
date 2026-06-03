<?php

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

namespace Eccube\Tests\Service\AgentCommerce\Security;

use Eccube\Service\AgentCommerce\Security\AgentCommerceScopeRegistry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1 (pure logic) tests for AgentCommerceScopeRegistry.
 *
 * Verifies the canonical "<protocol>:<capability>" scope vocabulary
 * (acp:checkout/acp:catalog, ucp:checkout/ucp:cart/ucp:catalog/ucp:identity),
 * rejection of malformed scopes, and that protocol crossover is denied.
 */
class AgentCommerceScopeRegistryTest extends TestCase
{
    private AgentCommerceScopeRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new AgentCommerceScopeRegistry();
    }

    #[DataProvider('validScopeProvider')]
    public function testIsValidScopeAcceptsCanonicalScopes(string $scope): void
    {
        self::assertTrue($this->registry->isValidScope($scope), sprintf('"%s" is a canonical <protocol>:<capability> scope and must be valid', $scope));
    }

    public static function validScopeProvider(): array
    {
        return [
            ['acp:checkout'],
            ['acp:catalog'],
            ['ucp:checkout'],
            ['ucp:cart'],
            ['ucp:catalog'],
            ['ucp:identity'],
        ];
    }

    #[DataProvider('invalidScopeProvider')]
    public function testIsValidScopeRejectsMalformedOrUnknownScopes(string $scope): void
    {
        self::assertFalse($this->registry->isValidScope($scope), sprintf('"%s" is not a canonical scope and must be rejected', $scope));
    }

    public static function invalidScopeProvider(): array
    {
        return [
            'three segments legacy form' => ['agent:catalog:read'],
            'unknown protocol' => ['foo:checkout'],
            'unknown capability for acp' => ['acp:identity'],
            'unknown capability for ucp' => ['ucp:feed'],
            'cart not valid for acp' => ['acp:cart'],
            'no colon' => ['checkout'],
            'empty string' => [''],
            'colon only' => [':'],
        ];
    }

    public function testScopesForProtocol(): void
    {
        self::assertEqualsCanonicalizing(
            ['acp:checkout', 'acp:catalog'],
            $this->registry->scopesForProtocol('acp'),
            'scopesForProtocol("acp") must list every acp scope in <protocol>:<capability> form'
        );
        self::assertEqualsCanonicalizing(
            ['ucp:checkout', 'ucp:cart', 'ucp:catalog', 'ucp:identity'],
            $this->registry->scopesForProtocol('ucp'),
            'scopesForProtocol("ucp") must list every ucp scope in <protocol>:<capability> form'
        );
    }

    public function testScopesForUnknownProtocolIsEmpty(): void
    {
        self::assertSame([], $this->registry->scopesForProtocol('unknown'), 'An unknown protocol must yield no scopes');
    }

    public function testSupportsGrantsWhenScopePresentForMatchingProtocol(): void
    {
        $granted = ['ucp:checkout', 'ucp:cart'];
        self::assertTrue(
            $this->registry->supports('ucp', 'checkout', $granted),
            'supports must be true when grantedScopes contains the exact <protocol>:<capability> and the protocol matches'
        );
    }

    public function testSupportsDeniesWhenCapabilityNotGranted(): void
    {
        $granted = ['ucp:cart'];
        self::assertFalse(
            $this->registry->supports('ucp', 'checkout', $granted),
            'supports must be false when the required capability scope was not granted'
        );
    }

    public function testSupportsDeniesProtocolCrossover(): void
    {
        $granted = ['acp:checkout'];
        self::assertFalse(
            $this->registry->supports('ucp', 'checkout', $granted),
            'Protocol crossover must be denied: an acp:checkout grant must not satisfy a ucp checkout request'
        );
    }

    public function testSupportsDeniesEmptyGrants(): void
    {
        self::assertFalse(
            $this->registry->supports('acp', 'catalog', []),
            'supports must be false when no scopes were granted'
        );
    }
}
