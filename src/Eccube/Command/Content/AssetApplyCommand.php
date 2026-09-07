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
use Eccube\Service\Content\AssetContentService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * カスタマイズ用の CSS / JS (html/user_data/assets 配下) を保存する.
 *
 * 管理画面の CSS 管理 / JS 管理と同じファイルを対象にする.
 */
#[AsCommand(name: 'eccube:asset:apply', description: 'カスタマイズ用の CSS / JS を保存します.')]
final class AssetApplyCommand extends Command
{
    use ContentCommandTrait;

    public function __construct(private readonly AssetContentService $assetContentService)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addOption('type', null, InputOption::VALUE_REQUIRED, sprintf('対象の種別 (%s)', implode('|', $this->assetContentService->getTypes())));
        // 静的ファイルのためキャッシュの削除は不要
        $this->addWriteOptions(false);
        $this->setHelp(<<<'EOF'
            <info>%command.name%</info> は管理画面の CSS 管理 / JS 管理と同じファイルを保存します.

              <info>cat customize.css | php %command.full_name% --type=css --body=-</info>
              <info>php %command.full_name% --type=js --body-file=customize.js --dry-run</info>
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

        $type = (string) $input->getOption('type');
        if (!$this->assetContentService->isValidType($type)) {
            $io->error(sprintf('--type は %s のいずれかで指定してください.', implode(' / ', $this->assetContentService->getTypes())));

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
            $result = $this->assetContentService->apply($type, $body, $dryRun);
        } catch (ContentValidationException $e) {
            $io->error(array_merge([sprintf('保存できません: %s', $type)], $e->getErrors()));

            return 1;
        } catch (ContentWriteException $e) {
            return $this->reportWriteFailure($io, sprintf('保存できません: %s', $type), $e);
        }

        $this->renderResult($io, $output, $format, $result, $dryRun);

        return 0;
    }
}
