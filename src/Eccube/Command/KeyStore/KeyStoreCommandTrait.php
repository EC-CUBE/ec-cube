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

namespace Eccube\Command\KeyStore;

use Eccube\Service\AgentCommerce\Security\KeyStoreEntry;
use Eccube\Service\AgentCommerce\Security\WebReadability;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 鍵の管理コマンドの共通処理 (出力形式と表示).
 */
trait KeyStoreCommandTrait
{
    /**
     * @var list<string>
     */
    private const FORMATS = ['table', 'json'];

    protected function addFormatOption(): void
    {
        $this->addOption('format', null, InputOption::VALUE_REQUIRED, sprintf('出力形式 (%s)', implode('|', self::FORMATS)), 'table');
    }

    protected function isValidFormat(string $format): bool
    {
        return in_array($format, self::FORMATS, true);
    }

    protected function invalidFormat(SymfonyStyle $io): void
    {
        $io->error(sprintf('--format は %s のいずれかで指定してください.', implode(' / ', self::FORMATS)));
    }

    /**
     * @param array<string, mixed>|list<array<string, mixed>> $data
     */
    protected function renderJson(OutputInterface $output, array $data): void
    {
        $output->writeln((string) json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * 鍵の状態を 1 語で表す.
     */
    protected function stateLabel(KeyStoreEntry $entry): string
    {
        if (!$entry->exists) {
            return '<comment>未生成</comment>';
        }

        return $entry->readable ? '<info>生成済み</info>' : '<error>読み取り不可</error>';
    }

    protected function webReadabilityLabel(WebReadability $readability): string
    {
        return match ($readability) {
            WebReadability::Readable => '<info>読み取り可</info>',
            WebReadability::Unreadable => '<error>読み取り不可</error>',
            WebReadability::Unknown => '<comment>不明</comment>',
        };
    }

    /**
     * 判定できなかった理由や対処を, 表の下へまとめて表示する.
     *
     * @param list<KeyStoreEntry> $entries
     */
    protected function renderHints(SymfonyStyle $io, array $entries): void
    {
        foreach ($entries as $entry) {
            $lines = array_values(array_filter([$entry->error, $entry->webReadabilityHint]));
            if ($lines === []) {
                continue;
            }

            $io->text(sprintf('%s:', $entry->purpose));
            foreach ($lines as $line) {
                $io->text('    '.$line);
            }
        }
    }
}
