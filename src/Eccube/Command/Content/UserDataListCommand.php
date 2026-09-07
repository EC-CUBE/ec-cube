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

use Eccube\Exception\ContentValidationException;
use Eccube\Service\Content\UserDataFileService;
use Eccube\Util\FilesystemUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'eccube:user-data:list', description: 'html/user_data 配下のファイルを一覧します.')]
final class UserDataListCommand extends Command
{
    use ContentCommandTrait;

    public function __construct(private readonly UserDataFileService $userDataFileService)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('path', null, InputOption::VALUE_REQUIRED, '一覧するディレクトリ (html/user_data からの相対パス. 既定はルート)')
            ->addOption('recursive', null, InputOption::VALUE_NONE, '配下を再帰的に一覧する');
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
            $entries = $this->userDataFileService->list(
                $input->getOption('path'),
                (bool) $input->getOption('recursive')
            );
        } catch (ContentValidationException $e) {
            $io->error($e->getErrors());

            return 1;
        }

        if ('json' === $format) {
            $output->writeln((string) json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return 0;
        }

        $io->table(
            ['パス', '種別', 'サイズ', '更新日時'],
            array_map(static fn (array $entry): array => [
                $entry['path'],
                $entry['is_dir'] ? 'dir' : 'file',
                $entry['is_dir'] ? '-' : FilesystemUtil::sizeToHumanReadable($entry['size']),
                date('Y-m-d H:i:s', $entry['mtime']),
            ], $entries)
        );

        return 0;
    }
}
