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
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * UCP Catalog キャッシュを破棄する開発者向けコマンド.
 *
 * 商品 / 在庫の更新を即時にカタログへ反映したい場合に利用する。
 */
#[AsCommand(name: 'eccube:ucp-catalog:cache:clear', description: 'Clear the UCP catalog cache')]
class UcpCatalogCacheClearCommand extends Command
{
    public function __construct(
        private readonly UcpCatalogCache $cache,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('UCP Catalog Cache Clear');

        $this->cache->clear();

        $io->success('UCP catalog cache cleared.');

        return Command::SUCCESS;
    }
}
