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

/**
 * html/user_data 配下へファイルを配置する (upsert).
 *
 * html/ はドキュメントルートのため, ファイル名と拡張子は管理画面のアップロードと同じ検証を通す.
 */
#[AsCommand(name: 'eccube:user-data:put', description: 'html/user_data 配下へファイルを配置します.')]
final class UserDataPutCommand extends Command
{
    use ContentCommandTrait;

    public function __construct(private readonly UserDataFileService $userDataFileService)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addOption('path', null, InputOption::VALUE_REQUIRED, '配置先 (html/user_data からの相対パス)');
        // 静的ファイルのためキャッシュの削除は不要
        $this->addWriteOptions(false);
        $this->setHelp(<<<'EOF'
            <info>%command.name%</info> は html/user_data 配下へファイルを配置します.

              <info>cat logo.png | php %command.full_name% --path=assets/img/logo.png --body=-</info>
              <info>php %command.full_name% --path=guide.html --body-file=guide.html --dry-run</info>

            中間ディレクトリは自動で作成します. 配置できる拡張子は管理画面のファイル管理と同じで,
            eccube_file_uploadable_extensions で定義します.
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

        $path = $input->getOption('path');
        if (null === $path || '' === $path) {
            $io->error('--path を指定してください.');

            return Command::INVALID;
        }

        try {
            $body = $this->readBody($input);
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        }

        if (null === $body) {
            $io->error('--body または --body-file を指定してください.');

            return Command::INVALID;
        }

        $dryRun = (bool) $input->getOption('dry-run');

        try {
            $result = $this->userDataFileService->write((string) $path, $body, $dryRun);
        } catch (ContentValidationException $e) {
            $io->error(array_merge([sprintf('配置できません: %s', (string) $path)], $e->getErrors()));

            return 1;
        } catch (ContentWriteException $e) {
            return $this->reportWriteFailure($io, sprintf('配置できません: %s', (string) $path), $e);
        }

        $this->renderResult($io, $output, $format, $result, $dryRun);

        return 0;
    }
}
