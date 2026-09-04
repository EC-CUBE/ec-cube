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

use Eccube\Entity\Master\DeviceType;
use Eccube\Exception\ContentValidationException;
use Eccube\Service\Content\BlockContentService;
use Eccube\Service\Content\ContentStatus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * ブロック (dtb_block + twig) をファイル名を鍵に登録・更新する.
 */
#[AsCommand(name: 'eccube:block:apply', description: 'ブロックを登録・更新します.')]
final class BlockApplyCommand extends Command
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
            ->addOption('file-name', null, InputOption::VALUE_REQUIRED, '対象ブロックのファイル名 (登録・更新の鍵)')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'ブロック名')
            ->addOption('device-type', null, InputOption::VALUE_REQUIRED, 'デバイス種別 ID', (string) DeviceType::DEVICE_TYPE_PC);
        $this->addWriteOptions();
        $this->setHelp(<<<'EOF'
            <info>%command.name%</info> は dtb_block とテンプレートファイルを対で登録・更新します.

              <info>cat campaign.twig | php %command.full_name% --file-name=campaign --name=キャンペーン --body=-</info>
            EOF
        );
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
            $body = $this->readBody($input);
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        }

        $payload = [
            'file_name' => (string) $fileName,
            'device_type' => (int) $input->getOption('device-type'),
        ];
        if (null !== $body) {
            $payload['body'] = $body;
        }
        if (null !== $input->getOption('name')) {
            $payload['name'] = (string) $input->getOption('name');
        }

        $dryRun = (bool) $input->getOption('dry-run');

        try {
            $result = $this->blockContentService->apply($payload, $dryRun);
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        } catch (ContentValidationException $e) {
            $io->error(array_merge([sprintf('ブロックを保存できません: %s', (string) $fileName)], $e->getErrors()));

            return 1;
        }

        $this->renderResult($io, $output, $format, $result, $dryRun);

        if ($dryRun || ContentStatus::Unchanged === $result->status || $input->getOption('no-cache-clear')) {
            return 0;
        }

        return $this->clearContentCache($io) ? 0 : self::EXIT_MANUAL_ACTION_REQUIRED;
    }
}
