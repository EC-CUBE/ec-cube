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

use Eccube\Service\Docs\DocsExportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * コード近接の仕様書（README.html）を集約・出力するコマンド（Issue #6906 §8.2）.
 *
 * 例:
 *   bin/console eccube:docs:export                      # 開発者向けフル版を var/docs/all へ
 *   bin/console eccube:docs:export --filter=customer    # 顧客提出向けフィルタ版を var/docs/customer へ
 *
 * 出力 HTML はブラウザの「印刷 → PDF」で顧客提出用 PDF に変換できる。
 */
#[AsCommand(
    name: 'eccube:docs:export',
    description: 'Export colocated README.html specs, optionally filtered for customer delivery',
)]
class DocsExportCommand extends Command
{
    public function __construct(
        private readonly DocsExportService $docsExportService,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('filter', null, InputOption::VALUE_REQUIRED, sprintf('Section filter: "%s" or "%s"', DocsExportService::FILTER_ALL, DocsExportService::FILTER_CUSTOMER), DocsExportService::FILTER_ALL)
            ->addOption('source', null, InputOption::VALUE_REQUIRED, 'Directory to scan for README.html (defaults to the project root)')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output directory (defaults to <project>/var/docs/<filter>)')
            ->setHelp('全 README.html を集約して出力します。--filter=customer で data-customer="true" の章だけを抽出します。');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filter = (string) $input->getOption('filter');
        $source = $input->getOption('source') ?: $this->projectDir;
        $outputDir = $input->getOption('output') ?: $this->projectDir.'/var/docs/'.$filter;

        try {
            $written = $this->docsExportService->export($source, $outputDir, $filter);
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return 1;
        }

        $io->success(sprintf('Exported %d README.html file(s) to "%s" (filter: %s).', \count($written), $outputDir, $filter));

        return 0;
    }
}
