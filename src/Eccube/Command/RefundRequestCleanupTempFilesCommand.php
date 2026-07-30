<?php

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

namespace Eccube\Command;

use Eccube\Service\RefundRequestService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 返品申請の期限切れ一時アップロードファイルを削除するコマンド.
 *
 * 入力 → 確認画面で離脱したユーザーの一時ファイルは自動削除されないため,
 * cron 等から定期実行して掃除することを想定する。
 */
#[AsCommand(name: 'eccube:refund-request:cleanup-temp-files', description: 'Delete expired refund request temporary upload files')]
class RefundRequestCleanupTempFilesCommand extends Command
{
    /** 既定の保持時間(時). この時間を超えて放置された一時ファイルを削除する. */
    private const DEFAULT_EXPIRE_HOURS = 24;

    public function __construct(private readonly RefundRequestService $refundRequestService)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addOption('hours', null, InputOption::VALUE_REQUIRED, 'Delete temp files older than this many hours', self::DEFAULT_EXPIRE_HOURS);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $hours = (int) $input->getOption('hours');
        if ($hours < 1) {
            $io->error('The "hours" option must be a positive integer.');

            return Command::INVALID;
        }

        $removed = $this->refundRequestService->cleanupExpiredTempDirs($hours * 3600);
        $io->success(sprintf('Deleted %d expired refund request temp directories (older than %d hours).', $removed, $hours));

        return Command::SUCCESS;
    }
}
