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

use Eccube\Service\Mcp\McpCliToolInvoker;
use Eccube\Service\Mcp\McpMarkdownFormatter;
use Eccube\Service\Mcp\ToolInputSchema;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 1 つの MCP ツールを表す `eccube:cli:<tool_name>` コマンド。
 *
 * MCP サーバでできる操作を `bin/console` からも同じように行うための実行口 (playwright-mcp に対する
 * playwright-cli の位置づけ)。 コンソール AI クライアント (Claude Code 等) が、 コンテキストを食う
 * MCP サーバ接続なしに同じツールを叩ける。 結果は Markdown で返す。
 *
 * 1 クラスで全ツールを表現し、 ツール名は {@see \Eccube\DependencyInjection\Compiler\McpCliCommandPass}
 * が MCP registry から列挙してコマンドごとに注入する。 ツールを追加しても本クラスの変更は不要。
 *
 * 認証・scope 強制は通さない (ローカル実行 = サーバ / DB にアクセスできる前提の「ローカル信頼」)。
 */
final class EccubeCliToolCommand extends Command
{
    public function __construct(
        private readonly string $toolName,
        private readonly McpCliToolInvoker $invoker,
        private readonly McpMarkdownFormatter $formatter,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $tool = $this->invoker->tool($this->toolName)->tool;
        $this->setDescription(($tool->description ?? '').' (MCP ツール '.$this->toolName.' 相当・ローカル信頼・トークン不要)');

        $schema = new ToolInputSchema($tool);
        foreach ($schema->propertyNames() as $name) {
            $mode = InputOption::VALUE_REQUIRED;
            if ($schema->isArray($name)) {
                $mode |= InputOption::VALUE_IS_ARRAY;
            }
            $this->addOption($name, null, $mode, $schema->description($name));
        }
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $schema = new ToolInputSchema($this->invoker->tool($this->toolName)->tool);

        $arguments = [];
        foreach ($schema->propertyNames() as $name) {
            $raw = $input->getOption($name);

            if ($schema->isArray($name)) {
                if ([] === (array) $raw) {
                    continue;
                }
                $elementType = $schema->elementType($name);
                $values = [];
                foreach ((array) $raw as $element) {
                    $cast = $this->cast((string) $element, $elementType);
                    if (null === $cast) {
                        $io->error(sprintf('--%s の要素 "%s" は %s で指定してください。', $name, $element, $elementType));

                        return Command::INVALID;
                    }
                    $values[] = $cast;
                }
                $arguments[$name] = $values;
            } elseif (null !== $raw) {
                $type = $schema->baseType($name);
                $cast = $this->cast((string) $raw, $type);
                if (null === $cast) {
                    $io->error(sprintf('--%s は %s で指定してください。', $name, $type));

                    return Command::INVALID;
                }
                $arguments[$name] = $cast;
            }
        }

        // 必須検証はキャスト後に走るため、 不正な数値は上の cast 失敗で先に弾かれる
        // (0 に化けて array_key_exists を素通りさせない)。
        foreach ($schema->requiredNames() as $required) {
            if (!\array_key_exists($required, $arguments)) {
                $io->error(sprintf('必須オプション --%s を指定してください。', $required));

                return Command::INVALID;
            }
        }

        $result = $this->invoker->call($this->toolName, $arguments);
        $result = \is_array($result) ? $result : ['result' => $result];

        $output->writeln($this->formatter->format($result));

        return Command::SUCCESS;
    }

    /**
     * CLI の文字列オプションを inputSchema の型へ検証しつつ変換する。
     * 数値型で不正な入力 (例 "abc") は黙って 0 にせず、 変換失敗として null を返す
     * (成功値は決して null にならないため、 null が失敗を一意に表す)。
     * 要素型が不明な配列は文字列のまま渡す (数値らしい文字列を勝手に int 化して float や
     * 先頭ゼロ ID を壊さない)。
     */
    private function cast(string $value, string $type): int|float|bool|string|null
    {
        return match ($type) {
            'integer' => false !== ($i = filter_var($value, FILTER_VALIDATE_INT)) ? $i : null,
            'number' => false !== ($f = filter_var($value, FILTER_VALIDATE_FLOAT)) ? $f : null,
            'boolean' => \in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true),
            default => $value,
        };
    }
}
