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
use Eccube\Service\AgentCommerce\Security\KeyStoreInspector;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * キーストアが扱う鍵の用途と, それぞれの状態を一覧表示する.
 */
#[AsCommand(name: 'eccube:keystore:list', description: '鍵の用途と保管状況を一覧表示します.', help: <<<'TXT'
<info>%command.name%</info> は鍵の用途と保管状況を一覧表示します.

  <info>php %command.full_name%</info>
  <info>php %command.full_name% --format=json</info>

鍵の値は表示しません. 公開してよい情報 (公開鍵 JWK 等) は
eccube:keystore:show で確認できます.
TXT)]
final class KeyStoreListCommand extends Command
{
    use KeyStoreCommandTrait;

    public function __construct(private readonly KeyStoreInspector $keyStoreInspector)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addFormatOption();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $format = (string) $input->getOption('format');
        if (!$this->isValidFormat($format)) {
            $this->invalidFormat($io);

            return Command::INVALID;
        }

        $entries = $this->keyStoreInspector->inspectAll();

        if ($format === 'json') {
            $this->renderJson($output, array_map(static fn (KeyStoreEntry $entry): array => $entry->toArray(), $entries));

            return Command::SUCCESS;
        }

        if ($entries === []) {
            $io->warning('鍵の用途が 1 つも登録されていません.');

            return Command::SUCCESS;
        }

        $rows = [];
        foreach ($entries as $entry) {
            $rows[] = [
                $entry->purpose,
                $entry->label,
                $this->stateLabel($entry),
                $this->webReadabilityLabel($entry->webReadability),
                $entry->path ?? '-',
            ];
        }

        $io->table(['用途', '説明', '状態', 'Web サーバー', '保管先'], $rows);
        $this->renderHints($io, $entries);

        return Command::SUCCESS;
    }
}
