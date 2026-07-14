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

        foreach ($this->properties() as $name => $spec) {
            $mode = InputOption::VALUE_REQUIRED;
            if ('array' === $this->baseType($spec['type'] ?? 'string')) {
                $mode |= InputOption::VALUE_IS_ARRAY;
            }
            $this->addOption($name, null, $mode, (string) ($spec['description'] ?? ''));
        }
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $arguments = [];
        foreach ($this->properties() as $name => $spec) {
            $raw = $input->getOption($name);
            $type = $this->baseType($spec['type'] ?? 'string');

            if ('array' === $type) {
                if ([] === (array) $raw) {
                    continue;
                }
                $elementType = $this->baseType($spec['items']['type'] ?? 'string');
                $values = [];
                foreach ((array) $raw as $element) {
                    [$ok, $cast] = $this->cast((string) $element, $elementType);
                    if (!$ok) {
                        $io->error(sprintf('--%s の要素 "%s" は %s で指定してください。', $name, $element, $elementType));

                        return Command::INVALID;
                    }
                    $values[] = $cast;
                }
                $arguments[$name] = $values;
            } elseif (null !== $raw) {
                [$ok, $cast] = $this->cast((string) $raw, $type);
                if (!$ok) {
                    $io->error(sprintf('--%s は %s で指定してください。', $name, $type));

                    return Command::INVALID;
                }
                $arguments[$name] = $cast;
            }
        }

        // 必須検証はキャスト後に走るため、 不正な数値は上の cast 失敗で先に弾かれる
        // (0 に化けて array_key_exists を素通りさせない)。
        foreach ($this->requiredProperties() as $required) {
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
     * @return array<string, array<string, mixed>>
     */
    private function properties(): array
    {
        /** @var array<string, array<string, mixed>> $properties */
        $properties = $this->invoker->tool($this->toolName)->tool->inputSchema['properties'] ?? [];

        return $properties;
    }

    /**
     * @return list<string>
     */
    private function requiredProperties(): array
    {
        /** @var list<string> $required */
        $required = $this->invoker->tool($this->toolName)->tool->inputSchema['required'] ?? [];

        return $required;
    }

    /**
     * JSON Schema の type から nullable を外した基底型を返す (例: ['null','integer'] → 'integer')。
     */
    private function baseType(mixed $type): string
    {
        if (\is_array($type)) {
            foreach ($type as $candidate) {
                if ('null' !== $candidate) {
                    return (string) $candidate;
                }
            }

            return 'string';
        }

        return \is_string($type) ? $type : 'string';
    }

    /**
     * CLI の文字列オプションを inputSchema の型へ検証しつつ変換する。
     * 数値型で不正な入力 (例 "abc") は黙って 0 にせず、 失敗を返して呼び出し側で弾かせる。
     * 要素型が不明な配列は文字列のまま渡す (数値らしい文字列を勝手に int 化して float や
     * 先頭ゼロ ID を壊さない)。
     *
     * @return array{0: bool, 1: int|float|bool|string} 変換成否と変換値
     */
    private function cast(string $value, string $type): array
    {
        return match ($type) {
            'integer' => false !== ($i = filter_var($value, FILTER_VALIDATE_INT)) ? [true, $i] : [false, ''],
            'number' => false !== ($f = filter_var($value, FILTER_VALIDATE_FLOAT)) ? [true, $f] : [false, ''],
            'boolean' => [true, \in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true)],
            default => [true, $value],
        };
    }
}
