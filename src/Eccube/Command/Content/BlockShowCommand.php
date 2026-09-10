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

#[AsCommand(name: 'eccube:block:show', description: 'ブロックのテンプレートを出力します.')]
final class BlockShowCommand extends Command
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
            ->addOption('device-type', null, InputOption::VALUE_REQUIRED, 'デバイス種別 ID', (string) DeviceType::DEVICE_TYPE_PC);
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

        $body = $this->blockContentService->readTemplate($Block);

        if ('json' === $format) {
            $output->writeln((string) json_encode([
                'id' => $Block->getId(),
                'name' => $Block->getName(),
                'file_name' => $Block->getFileName(),
                'device_type_id' => $Block->getDeviceType()->getId(),
                'deletable' => $Block->isDeletable(),
                'path' => $this->blockContentService->getFilePath($Block),
                'body' => $body,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return 0;
        }

        $output->write($body);

        return 0;
    }
}
