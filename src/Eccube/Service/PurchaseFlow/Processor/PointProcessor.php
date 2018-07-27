<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\PurchaseFlow\Processor;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ItemInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;

/**
 * 購入フローにおけるポイント処理.
 */
class PointProcessor extends ItemHolderValidator implements ItemHolderPreprocessor, PurchaseProcessor
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * AddPointProcessor constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param BaseInfo $BaseInfo
     */
    public function __construct(EntityManagerInterface $entityManager, BaseInfo $BaseInfo)
    {
        $this->entityManager = $entityManager;
        $this->BaseInfo = $BaseInfo;
    }

    /*
     * ItemHolderPreprocessor
     */

    /**
     * {@inheritdoc}
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$this->supports($itemHolder)) {
            return;
        }

        // 付与ポイントを計算
        $addPoint = $this->calculateAddPoint($itemHolder);
        $itemHolder->setAddPoint($addPoint);

        // 利用ポイントがある場合は割引明細を追加
        if ($itemHolder->getUsePoint() > 0) {
            $discount = $this->pointToPrice($itemHolder->getUsePoint());
            $this->removePointDiscountItem($itemHolder);
            $this->addPointDiscountItem($itemHolder, $discount);
        }
    }

    /*
     * ItemHolderValidator
     */

    /**
     * {@inheritdoc}
     */
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$this->supports($itemHolder)) {
            return;
        }

        // 支払い金額 < 利用ポイント
        $discount = $this->pointToPrice($itemHolder->getUsePoint());
        // TODO: 値引き後の金額と比較してしまっている
        if (($itemHolder->getTotal() + $discount) < 0) {
            $this->throwInvalidItemException('利用ポイントがお支払い金額を上回っています.');
        }

        // 所有ポイント < 利用ポイント
        $Customer = $itemHolder->getCustomer();
        if ($Customer->getPoint() < $itemHolder->getUsePoint()) {
            $this->throwInvalidItemException('利用ポイントが所有ポイントを上回っています.');
        }
    }

    /*
     * PurchaseProcessor
     */

    /**
     * {@inheritdoc}
     */
    public function prepare(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$this->supports($itemHolder)) {
            return;
        }

        // ユーザの保有ポイントを減算
        $Customer = $itemHolder->getCustomer();
        if ($Customer) {
            $Customer->setPoint($Customer->getPoint() - $itemHolder->getUsePoint());
        }
    }

    /**
     * {@inheritdoc
     */
    public function commit(ItemHolderInterface $target, PurchaseContext $context)
    {
        // 何もしない
    }

    /**
     * {@inheritdoc
     */
    public function rollback(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        // 利用したポイントをユーザに戻す.
        if (!$this->supports($itemHolder)) {
            return;
        }

        $Customer = $itemHolder->getCustomer();
        $Customer->setPoint($Customer->getPoint() + $itemHolder->getUsePoint());
    }

    /*
     * Helper methods
     */

    /**
     * Processorが実行出来るかどうかを返す.
     *
     * 以下を満たす場合に実行できる.
     *
     * - ポイント設定が有効であること.
     * - $itemHolderがOrderエンティティであること.
     * - 会員のOrderであること.
     *
     * @param ItemHolderInterface $itemHolder
     *
     * @return bool
     */
    private function supports(ItemHolderInterface $itemHolder)
    {
        if (!$this->BaseInfo->isOptionPoint()) {
            return false;
        }

        if (!$itemHolder instanceof Order) {
            return false;
        }

        if (!$itemHolder->getCustomer()) {
            return false;
        }

        return true;
    }

    /**
     * 付与ポイントを計算.
     *
     * @param ItemHolderInterface $itemHolder
     *
     * @return int
     */
    private function calculateAddPoint(ItemHolderInterface $itemHolder)
    {
        $basicPointRate = $this->BaseInfo->getBasicPointRate();

        // 明細ごとのポイントを集計
        $totalPoint = array_reduce($itemHolder->getItems()->toArray(), function ($carry, ItemInterface $item) use ($basicPointRate) {
            $pointRate = $item->getPointRate();
            if ($pointRate === null) {
                $pointRate = $basicPointRate;
            }

            // ポイント = 単価 * ポイント付与率 * 数量
            $point = round($item->getPriceIncTax() * ($pointRate / 100)) * $item->getQuantity();

            return $carry + $point;
        }, 0);

        /* 利用したポイントの割合に対して付与するポイントを減算
         * 明細のポイント合計 - (利用ポイント * ポイント付与率)
         *
         * 例) ポイント付与率10%で、1000円分購入したとき
         * ポイント利用なし -> 1000円 * 10% = 100ポイント付与
         * 500ポイント利用して購入 -> (1000円 - 500p) * 10% = 50ポイント付与
         */
        $totalPoint -= intval($itemHolder->getUsePoint() * $basicPointRate / 100);

        return $totalPoint < 0 ? 0 : $totalPoint;
    }

    /**
     * 明細追加処理.
     *
     * @param ItemHolderInterface $itemHolder
     * @param $discount
     */
    private function addPointDiscountItem(ItemHolderInterface $itemHolder, $discount)
    {
        $DiscountType = $this->entityManager->find(OrderItemType::class, OrderItemType::POINT);
        $TaxInclude = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $Taxation = $this->entityManager->find(TaxType::class, TaxType::TAXATION);

        $OrderItem = new OrderItem();
        $OrderItem->setProductName('ポイント値引')
            ->setPrice($discount)
            ->setPriceIncTax($discount)
            ->setTaxRate(8)
            ->setQuantity(1)
            ->setOrderItemType($DiscountType)
            ->setTaxDisplayType($TaxInclude)
            ->setTaxType($Taxation)
            ->setOrder($itemHolder);
        $itemHolder->addItem($OrderItem);
    }

    /**
     * 既存のポイント明細を削除する.
     *
     * @param ItemHolderInterface $itemHolder
     */
    private function removePointDiscountItem(ItemHolderInterface $itemHolder)
    {
        foreach ($itemHolder->getItems() as $item) {
            if ($item->isPoint()) {
                $itemHolder->removeOrderItem($item);
                $this->entityManager->remove($item);
            }
        }
    }

    /**
     * ポイントを金額に変換する.
     *
     * @param $point int ポイント
     *
     * @return int 金額
     */
    private function pointToPrice($point)
    {
        return intval($point * $this->BaseInfo->getPointConversionRate()) * -1;
    }
}
