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
use Eccube\Repository\BlockRepository;
use Eccube\Service\Content\BlockContentService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'eccube:block:list', description: 'ブロックの一覧を表示します.')]
final class BlockListCommand extends Command
{
    use ContentCommandTrait;

    public function __construct(
        private readonly BlockRepository $blockRepository,
        private readonly BlockContentService $blockContentService,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addOption('device-type', null, InputOption::VALUE_REQUIRED, 'デバイス種別 ID', (string) DeviceType::DEVICE_TYPE_PC);
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
            $DeviceType = $this->blockContentService->getDeviceType((int) $input->getOption('device-type'));
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        }

        $rows = [];
        /** @var Block $Block */
        foreach ($this->blockRepository->getList($DeviceType) as $Block) {
            $rows[] = [
                'id' => $Block->getId(),
                'name' => (string) $Block->getName(),
                'file_name' => (string) $Block->getFileName(),
                'deletable' => $Block->isDeletable(),
                'path' => $this->blockContentService->getFilePath($Block),
            ];
        }

        if ('json' === $format) {
            $output->writeln((string) json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return 0;
        }

        $io->table(
            ['ID', 'ブロック名', 'ファイル名', '削除可'],
            array_map(static fn (array $row): array => [
                $row['id'],
                $row['name'],
                $row['file_name'],
                $row['deletable'] ? 'yes' : 'no',
            ], $rows)
        );

        return 0;
    }
}
