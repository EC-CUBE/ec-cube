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

use Eccube\DependencyInjection\Compiler\McpScopeEnforcementPass;
use Mcp\Capability\Registry\ReferenceHandlerInterface;
use Mcp\Capability\RegistryInterface;
use Mcp\Exception\ToolNotFoundException;
use Mcp\Server\Builder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * MCP ツールを CLI から実行する (MCP の `tools/call` 相当)。
 *
 * コンソール AI クライアント (Claude Code 等) が、 コンテキストを食う MCP サーバ接続なしに、
 * CLI から同じ操作を行うための実行口。 ツール名・引数は `eccube:mcp:tools` のスキーマに従う。
 *
 * 認証・scope 強制は通さない (scope 強制なしの inner ReferenceHandler を使う)。 本コマンドを
 * 実行できる = サーバ/DB にアクセスできる前提であり、 HTTP の OAuth Bearer + scope とは別の
 * 「ローカル信頼」で運用する。
 */
#[AsCommand(
    name: 'eccube:mcp:call',
    description: 'MCP ツールを CLI から実行し結果を JSON で出力する (tools/call 相当・ローカル信頼・トークン不要)',
)]
final class McpCallCommand extends Command
{
    public function __construct(
        #[Autowire(service: 'mcp.registry')]
        private readonly RegistryInterface $registry,
        // scope 強制を通さない本物の Tool 実行器。 CLI はローカル信頼なので scope 検査を挟まない。
        #[Autowire(service: McpScopeEnforcementPass::INNER_REFERENCE_HANDLER_ID)]
        private readonly ReferenceHandlerInterface $handler,
        #[Autowire(service: 'mcp.server.builder')]
        private readonly Builder $builder,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('tool', InputArgument::REQUIRED, 'ツール名 (例: search_products)。 一覧は eccube:mcp:tools')
            ->addOption('args', null, InputOption::VALUE_REQUIRED, 'ツール引数の JSON (例: \'{"keyword":"cube","limit":3}\')', '{}');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $toolName = (string) $input->getArgument('tool');

        // Builder::build() が discovery ローダを走らせ、 共有 registry に #[McpTool] 群を登録する。
        // HTTP リクエストを経ない CLI では registry が空のままなので、 引く前に一度 build させる。
        $this->builder->build();

        $arguments = json_decode((string) $input->getOption('args'), true);
        // JSON オブジェクト (連想配列) のみ受ける。 非空の JSON 配列 (例 [1,2,3]) は引数名で
        // 解決できず全パラメータが既定値実行になり黙って誤動作するため弾く ([] は {} と同義で許可)。
        if (!\is_array($arguments) || (array_is_list($arguments) && [] !== $arguments)) {
            $io->error('--args は JSON オブジェクトで指定してください (例: \'{"limit":3}\')。');

            return Command::INVALID;
        }

        try {
            $reference = $this->registry->getTool($toolName);
        } catch (ToolNotFoundException) {
            $available = [];
            $cursor = null;
            do {
                $page = $this->registry->getTools(null, $cursor);
                foreach ($page->references as $tool) {
                    $available[] = $tool->name;
                }
                $cursor = $page->nextCursor;
            } while (null !== $cursor);

            $io->error(sprintf('未知のツール: %s', $toolName));
            $io->writeln('利用可能: '.implode(', ', $available));

            return Command::INVALID;
        }

        // sdk の ReferenceHandler は arguments['_session'] を無条件参照する (本来はサーバが
        // リクエスト時に注入する)。 CLI にセッションは無く、 各ツールも session を引数に取らないため
        // null を置いてキー欠落の warning だけ回避する (RequestContext 経路は isset で誤爆しない)。
        $arguments['_session'] = null;

        // ツール実行の例外は捕まえず Symfony に委ねる (メッセージ・トレース・非0終了を得るため)。
        $result = $this->handler->handle($reference, $arguments);

        // JSON_THROW_ON_ERROR: 不正 UTF-8 等でのエンコード失敗を空出力+成功にせず例外で落とす。
        $output->writeln(json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return Command::SUCCESS;
    }
}
