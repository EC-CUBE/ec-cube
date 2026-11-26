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

namespace Eccube\Tests\Formal;

use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Service\TaxRuleService;
use PHPUnit\Framework\TestCase;

/**
 * 税計算の PHP 実装を、Lean 4 のモデル（formal/Eccube/Tax/）から生成したテストベクタと突き合わせる.
 *
 * ベクタの期待値は bcmath の途中の切り捨てまで写した実装モデルの出力である。
 * このテストでモデルが PHP 実装を正しく写していることを確かめ、モデルと仕様（真値を 1 回だけ丸めた値）の
 * 一致は Lean の定理がすべての入力について保証する。ベクタの再生成手順は formal/README.md を参照。
 */
final class TaxDifferentialTest extends TestCase
{
    private const VECTORS = __DIR__.'/vectors/tax.json';

    /** 失敗時に表示する不一致の件数の上限 */
    private const MAX_REPORTED = 20;

    /** @var array<string, list<list<int|string>>>|null */
    private static ?array $vectors = null;

    private TaxRuleService $taxRuleService;

    protected function setUp(): void
    {
        // calcTax / calcTaxIncluded はコンストラクタで受け取る依存を使わない
        $this->taxRuleService = (new \ReflectionClass(TaxRuleService::class))->newInstanceWithoutConstructor();
    }

    public function testRoundByRoundingType(): void
    {
        $mismatches = [];
        foreach ($this->vectors('roundByRoundingType') as [$value, $roundingType, $expected]) {
            $actual = TaxRuleService::roundByRoundingType((string) $value, (int) $roundingType);
            if (bccomp($actual, (string) $expected, 6) !== 0) {
                $mismatches[] = sprintf('roundByRoundingType(%s, %d) = %s, model = %s', $value, $roundingType, $actual, $expected);
            }
        }

        $this->assertNoMismatch($mismatches);
    }

    public function testCalcTax(): void
    {
        $mismatches = [];
        foreach ($this->vectors('calcTax') as [$price, $rate, $adjust, $roundingType, $expected]) {
            $actual = $this->taxRuleService->calcTax((string) $price, (string) $rate, (int) $roundingType, (string) $adjust);
            if (bccomp($actual, (string) $expected, 6) !== 0) {
                $mismatches[] = sprintf('calcTax(%s, %s, %d, %s) = %s, model = %s', $price, $rate, $roundingType, $adjust, $actual, $expected);
            }
        }

        $this->assertNoMismatch($mismatches);
    }

    public function testCalcTaxIncluded(): void
    {
        $mismatches = [];
        foreach ($this->vectors('calcTaxIncluded') as [$price, $rate, $adjust, $roundingType, $expected]) {
            $actual = $this->taxRuleService->calcTaxIncluded((string) $price, (string) $rate, (int) $roundingType, (string) $adjust);
            if (bccomp($actual, (string) $expected, 6) !== 0) {
                $mismatches[] = sprintf('calcTaxIncluded(%s, %s, %d, %s) = %s, model = %s', $price, $rate, $roundingType, $adjust, $actual, $expected);
            }
        }

        $this->assertNoMismatch($mismatches);
    }

    /**
     * 税率ごとの税額（インボイスの税率別消費税額）.
     *
     * 課税の商品明細 1 行（税込表示・数量 1）だけの受注を組み立て、税込合計がそのまま
     * getTotalByTaxRate() になる状態で getTaxByTaxRate() を確かめる。
     */
    public function testGetTaxByTaxRate(): void
    {
        $mismatches = [];
        foreach ($this->vectors('taxByRate') as [$total, $rate, $roundingType, $expected]) {
            $Order = $this->createOrderWithSingleItem((string) $total, (string) $rate, (int) $roundingType);
            $actual = $Order->getTaxByTaxRate()[(string) $rate] ?? null;
            if ($actual === null || bccomp($actual, (string) $expected, 6) !== 0) {
                $mismatches[] = sprintf('getTaxByTaxRate() total=%s rate=%s rounding=%d = %s, model = %s', $total, $rate, $roundingType, $actual ?? 'null', $expected);
            }
        }

        $this->assertNoMismatch($mismatches);
    }

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

    /**
     * @return list<list<int|string>>
     */
    private function vectors(string $name): array
    {
        if (self::$vectors === null) {
            $json = file_get_contents(self::VECTORS);
            $this->assertIsString($json, self::VECTORS.' を読み込めません');
            /** @var array<string, list<list<int|string>>> $decoded */
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            self::$vectors = $decoded;
        }

        $this->assertArrayHasKey($name, self::$vectors);
        $this->assertNotEmpty(self::$vectors[$name]);

        return self::$vectors[$name];
    }

    /**
     * @param list<string> $mismatches
     */
    private function assertNoMismatch(array $mismatches): void
    {
        $this->assertSame(
            [],
            array_slice($mismatches, 0, self::MAX_REPORTED),
            sprintf('Lean のモデルと %d 件一致しません（先頭 %d 件を表示）', count($mismatches), self::MAX_REPORTED)
        );
    }
}
