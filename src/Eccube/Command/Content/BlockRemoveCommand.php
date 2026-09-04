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

namespace Eccube\Command\Content;

use Eccube\Entity\Block;
use Eccube\Entity\Master\DeviceType;
use Eccube\Service\Content\BlockContentService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * ブロック (dtb_block + twig) を削除する.
 *
 * 管理画面と同じく, 削除可能なブロックのみ削除できる.
 */
#[AsCommand(name: 'eccube:block:remove', description: 'ブロックを削除します.')]
final class BlockRemoveCommand extends Command
{
    use ContentCommandTrait;

    public function __construct(private readonly BlockContentService $blockContentService)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('file-name', null, InputOption::VALUE_REQUIRED, '対象ブロックのファイル名')
            ->addOption('device-type', null, InputOption::VALUE_REQUIRED, 'デバイス種別 ID', (string) DeviceType::DEVICE_TYPE_PC)
            ->addOption('force', 'f', InputOption::VALUE_NONE, '確認せずに削除する')
            ->addOption('no-cache-clear', null, InputOption::VALUE_NONE, 'キャッシュの削除を省略する');
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

        $fileName = $input->getOption('file-name');
        if (null === $fileName || '' === $fileName) {
            $io->error('--file-name を指定してください.');

            return Command::INVALID;
        }

        try {
            $DeviceType = $this->blockContentService->getDeviceType((int) $input->getOption('device-type'));
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        }

        $Block = $this->blockContentService->findByFileName((string) $fileName, $DeviceType);
        if (!$Block instanceof Block) {
            $io->error(sprintf('ブロックが見つかりません: %s', (string) $fileName));

            return 1;
        }

        if (!$Block->isDeletable()) {
            $io->error(sprintf('削除できないブロックです: %s', (string) $fileName));

            return 1;
        }

        if (!$input->getOption('force') && !$io->confirm(sprintf('%s を削除しますか?', (string) $fileName), false)) {
            $io->text('中止しました.');

            return 1;
        }

        $result = $this->blockContentService->remove($Block);

        $this->renderResult($io, $output, $format, $result, false);

        if ($input->getOption('no-cache-clear')) {
            return 0;
        }

        return $this->clearContentCache($io) ? 0 : self::EXIT_MANUAL_ACTION_REQUIRED;
    }
}
