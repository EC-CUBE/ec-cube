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

namespace Eccube\Command\AgentCommerce;

use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutSessionTerminator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 在庫確保期限を過ぎたエージェントチェックアウトのセッションを expired にし、引き当てた在庫を戻すコマンド.
 *
 * 追加認証 (3DS) や非同期決済の待機中のセッションは在庫を引き当てたまま保持される。放置されたセッションの
 * 在庫を戻すため cron で定期実行する (例: 5 分ごと)。CLI を使えない環境では管理画面
 * (コンテンツ管理 > Agent Commerce) から同じ回収を実行できる。多重起動しても、セッションごとの行ロックで
 * 在庫を二重に戻さない。
 */
#[AsCommand(name: 'eccube:agent-commerce:checkout-session:expire', description: 'Expire overdue agent checkout sessions and release reserved stock')]
class CheckoutSessionExpireCommand extends Command
{
    /** 1 回のバッチで処理する件数. */
    private const BATCH_SIZE = 100;

    public function __construct(
        private readonly AgentCheckoutSessionTerminator $terminator,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Maximum number of sessions to expire (0 means no limit)', '0');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limit = max(0, (int) $input->getOption('limit'));
        $now = new \DateTime();

        $total = 0;
        do {
            $batch = $limit === 0 ? self::BATCH_SIZE : min(self::BATCH_SIZE, $limit - $total);
            $expired = $this->terminator->expireOverdue($now, $batch);
            $total += $expired;
            // 1 件も遷移しなかったバッチ (並行する complete が先に確定させた等) で打ち切り、無限ループを避ける。
        } while ($expired > 0 && ($limit === 0 || $total < $limit));

        $io->success(sprintf('Expired %d agent checkout session(s).', $total));

        return Command::SUCCESS;
    }
}
