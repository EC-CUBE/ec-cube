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

namespace Eccube\Tests\Service\Mcp\Tool;

use Eccube\Service\Mcp\McpScope;
use Eccube\Service\Mcp\Tool\GetPluginTool;
use Eccube\Tests\EccubeTestCase;
use Mcp\Exception\ToolCallException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * `GetPluginTool` の DB 結合テスト。 Api44 を題材に composer.json マージまで確認する。
 */
final class GetPluginToolTest extends EccubeTestCase
{
    private ?GetPluginTool $tool = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->tool = static::getContainer()->get(GetPluginTool::class);
        $this->tokenStorage()->setToken(null);
    }

    public function testThrowsWhenScopeIsAbsent(): void
    {
        $this->expectException(ToolCallException::class);
        $this->tool->get(code: 'Api44');
    }

    public function testReturnsPluginByCode(): void
    {
        $this->grantScope(McpScope::ROLE_PLUGIN_READ);

        $result = $this->tool->get(code: 'Api44');

        $this->assertSame('Api44', $result['code']);
        $this->assertArrayHasKey('composer', $result);
    }

    public function testReturnsEmptyWhenNotFound(): void
    {
        $this->grantScope(McpScope::ROLE_PLUGIN_READ);

        $result = $this->tool->get(code: 'NoSuchPlugin');

        $this->assertSame([], $result);
    }

    public function testReturnsEmptyWhenNeitherIdNorCode(): void
    {
        $this->grantScope(McpScope::ROLE_PLUGIN_READ);

        $result = $this->tool->get();

        $this->assertSame([], $result);
    }

    public function testIncludesComposerJsonDescriptionAndRequire(): void
    {
        $this->grantScope(McpScope::ROLE_PLUGIN_READ);

        $result = $this->tool->get(code: 'Api44');

        $composer = $result['composer'] ?? null;
        $this->assertIsArray($composer, 'composer キーが配列で含まれる');
        $this->assertArrayHasKey('description', $composer);
        $this->assertArrayHasKey('require', $composer);
        $this->assertIsArray($composer['require']);
        // Api44 は league/oauth2-server-bundle を require している (今 PR で追加した経路でも変わらない)
        $this->assertArrayHasKey('league/oauth2-server-bundle', $composer['require']);
    }

    private function grantScope(string $role): void
    {
        $member = $this->createMember();
        $token = new UsernamePasswordToken($member, 'admin', [$role]);
        $this->tokenStorage()->setToken($token);
    }

    private function tokenStorage(): TokenStorageInterface
    {
        $tokenStorage = static::getContainer()->get(TokenStorageInterface::class);
        $this->assertInstanceOf(TokenStorageInterface::class, $tokenStorage);

        return $tokenStorage;
    }
}
