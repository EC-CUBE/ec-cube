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

namespace Eccube\Tests\Service\Mcp;

use Eccube\Service\Mcp\McpAuditLogger;
use Eccube\Service\Mcp\McpScope;
use Eccube\Service\Mcp\ScopeChecker;
use Eccube\Service\Mcp\ScopeEnforcingReferenceHandler;
use Mcp\Capability\Registry\PromptReference;
use Mcp\Capability\Registry\ReferenceHandlerInterface;
use Mcp\Capability\Registry\ToolReference;
use Mcp\Exception\ToolCallException;
use Mcp\Schema\Prompt;
use Mcp\Schema\Tool;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\User\InMemoryUser;

/**
 * `ScopeEnforcingReferenceHandler` のユニットテスト (DB 不要)。
 *
 * 設計案 A の核心 = 「scope 検査を Tool の外、 mcp-bundle が Tool を呼ぶ手前の 1 箇所で強制する」 を、
 * 中央 handler 単体で検証する。 各 Tool 個別の scope テストはここに集約された。
 *
 * 検証:
 *   - scope を満たす token → inner に委譲され、 inner の戻り値が返る
 *   - scope 不足 → ToolCallException (メッセージに必要 scope 名)、 inner は呼ばれない
 *   - 中央マップ未登録 Tool → fail-closed deny (scope を持っていても呼べない)、 inner は呼ばれない
 *   - 非 Tool 参照 (prompt 等) → scope 検査せず素通し
 */
final class ScopeEnforcingReferenceHandlerTest extends TestCase
{
    public function testDelegatesToInnerWhenScopeGranted(): void
    {
        $inner = new RecordingReferenceHandler('INNER_RESULT');
        $handler = $this->buildHandler($inner, [McpScope::ROLE_PRODUCT_READ]);

        $result = $handler->handle($this->toolReference('search_products'), ['_session' => null]);

        $this->assertSame('INNER_RESULT', $result);
        $this->assertSame(1, $inner->calls, 'scope 充足時は inner に委譲される');
    }

    public function testThrowsAndSkipsInnerWhenScopeInsufficient(): void
    {
        $inner = new RecordingReferenceHandler('INNER_RESULT');
        // order scope を持たない token で order tool を呼ぶ
        $handler = $this->buildHandler($inner, [McpScope::ROLE_PRODUCT_READ]);

        try {
            $handler->handle($this->toolReference('search_orders'), ['_session' => null]);
            $this->fail('ToolCallException が投げられるべき');
        } catch (ToolCallException $e) {
            $this->assertStringContainsString('Insufficient scope: mcp:order:read', $e->getMessage());
        }

        $this->assertSame(0, $inner->calls, 'scope 不足時は inner を呼ばない');
    }

    public function testFailClosedForUnmappedTool(): void
    {
        $inner = new RecordingReferenceHandler('INNER_RESULT');
        // 全 scope を与えても、 中央マップ未登録の tool は呼べない
        $handler = $this->buildHandler($inner, [
            McpScope::ROLE_PRODUCT_READ,
            McpScope::ROLE_ORDER_READ,
            McpScope::ROLE_CUSTOMER_READ,
            McpScope::ROLE_PLUGIN_READ,
        ]);

        try {
            $handler->handle($this->toolReference('some_unregistered_tool'), ['_session' => null]);
            $this->fail('未登録 tool は fail-closed で拒否されるべき');
        } catch (ToolCallException $e) {
            $this->assertStringContainsString('no scope mapping', $e->getMessage());
        }

        $this->assertSame(0, $inner->calls, '未登録 tool は inner を呼ばない');
    }

    public function testPassesThroughNonToolReference(): void
    {
        $inner = new RecordingReferenceHandler('PROMPT_RESULT');
        // scope を一切持たない token でも、 prompt 参照は scope 検査されず素通し
        $handler = $this->buildHandler($inner, []);

        $result = $handler->handle($this->promptReference('some_prompt'), ['_session' => null]);

        $this->assertSame('PROMPT_RESULT', $result);
        $this->assertSame(1, $inner->calls, '非 Tool 参照は素通しで inner に委譲される');
    }

    /**
     * @param list<string> $roles token に付与する role
     */
    private function buildHandler(ReferenceHandlerInterface $inner, array $roles): ScopeEnforcingReferenceHandler
    {
        $tokenStorage = new TokenStorage();
        $tokenStorage->setToken(new UsernamePasswordToken(
            new InMemoryUser('mcp-tester', null, $roles),
            'mcp',
            $roles,
        ));

        // role ベースの認可のみ必要 (scope は ROLE_OAUTH2_* role に変換済み)
        $authChecker = new AuthorizationChecker(
            $tokenStorage,
            new AccessDecisionManager([new RoleVoter()]),
        );

        return new ScopeEnforcingReferenceHandler(
            $inner,
            new ScopeChecker($authChecker),
            new McpAuditLogger(new NullLogger(), new RequestStack()),
        );
    }

    private function toolReference(string $name): ToolReference
    {
        $tool = new Tool($name, null, ['type' => 'object', 'properties' => [], 'required' => null], $name, null);

        return new ToolReference($tool, static fn () => null);
    }

    private function promptReference(string $name): PromptReference
    {
        $prompt = new Prompt($name);

        return new PromptReference($prompt, static fn () => null);
    }
}
