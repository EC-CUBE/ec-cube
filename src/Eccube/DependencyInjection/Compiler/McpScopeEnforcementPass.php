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

namespace Eccube\DependencyInjection\Compiler;

use Eccube\Service\Mcp\ScopeEnforcingReferenceHandler;
use Mcp\Capability\Registry\ReferenceHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * MCP の scope 検査を「mcp-bundle が Tool を呼ぶ手前の 1 箇所」 に強制するための DI 配線。
 *
 * mcp-bundle の `Builder::setReferenceHandler()` に {@see ScopeEnforcingReferenceHandler} を差し込み、
 * 全 Tool 呼び出しが本層を必ず通過するようにする。 本層が委譲する「本物の」 Tool 実行器
 * (`Mcp\Capability\Registry\ReferenceHandler`) には、 **mcp-bundle の `McpPass` が builder に渡すものと
 * 同一の Tool ServiceLocator** を再利用する (discovery で発見される Tool 集合と一致させ、 DI 解決させる)。
 *
 * 本 pass は `McpPass` (優先度 0) の後に走る必要があるため、 Kernel 側で負の優先度で登録する。
 *
 * @see \Symfony\AI\McpBundle\DependencyInjection\McpPass mcp.tool を収集して builder->setContainer に渡す本家 pass
 */
final class McpScopeEnforcementPass implements CompilerPassInterface
{
    /** 本物の Tool 実行器 (ScopeEnforcingReferenceHandler の委譲先) のサービス ID。 */
    public const INNER_REFERENCE_HANDLER_ID = 'eccube.mcp.reference_handler.inner';

    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        // builder が無い (mcp-bundle 未配線) 環境では scope 強制を差し込む先が無い。 ここで先に
        // return しないと、 配線されない ReferenceHandler 定義だけが残り、 builder 不在構成の
        // コンテナコンパイルを乱す。 MCP サーバが無ければ守る対象も無いのでスキップしてよい。
        if (!$container->hasDefinition('mcp.server.builder')) {
            return;
        }

        // 本物の Tool 実行器に渡す Tool ServiceLocator を決める。
        // mcp-bundle が builder->setContainer に渡したロケータをそのまま再利用し、 集合の乖離を無くす。
        $toolLocator = $this->resolveToolLocator($container);

        $container->setDefinition(
            self::INNER_REFERENCE_HANDLER_ID,
            (new Definition(ReferenceHandler::class))->setArguments([$toolLocator]),
        );

        // 全 Tool 呼び出しが通る referenceHandler を scope 強制版に差し替える。
        $container->getDefinition('mcp.server.builder')
            ->addMethodCall('setReferenceHandler', [new Reference(ScopeEnforcingReferenceHandler::class)]);
    }

    /**
     * builder の `setContainer(...)` 呼び出し引数 (= McpPass が組んだ Tool ServiceLocator) を取り出して返す。
     * 見つからなければ `mcp.tool` タグから自前で同等のロケータを構築する。
     */
    private function resolveToolLocator(ContainerBuilder $container): Reference
    {
        // 呼び出し元 (process) が builder の存在を保証済み。
        foreach ($container->getDefinition('mcp.server.builder')->getMethodCalls() as [$method, $arguments]) {
            if ('setContainer' === $method && isset($arguments[0]) && $arguments[0] instanceof Reference) {
                return $arguments[0];
            }
        }

        $serviceReferences = [];
        foreach (array_keys($container->findTaggedServiceIds('mcp.tool')) as $serviceId) {
            $serviceReferences[$serviceId] = new Reference($serviceId);
        }

        return ServiceLocatorTagPass::register($container, $serviceReferences);
    }
}
