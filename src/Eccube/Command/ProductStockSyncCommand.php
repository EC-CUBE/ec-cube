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

use Eccube\Service\ProductStockSynchronizer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 在庫 (dtb_product_stock) と dtb_product_class の不整合を修復する.
 *
 * DQL の一括更新やネイティブ SQL で dtb_product_stock を書き換えた場合は, in_stock が再計算されない.
 * このコマンドで in_stock の再計算と dtb_product_stock の不足行の補完を行う.
 */
#[AsCommand(name: 'eccube:product-stock:sync', description: '在庫 (dtb_product_stock) と商品規格の不整合を修復します.')]
final class ProductStockSyncCommand extends Command
{
    private const SOURCE_PRODUCT_STOCK = 'product-stock';

    private const SOURCE_PRODUCT_CLASS = 'product-class';

    public function __construct(private readonly ProductStockSynchronizer $productStockSynchronizer)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, '修復せず, 不整合の内容だけを表示する')
            ->addOption(
                'source',
                null,
                InputOption::VALUE_REQUIRED,
                sprintf(
                    '旧列 (dtb_product_class.stock) と在庫数がずれている場合に採用する値 (%s: dtb_product_stock / %s: 旧列). 旧列が残っている場合だけ有効',
                    self::SOURCE_PRODUCT_STOCK,
                    self::SOURCE_PRODUCT_CLASS
                ),
                self::SOURCE_PRODUCT_STOCK
            );
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $source = (string) $input->getOption('source');
        if (!in_array($source, [self::SOURCE_PRODUCT_STOCK, self::SOURCE_PRODUCT_CLASS], true)) {
            $io->error(sprintf('--source は %s / %s のいずれかで指定してください.', self::SOURCE_PRODUCT_STOCK, self::SOURCE_PRODUCT_CLASS));

            return Command::INVALID;
        }

        $hasLegacy = $this->productStockSynchronizer->hasLegacyStockColumn();
        if ($source === self::SOURCE_PRODUCT_CLASS && !$hasLegacy) {
            $io->error('旧列 (dtb_product_class.stock) が無いため, --source=product-class は指定できません.');

            return Command::INVALID;
        }

        if ($input->getOption('dry-run')) {
            $this->renderPlan($io, $this->productStockSynchronizer->diagnose(), $hasLegacy, $source);

            return Command::SUCCESS;
        }

        $result = $this->productStockSynchronizer->repair($source === self::SOURCE_PRODUCT_CLASS);

        $io->table(['処理', '件数'], [
            ['dtb_product_stock の行を作成', $result['created']],
            ['重複した dtb_product_stock の行を 1 行にまとめた', $result['deduplicated']],
            ['旧列の値で dtb_product_stock を上書き', $result['overwritten']],
            ['旧列とずれていたが dtb_product_stock の値を採用 (ログに記録)', $result['mismatched']],
            ['在庫の有無 (in_stock) を更新', $result['in_stock_updated']],
        ]);
        $io->success('在庫の不整合を修復しました.');

        return Command::SUCCESS;
    }

    /**
     * @param array{
     *     missing: list<int>,
     *     duplicated: list<int>,
     *     in_stock_mismatched: list<int>|null,
     *     legacy_mismatched: list<array{product_class_id: int, product_class_stock: string|null, product_stock_stock: string|null}>|null
     * } $diagnosis
     */
    private function renderPlan(SymfonyStyle $io, array $diagnosis, bool $hasLegacy, string $source): void
    {
        $io->title('在庫の不整合の修復 (dry-run)');

        $rows = [
            [
                'dtb_product_stock の行を作成',
                count($diagnosis['missing']),
                $hasLegacy ? '旧列の値で作成' : '在庫数 0 (在庫無制限なら NULL) で作成',
            ],
            [
                '重複した dtb_product_stock の行を 1 行にまとめる',
                count($diagnosis['duplicated']),
                $hasLegacy ? '旧列の値でまとめる' : '最も少ない在庫数でまとめる',
            ],
        ];
        if ($diagnosis['legacy_mismatched'] !== null) {
            $rows[] = [
                '旧列と在庫数がずれている規格',
                count($diagnosis['legacy_mismatched']),
                $source === self::SOURCE_PRODUCT_CLASS ? '旧列の値で上書き' : 'dtb_product_stock の値を採用 (ログに記録)',
            ];
        }
        $rows[] = [
            '在庫の有無 (in_stock) を更新',
            $diagnosis['in_stock_mismatched'] === null ? '-' : count($diagnosis['in_stock_mismatched']),
            $diagnosis['in_stock_mismatched'] === null ? 'in_stock 列が無いため更新しない' : '在庫数から再計算',
        ];
        $io->table(['処理', '件数', '内容'], $rows);

        $io->note('dry-run のため, DB は変更していません. 在庫の有無の件数は, 行の作成・統合の前の値です.');
    }
}
