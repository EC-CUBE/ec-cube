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

use Eccube\Service\AgentCommerce\Catalog\AgentCatalogItemDto;
use Eccube\Service\AgentCommerce\Catalog\CatalogMapper;
use Eccube\Service\AgentCommerce\Catalog\CatalogProviderInterface;
use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogCache;
use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogResponseBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * UCP Catalog キャッシュを事前生成する開発者向けコマンド.
 *
 * UCP Catalog はリクエストボディ hash でキャッシュするため、本コマンドは最も頻度が高い
 * 既定の "search" (空ボディ = フィルタ無しの先頭ページ) を生成する。--limit で件数を指定できる。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/shopping/catalog_search.json
 */
#[AsCommand(name: 'eccube:ucp-catalog:cache:warmup', description: 'Warm up the UCP catalog cache for the default search response')]
class UcpCatalogCacheWarmupCommand extends Command
{
    /**
     * 生成する search のデフォルト件数.
     */
    private const DEFAULT_LIMIT = 10;

    public function __construct(
        private readonly UcpCatalogCache $cache,
        private readonly CatalogProviderInterface $catalogProvider,
        private readonly CatalogMapper $catalogMapper,
        private readonly UcpCatalogResponseBuilder $responseBuilder,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Number of products to include in the warmed default search page', (string) self::DEFAULT_LIMIT);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('UCP Catalog Cache Warmup');

        $limit = max(1, (int) $input->getOption('limit'));

        $io->text(sprintf('Generating the default search response (first %d display product(s))...', $limit));

        $body = $this->buildDefaultSearchBody($limit);

        // コントローラの decodeBody は空ボディ / "{}" のいずれも空 payload として同一の
        // 既定 search を返すため、両ボディのキャッシュキーを生成する。
        foreach (['', '{}'] as $rawBody) {
            $key = $this->cache->buildKey('search', $rawBody);
            $this->cache->warmup($key, fn (): string => $body);
        }

        $io->success('UCP catalog cache warmed up for the default search response.');

        return Command::SUCCESS;
    }

    /**
     * フィルタ無し先頭ページの search レスポンス本文 (JSON) を生成する.
     */
    private function buildDefaultSearchBody(int $limit): string
    {
        $items = [];
        foreach ($this->catalogProvider->provideDisplayProducts() as $product) {
            $item = $this->catalogMapper->mapProduct($product);
            if ($item instanceof AgentCatalogItemDto) {
                $items[] = $item;
            }
            if (\count($items) >= $limit) {
                break;
            }
        }

        $response = $this->responseBuilder->buildSearchResponse($items, ['has_next_page' => false]);

        return json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
