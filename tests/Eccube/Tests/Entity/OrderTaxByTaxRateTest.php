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

namespace Eccube\Tests\Entity;

use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * 税率ごとの税額 (Order::getTaxByTaxRate()) が, 税込合計から割り戻した真値を 1 回だけ丸めた値になることを検証する.
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/7279
 */
final class OrderTaxByTaxRateTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string, int, string}>
     */
    public static function provideTaxByTaxRate(): iterable
    {
        // 真値がちょうど整数になる合計. 比率を先に切り捨てると 1 円少なくなっていた
        yield '切り捨て・10%・110 円' => ['110', '10', RoundingType::FLOOR, '10'];
        yield '切り捨て・8%・108 円' => ['108', '8', RoundingType::FLOOR, '8'];
        yield '切り捨て・10%・11,000 円' => ['11000', '10', RoundingType::FLOOR, '1000'];
        // 合計が大きいと誤差が 0.5 円の境界を越えていた
        yield '四捨五入・10%・500,011 円' => ['500011', '10', RoundingType::ROUND, '45456'];
        yield '四捨五入・8%・250,027 円' => ['250027', '8', RoundingType::ROUND, '18521'];
        yield '切り上げ・10%・1,000,000 円' => ['1000000', '10', RoundingType::CEIL, '90910'];
        yield '切り上げ・8%・500,000 円' => ['500000', '8', RoundingType::CEIL, '37038'];
        // 従来から正しかった値
        yield '四捨五入・10%・110 円' => ['110', '10', RoundingType::ROUND, '10'];
        yield '切り捨て・10%・108 円' => ['108', '10', RoundingType::FLOOR, '9'];
        yield '切り上げ・8%・110 円' => ['110', '8', RoundingType::CEIL, '9'];
    }

    #[DataProvider(methodName: 'provideTaxByTaxRate')]
    public function testGetTaxByTaxRate(string $total, string $rate, int $roundingType, string $expected): void
    {
        $Order = $this->createOrderWithSingleItem($total, $rate, $roundingType);

        $this->assertSame(0, bccomp($expected, $Order->getTaxByTaxRate()[$rate], 6));
    }

    /**
     * 0〜3,000 円のすべての税込合計で, 整数演算で求めた真値の丸めと一致する.
     */
    public function testGetTaxByTaxRateMatchesExactRounding(): void
    {
        $mismatches = [];
        foreach ([8, 10] as $rate) {
            foreach ([RoundingType::ROUND, RoundingType::FLOOR, RoundingType::CEIL] as $roundingType) {
                for ($total = 0; $total <= 3000; ++$total) {
                    $expected = self::roundExact($total * $rate, 100 + $rate, $roundingType);
                    $actual = $this->createOrderWithSingleItem((string) $total, (string) $rate, $roundingType)
                        ->getTaxByTaxRate()[(string) $rate];
                    if (bccomp((string) $expected, $actual, 6) !== 0) {
                        $mismatches[] = sprintf('total=%d rate=%d rounding=%d: %s (expected %d)', $total, $rate, $roundingType, $actual, $expected);
                    }
                }
            }
        }

        $this->assertSame([], array_slice($mismatches, 0, 20), sprintf('%d 件一致しません', count($mismatches)));
    }

    /**
     * 非負の整数 n / m (m > 0) を端数処理で丸める.
     */
    private static function roundExact(int $n, int $m, int $roundingType): int
    {
        return match ($roundingType) {
            RoundingType::ROUND => intdiv(2 * $n + $m, 2 * $m),
            RoundingType::FLOOR => intdiv($n, $m),
            default => intdiv($n + $m - 1, $m),
        };
    }

    /**
     * 課税の商品明細 1 行 (税込表示・数量 1) だけの受注. 税込合計がそのまま getTotalByTaxRate() になる.
     */
    private function createOrderWithSingleItem(string $total, string $rate, int $roundingType): Order
    {
        $Item = (new OrderItem())
            ->setPrice($total)
            ->setTax('0')
            ->setQuantity('1')
            ->setTaxRate($rate)
            ->setOrderItemType((new OrderItemType())->setId(OrderItemType::PRODUCT))
            ->setTaxType((new TaxType())->setId(TaxType::TAXATION))
            ->setTaxDisplayType((new TaxDisplayType())->setId(TaxDisplayType::INCLUDED))
            ->setRoundingType((new RoundingType())->setId($roundingType));

        $Order = new Order();
        $Order->addOrderItem($Item);
        $Item->setOrder($Order);

        return $Order;
    }
}
