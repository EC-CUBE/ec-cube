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

use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogCache;
use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogSearcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * UCP Catalog キャッシュを事前生成する開発者向けコマンド.
 *
 * UCP Catalog は正規化したリクエストパラメータとオリジンでキャッシュするため、本コマンドは最も頻度が高い
 * フィルタ無しの先頭ページの "search" を生成する。--limit で件数 (= pagination.limit) を指定できる。
 * 本文は search エンドポイントと同じ {@link UcpCatalogSearcher} で作る (同じキーを共有するため)。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/shopping/catalog_search.json
 */
#[AsCommand(name: 'eccube:ucp-catalog:cache:warmup', description: 'Warm up the UCP catalog cache for the default search response')]
class UcpCatalogCacheWarmupCommand extends Command
{
    public function __construct(
        private readonly UcpCatalogCache $cache,
        private readonly UcpCatalogSearcher $searcher,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, sprintf('Number of products to include in the warmed default search page (1-%d)', UcpCatalogSearcher::MAX_LIMIT), (string) UcpCatalogSearcher::DEFAULT_LIMIT);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('UCP Catalog Cache Warmup');

        // search と同じ上限に収める (上限を超えた limit のキーは search から参照されない)。
        $limit = UcpCatalogSearcher::clampLimit((int) $input->getOption('limit'));

        $io->text(sprintf('Generating the default search response (first %d display product(s))...', $limit));

        // search のキャッシュキーは正規化済みパラメータとオリジンから作られる。CLI のオリジンは
        // router.request_context (framework.router.default_uri) で決まるため、公開 URL と一致させておくこと。
        $key = $this->cache->buildKey('search', UcpCatalogCache::searchParams(null, $limit, 0));
        $this->cache->warmup($key, fn (): string => $this->searcher->searchBody(null, $limit, 0));

        $io->success('UCP catalog cache warmed up for the default search response.');

        return Command::SUCCESS;
    }
}
