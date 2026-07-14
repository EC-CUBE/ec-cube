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

use Eccube\Command\EccubeCliToolCommand;
use Eccube\Service\Mcp\McpCliToolInvoker;
use Eccube\Service\Mcp\McpMarkdownFormatter;
use Mcp\Capability\Attribute\McpTool;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * MCP ツール 1 つにつき `eccube:cli:<tool_name>` コマンドを 1 個、 遅延サービスとして登録する。
 *
 * ツール名は compile 時に `mcp.tool` タグ付きサービスの `#[McpTool]` から取れる (name は属性に明示、
 * 無ければ SDK と同じくメソッド名 / `__invoke` はクラス短名にフォールバック)。 inputSchema は
 * runtime にしか得られないが、 コマンドの登録に必要なのは名前だけなので compile 時で足りる。
 *
 * 各コマンドは標準の `console.command` タグ (lazy) で登録するため、 実際に呼ばれたコマンドだけが
 * インスタンス化される (全 bin/console 呼び出しへ discovery コストを乗せない)。 本 pass は inner
 * ReferenceHandler を定義する {@see McpScopeEnforcementPass} (優先度 -100) の後に走らせる。
 */
final class McpCliCommandPass implements CompilerPassInterface
{
    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        // MCP 未配線 (builder 不在) なら守る対象も呼ぶ対象も無いのでスキップ。
        if (!$container->hasDefinition('mcp.server.builder')) {
            return;
        }

        foreach ($this->collectToolNames($container) as $toolName) {
            $definition = (new Definition(EccubeCliToolCommand::class))
                ->setArguments([
                    $toolName,
                    new Reference(McpCliToolInvoker::class),
                    new Reference(McpMarkdownFormatter::class),
                ])
                ->addTag('console.command', ['command' => 'eccube:cli:'.$toolName]);

            $container->setDefinition('eccube.mcp.cli_command.'.$toolName, $definition);
        }
    }

    /**
     * `mcp.tool` タグ付きサービスをリフレクションし、 登録済みツール名を列挙する。
     *
     * SDK の Discoverer に合わせ、 クラス属性 (invokable ツール) とメソッド属性の両方を、
     * `IS_INSTANCEOF` (McpTool を継承した独自属性も拾う) で走査する。 メソッドは public かつ
     * static / abstract / コンストラクタでないものに限る (SDK が登録しないメソッドから
     * 存在しないコマンドを生成しない)。
     *
     * @return list<string>
     */
    private function collectToolNames(ContainerBuilder $container): array
    {
        $names = [];
        foreach (array_keys($container->findTaggedServiceIds('mcp.tool')) as $serviceId) {
            $class = $container->getDefinition($serviceId)->getClass();
            if (null === $class || !class_exists($class)) {
                continue;
            }

            $reflection = new \ReflectionClass($class);

            foreach ($reflection->getAttributes(McpTool::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                /** @var McpTool $instance */
                $instance = $attribute->newInstance();
                $names[] = $instance->name ?? $reflection->getShortName();
            }

            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->isStatic() || $method->isAbstract() || $method->isConstructor()) {
                    continue;
                }
                foreach ($method->getAttributes(McpTool::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                    /** @var McpTool $instance */
                    $instance = $attribute->newInstance();
                    $names[] = $instance->name ?? ('__invoke' === $method->getName()
                        ? $method->getDeclaringClass()->getShortName()
                        : $method->getName());
                }
            }
        }

        return array_values(array_unique($names));
    }
}
