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

namespace Eccube\Command;

use Mcp\Capability\RegistryInterface;
use Mcp\Server\Builder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * 利用可能な MCP ツールの一覧 (名前・説明・入力スキーマ) を JSON で出力する。
 *
 * MCP の `tools/list` に相当する。 コンソール AI クライアント (Claude Code 等) が、
 * コンテキストを食う MCP サーバ接続なしに、 CLI から同じツール群を発見・実行するための入口。
 * ツール集合は mcp-bundle の Registry を再利用するため、 ツール追加時に本コマンドの変更は不要。
 */
#[AsCommand(
    name: 'eccube:mcp:tools',
    description: '利用可能な MCP ツールの一覧 (名前・説明・入力スキーマ) を JSON で出力する',
)]
final class McpToolsCommand extends Command
{
    public function __construct(
        #[Autowire(service: 'mcp.registry')]
        private readonly RegistryInterface $registry,
        #[Autowire(service: 'mcp.server.builder')]
        private readonly Builder $builder,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addOption('names-only', null, InputOption::VALUE_NONE, 'ツール名だけを出力する');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $namesOnly = (bool) $input->getOption('names-only');

        // Builder::build() が discovery ローダを走らせ、 共有 registry に #[McpTool] 群を登録する。
        // HTTP リクエストを経ない CLI では registry が空のままなので、 読む前に一度 build させる。
        $this->builder->build();

        $tools = [];
        $cursor = null;
        do {
            $page = $this->registry->getTools(null, $cursor);
            foreach ($page->references as $tool) {
                // getTools() の Page->references は Mcp\Schema\Tool を直接持つ
                $tools[] = $namesOnly ? $tool->name : [
                    'name' => $tool->name,
                    'description' => $tool->description,
                    'inputSchema' => $tool->inputSchema,
                ];
            }
            $cursor = $page->nextCursor;
        } while (null !== $cursor);

        // JSON_THROW_ON_ERROR: エンコード失敗を空出力+成功にせず例外で落とす。
        $output->writeln(json_encode($tools, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return Command::SUCCESS;
    }
}
