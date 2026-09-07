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
use Eccube\Exception\ContentWriteException;
use Eccube\Service\Content\UserDataFileService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'eccube:user-data:remove', description: 'html/user_data 配下のファイルを削除します.')]
final class UserDataRemoveCommand extends Command
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
            ->addOption('path', null, InputOption::VALUE_REQUIRED, '削除するファイル・ディレクトリ (html/user_data からの相対パス)')
            ->addOption('recursive', null, InputOption::VALUE_NONE, '空でないディレクトリを配下ごと削除する')
            ->addOption('force', 'f', InputOption::VALUE_NONE, '確認せずに削除する')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, '削除対象を表示するだけで実行しない');
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

        $path = $input->getOption('path');
        if (null === $path || '' === $path) {
            $io->error('--path を指定してください.');

            return Command::INVALID;
        }

        $dryRun = (bool) $input->getOption('dry-run');

        // eccube:page:remove / eccube:block:remove と同じく既定で確認する
        if (!$dryRun && !$input->getOption('force') && !$io->confirm(sprintf('%s を削除しますか?', (string) $path), false)) {
            $io->text('中止しました.');

            return 1;
        }

        try {
            $result = $this->userDataFileService->remove(
                (string) $path,
                (bool) $input->getOption('recursive'),
                $dryRun
            );
        } catch (ContentValidationException $e) {
            $io->error(array_merge([sprintf('削除できません: %s', (string) $path)], $e->getErrors()));

            return 1;
        } catch (ContentWriteException $e) {
            return $this->reportWriteFailure($io, sprintf('削除できません: %s', (string) $path), $e);
        }

        $this->renderResult($io, $output, $format, $result, $dryRun);

        return 0;
    }
}
