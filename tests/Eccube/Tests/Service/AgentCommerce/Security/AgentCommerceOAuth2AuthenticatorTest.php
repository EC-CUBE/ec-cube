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

namespace Eccube\Tests\Service\AgentCommerce\Security;

use Eccube\Service\AgentCommerce\Security\AgentCommerceOAuth2Authenticator;
use Eccube\Service\AgentCommerce\Security\AgentCommerceScopeRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

/**
 * Layer 4a (unit, eccube-api4 不要) tests for AgentCommerceOAuth2Authenticator.
 *
 * Symfony 標準 AccessTokenHandlerInterface のスタブを用いて、トークン検証 + scope×protocol
 * 照合の分岐 (200/401/403) と、リソースサーバー (eccube-api4) 未導入時の 503 フォールバックを検証する。
 */
final class AgentCommerceOAuth2AuthenticatorTest extends TestCase
{
    private AgentCommerceScopeRegistry $scopeRegistry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scopeRegistry = new AgentCommerceScopeRegistry();
    }

    /**
     * 付与 scope を attributes に載せて UserBadge を返すスタブ handler.
     *
     * @param array<int, string> $scopes
     */
    private function handlerWithScopes(array $scopes): AccessTokenHandlerInterface
    {
        return new readonly class($scopes) implements AccessTokenHandlerInterface {
            /** @param array<int, string> $scopes */
            public function __construct(private array $scopes)
            {
            }

            public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
            {
                return new UserBadge('agent-platform', null, ['scopes' => $this->scopes]);
            }
        };
    }

    /**
     * 不正トークンで AuthenticationException を投げるスタブ handler.
     */
    private function handlerRejectingToken(): AccessTokenHandlerInterface
    {
        return new class implements AccessTokenHandlerInterface {
            public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
            {
                throw new BadCredentialsException('Invalid token.');
            }
        };
    }

    public function testValidTokenWithMatchingScopeReturnsUserBadge(): void
    {
        $authenticator = new AgentCommerceOAuth2Authenticator($this->scopeRegistry, $this->handlerWithScopes(['acp:checkout']));

        $badge = $authenticator->authenticate('valid-token', 'acp', 'checkout');

        $this->assertSame('agent-platform', $badge->getUserIdentifier(), 'valid token + matching scope は UserBadge を返す');
    }

    public function testSpaceDelimitedScopeAttributeIsAccepted(): void
    {
        $handler = new class implements AccessTokenHandlerInterface {
            public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
            {
                return new UserBadge('agent-platform', null, ['scope' => 'ucp:catalog ucp:checkout']);
            }
        };
        $authenticator = new AgentCommerceOAuth2Authenticator($this->scopeRegistry, $handler);

        $badge = $authenticator->authenticate('valid-token', 'ucp', 'checkout');

        $this->assertSame('agent-platform', $badge->getUserIdentifier(), 'OAuth2 標準の空白区切り scope 文字列も解釈できる');
    }

    public function testInvalidTokenThrowsAuthenticationException(): void
    {
        $authenticator = new AgentCommerceOAuth2Authenticator($this->scopeRegistry, $this->handlerRejectingToken());

        $this->expectException(BadCredentialsException::class);
        $authenticator->authenticate('invalid-token', 'acp', 'checkout');
    }

    public function testMissingScopeThrowsAccessDenied(): void
    {
        // catalog scope しか持たないトークンで checkout を要求 -> 403。
        $authenticator = new AgentCommerceOAuth2Authenticator($this->scopeRegistry, $this->handlerWithScopes(['acp:catalog']));

        $this->expectException(AccessDeniedException::class);
        $authenticator->authenticate('valid-token', 'acp', 'checkout');
    }

    public function testProtocolCrossoverIsDenied(): void
    {
        // ucp:checkout を持つトークンで acp:checkout を要求 -> protocol 越境で 403。
        $authenticator = new AgentCommerceOAuth2Authenticator($this->scopeRegistry, $this->handlerWithScopes(['ucp:checkout']));

        $this->expectException(AccessDeniedException::class);
        $authenticator->authenticate('valid-token', 'acp', 'checkout');
    }

    public function testHandlerUnavailableThrowsServiceUnavailable(): void
    {
        // eccube-api4 未導入 = handler が null -> 503。
        $authenticator = new AgentCommerceOAuth2Authenticator($this->scopeRegistry);

        $this->assertFalse($authenticator->isAvailable(), 'handler 未注入時は isAvailable() が false');

        $this->expectException(ServiceUnavailableHttpException::class);
        $authenticator->authenticate('any-token', 'acp', 'checkout');
    }
}
