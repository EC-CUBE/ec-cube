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

use Eccube\Service\AgentCommerce\Security\KeyPurposeRegistry;
use Eccube\Service\AgentCommerce\Security\KeyStoreInspector;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 鍵 1 件の状態と, 公開してよい情報を表示する.
 *
 * 鍵素材そのものは表示しない. ACP の共有シークレットをエージェントへ渡す必要がある場合は,
 * 本コマンドが表示する保管先から直接読み出す (コマンド履歴や CI のログへ残さないため).
 */
#[AsCommand(name: 'eccube:keystore:show', description: '鍵の状態と公開情報を表示します.', help: <<<'TXT'
<info>%command.name%</info> は鍵の状態と公開情報を表示します.

  <info>php %command.full_name% ucp_signing</info>
  <info>php %command.full_name% ucp_signing --format=json</info>

表示するのは公開してよい情報だけです (署名鍵は公開鍵 JWK と kid,
共有シークレットはアルゴリズムと長さ). 鍵の値は表示しません.
TXT)]
final class KeyStoreShowCommand extends Command
{
    use KeyStoreCommandTrait;

    public function __construct(
        private readonly KeyStoreInspector $keyStoreInspector,
        private readonly KeyPurposeRegistry $keyPurposeRegistry,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('purpose', InputArgument::REQUIRED, '鍵の用途 (eccube:keystore:list で確認できます)');
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

        try {
            $purpose = $this->keyPurposeRegistry->get((string) $input->getArgument('purpose'));
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        }

        $entry = $this->keyStoreInspector->inspect($purpose, true);

        if ($format === 'json') {
            $this->renderJson($output, $entry->toArray());

            return $entry->exists && $entry->error === null ? Command::SUCCESS : Command::FAILURE;
        }

        $rows = [
            ['用途', $entry->purpose],
            ['説明', $entry->label],
            ['状態', $this->stateLabel($entry)],
            ['保管先', $entry->path ?? '-'],
            ['権限', $entry->permissions ?? '-'],
            ['所有者', $entry->owner ?? '-'],
            ['更新日時', $entry->updatedAt ?? '-'],
            ['Web サーバー', $this->webReadabilityLabel($entry->webReadability)],
        ];
        foreach ($entry->details as $key => $value) {
            $rows[] = [$key, is_scalar($value) ? (string) $value : (string) json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)];
        }

        $io->table(['項目', '値'], $rows);
        $this->renderHints($io, [$entry]);

        if (!$entry->exists) {
            $io->error(sprintf('%s の鍵は生成されていません. bin/console eccube:keystore:generate %s で生成してください.', $entry->purpose, $entry->purpose));

            return Command::FAILURE;
        }

        if ($entry->error !== null) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
