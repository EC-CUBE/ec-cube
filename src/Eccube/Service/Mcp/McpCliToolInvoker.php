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

namespace Eccube\Service\Mcp;

use Eccube\DependencyInjection\Compiler\McpScopeEnforcementPass;
use Mcp\Capability\Registry\ReferenceHandlerInterface;
use Mcp\Capability\Registry\ToolReference;
use Mcp\Capability\RegistryInterface;
use Mcp\Server\Builder;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * `eccube:cli:*` コマンド群から MCP ツールへアクセスするための実行口。
 *
 * MCP registry はコンテナ起動時点では空で、 `Builder::build()` が discovery ローダを走らせて初めて
 * ツールが登録される。 HTTP を経ない CLI ではこれを明示的に呼ぶ必要があるため、 本サービスが
 * 「初回 1 回だけ build する」 ガードを持ち、 registry へのアクセスを集約する。
 *
 * ツール実行は scope 強制を通さない inner ReferenceHandler に委譲する (CLI はローカル信頼)。
 */
final class McpCliToolInvoker
{
    private bool $primed = false;

    public function __construct(
        #[Autowire(service: 'mcp.registry')]
        private readonly RegistryInterface $registry,
        #[Autowire(service: 'mcp.server.builder')]
        private readonly Builder $builder,
        #[Autowire(service: McpScopeEnforcementPass::INNER_REFERENCE_HANDLER_ID)]
        private readonly ReferenceHandlerInterface $handler,
    ) {
    }

    /**
     * ツール名から {@see ToolReference} を得る (`->tool` に name / description / inputSchema)。
     */
    public function tool(string $name): ToolReference
    {
        $this->prime();

        return $this->registry->getTool($name);
    }

    /**
     * ツールを実行し、 各ツールの生の返り値を返す。
     *
     * @param array<string, mixed> $arguments
     */
    public function call(string $name, array $arguments): mixed
    {
        $reference = $this->tool($name);

        // sdk の ReferenceHandler は arguments['_session'] を無条件参照する (本来はサーバが
        // リクエスト時に注入する)。 CLI にセッションは無く各ツールも session を取らないため、
        // null を置いてキー欠落を避ける (RequestContext 経路は isset ガードで発火しない)。
        $arguments['_session'] = null;

        return $this->handler->handle($reference, $arguments);
    }

    private function prime(): void
    {
        if ($this->primed) {
            return;
        }

        $this->builder->build();
        $this->primed = true;
    }
}
