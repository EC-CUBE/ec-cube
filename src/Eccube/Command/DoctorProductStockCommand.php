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
 * 在庫 (dtb_product_stock) と dtb_product_class の整合を診断する. DB は変更しない.
 *
 * 4.3 以前からの更新前に実行すると, 更新時に削除される旧列 (dtb_product_class.stock) と
 * dtb_product_stock のずれも確認できる.
 */
#[AsCommand(name: 'eccube:doctor:product-stock', description: '在庫 (dtb_product_stock) と商品規格の整合を診断します.')]
final class DoctorProductStockCommand extends Command
{
    /**
     * @var list<string>
     */
    private const FORMATS = ['table', 'json'];

    /**
     * 表に表示する規格 ID の最大件数.
     */
    private const MAX_IDS = 20;

    public function __construct(private readonly ProductStockSynchronizer $productStockSynchronizer)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('format', null, InputOption::VALUE_REQUIRED, sprintf('出力形式 (%s)', implode('|', self::FORMATS)), 'table');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $format = (string) $input->getOption('format');
        if (!in_array($format, self::FORMATS, true)) {
            $io->error(sprintf('--format は %s のいずれかで指定してください.', implode(' / ', self::FORMATS)));

            return Command::INVALID;
        }

        $diagnosis = $this->productStockSynchronizer->diagnose();
        $hasError = $diagnosis['missing'] !== []
            || $diagnosis['duplicated'] !== []
            || !empty($diagnosis['in_stock_mismatched'])
            || !empty($diagnosis['legacy_mismatched']);

        if ($format === 'json') {
            $output->writeln((string) json_encode($diagnosis + ['ok' => !$hasError], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return $hasError ? 1 : 0;
        }

        $io->title('在庫の整合の診断');

        $rows = [
            $this->row('dtb_product_stock の行が無い規格', $diagnosis['missing']),
            $this->row('dtb_product_stock の行が重複している規格', $diagnosis['duplicated']),
            $this->row('在庫の有無 (in_stock) が在庫数と合わない規格', $diagnosis['in_stock_mismatched']),
            $this->row(
                '旧列 (dtb_product_class.stock) と在庫数がずれている規格',
                $diagnosis['legacy_mismatched'] === null ? null : array_column($diagnosis['legacy_mismatched'], 'product_class_id')
            ),
        ];
        $io->table(['項目', '件数', '規格 ID'], $rows);

        if (!empty($diagnosis['legacy_mismatched'])) {
            $io->table(
                ['規格 ID', 'dtb_product_class.stock (旧列)', 'dtb_product_stock.stock'],
                array_map(fn (array $row): array => [
                    $row['product_class_id'],
                    $row['product_class_stock'] ?? 'NULL',
                    $row['product_stock_stock'] ?? 'NULL',
                ], array_slice($diagnosis['legacy_mismatched'], 0, self::MAX_IDS))
            );
            $io->note([
                '旧列は更新時のマイグレーションで削除されます. 値がずれている規格は dtb_product_stock の値を採用します.',
                '旧列の値を採用する場合は, 更新の前に bin/console eccube:product-stock:sync --source=product-class を実行してください.',
            ]);
        }

        if ($hasError) {
            $io->error('不整合があります. bin/console eccube:product-stock:sync で修復できます (--dry-run で内容を確認できます).');

            return Command::FAILURE;
        }

        $io->success('不整合はありません.');

        return Command::SUCCESS;
    }

    /**
     * @param list<int>|null $ids null は対象の列が無く診断していないことを表す
     *
     * @return list<string|int>
     */
    private function row(string $label, ?array $ids): array
    {
        if ($ids === null) {
            return [$label, '-', '(対象の列が無いため診断していません)'];
        }

        $shown = implode(', ', array_slice($ids, 0, self::MAX_IDS));
        if (count($ids) > self::MAX_IDS) {
            $shown .= sprintf(', ... (他 %d 件)', count($ids) - self::MAX_IDS);
        }

        return [$label, count($ids), $shown];
    }
}
