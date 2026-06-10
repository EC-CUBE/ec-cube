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

namespace Eccube\Tests\DependencyInjection\Compiler;

use Eccube\DependencyInjection\Compiler\McpScopeEnforcementPass;
use Eccube\Service\Mcp\ScopeEnforcingReferenceHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * `McpScopeEnforcementPass` の配線テスト。
 *
 * 案 A の核心 = 「全 Tool 呼び出しが ScopeEnforcingReferenceHandler を必ず通る」 は、 本 pass が
 * mcp-bundle の builder に `setReferenceHandler` を差し込むことで成立する。 pass を消したり builder の
 * 構造が変わってこの配線が外れると、 scope 検査が丸ごとバイパスされる。 それを防ぐ回帰テスト。
 */
final class McpScopeEnforcementPassTest extends TestCase
{
    public function testWiresScopeEnforcingHandlerIntoBuilder(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition('mcp.server.builder', new Definition(\stdClass::class));
        // mcp.tool タグの Tool を 1 つ用意 (locator 構築対象)
        $container->setDefinition('dummy.tool', (new Definition(\stdClass::class))->addTag('mcp.tool'));

        (new McpScopeEnforcementPass())->process($container);

        // 1. builder に setReferenceHandler が ScopeEnforcingReferenceHandler 参照付きで差し込まれている
        $calls = $container->getDefinition('mcp.server.builder')->getMethodCalls();
        $setReferenceHandlerCalls = array_filter($calls, static fn (array $c): bool => 'setReferenceHandler' === $c[0]);
        $this->assertCount(1, $setReferenceHandlerCalls, 'builder に setReferenceHandler が 1 回差し込まれる');

        $call = array_values($setReferenceHandlerCalls)[0];
        $argument = $call[1][0];
        $this->assertInstanceOf(Reference::class, $argument);
        $this->assertSame(ScopeEnforcingReferenceHandler::class, (string) $argument, 'scope 強制版 handler が渡される');

        // 2. 委譲先の inner ReferenceHandler サービスが構築されている
        $this->assertTrue(
            $container->hasDefinition(McpScopeEnforcementPass::INNER_REFERENCE_HANDLER_ID),
            'inner ReferenceHandler サービスが定義される',
        );
    }

    public function testNoWiringWhenBuilderAbsent(): void
    {
        // mcp-bundle 未導入 (builder 無し) では配線をスキップする (例外も出さない)
        $container = new ContainerBuilder();

        (new McpScopeEnforcementPass())->process($container);

        $this->assertFalse($container->hasDefinition('mcp.server.builder'));
    }
}
