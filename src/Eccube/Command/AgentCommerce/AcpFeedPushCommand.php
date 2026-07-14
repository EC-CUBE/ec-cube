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

use Eccube\Service\AgentCommerce\Catalog\Acp\AcpFeedClientInterface;
use Eccube\Service\AgentCommerce\Catalog\Exception\AcpFeedException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * ACP Product Feed を OpenAI ホストの Feed API へ push する開発者向けコマンド.
 *
 * --full で products.jsonl / metadata.json の全置換 push、既定は --feed-id 指定済み feed への
 * 差分 upsert。acp_feed_enabled フラグ / base URL / api_key のいずれかが未設定なら警告して終了する
 * (国内未提供 / 検証目的限定)。シークレット (Bearer) は一切出力しない。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/openapi/openapi.feed.yaml
 */
#[AsCommand(name: 'eccube:acp-feed:push', description: 'Push the ACP product feed to the OpenAI-hosted Feed API')]
class AcpFeedPushCommand extends Command
{
    public function __construct(
        private readonly AcpFeedClientInterface $feedClient,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('full', null, InputOption::VALUE_NONE, 'Perform a full replacement push (products.jsonl + metadata.json) instead of a differential upsert')
            ->addOption('feed-id', null, InputOption::VALUE_REQUIRED, 'The target ACP feed id. For --full this is the feed metadata id; for upsert it is the existing feed id');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('ACP Product Feed Push');

        // 認証情報 (base URL + API key) 未設定時は AcpFeedClient が AcpFeedException を投げる
        // (専用の有効化フラグは持たない。Bearer 等のシークレットは出力しない)。
        $feedId = $input->getOption('feed-id');
        if (!\is_string($feedId) || $feedId === '') {
            $io->error('The --feed-id option is required.');

            return Command::INVALID;
        }

        $full = (bool) $input->getOption('full');

        try {
            if ($full) {
                $io->section('Full replacement push');
                $io->text('Generating products.jsonl and metadata.json, then registering the feed...');
                $result = $this->feedClient->pushFullReplacement($feedId);
                $io->success(sprintf('Full replacement push completed. %d product(s) generated for feed "%s".', $result['product_count'], $feedId));

                return Command::SUCCESS;
            }

            $io->section('Differential upsert push');
            $io->text(sprintf('Fetching current products from feed "%s" for differential upsert...', $feedId));

            // TODO: 「次回 push 対象」マークに基づく差分抽出 (stock_updated_at 等) は後続実装。
            //       現状は現行 feed の全商品を取得して再 upsert するプレースホルダ動作とする。
            $products = $this->feedClient->getFeedProducts($feedId);
            if ($products === []) {
                $io->note('No products to upsert.');

                return Command::SUCCESS;
            }

            $accepted = $this->feedClient->upsertProducts($feedId, $products);
            if ($accepted) {
                $io->success(sprintf('Upsert accepted for %d product(s) on feed "%s".', \count($products), $feedId));
            } else {
                $io->warning(sprintf('Upsert was not accepted by the Feed API for feed "%s".', $feedId));
            }

            return Command::SUCCESS;
        } catch (AcpFeedException $e) {
            // メッセージにシークレットは含まれない (client 側で除去済み)。
            $io->error('ACP Feed push failed: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
