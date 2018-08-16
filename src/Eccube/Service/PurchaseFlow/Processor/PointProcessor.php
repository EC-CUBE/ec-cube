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
use Eccube\Repository\BaseInfoRepository;
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
     * @param BaseInfoRepository $baseInfoRepository
     */
    public function __construct(EntityManagerInterface $entityManager, BaseInfoRepository $baseInfoRepository)
    {
        $this->entityManager = $entityManager;
        $this->BaseInfo = $baseInfoRepository->get();
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

        // 利用ポイントがある場合は割引明細を追加
        $this->removePointDiscountItem($itemHolder);
        if ($itemHolder->getUsePoint() > 0) {
            $discount = $this->pointToPrice($itemHolder->getUsePoint());
            $this->addPointDiscountItem($itemHolder, $discount);
        }

        // 付与ポイントを計算
        $addPoint = $this->calculateAddPoint($itemHolder);
        $itemHolder->setAddPoint($addPoint);
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

        // 所有ポイント < 利用ポイント
        $Customer = $itemHolder->getCustomer();
        if ($Customer->getPoint() < $itemHolder->getUsePoint()) {
            // 利用ポイントが所有ポイントを上回っていた場合は所有ポイントで上書き
            $itemHolder->setUsePoint($Customer->getPoint());
            $this->throwInvalidItemException('利用ポイントが所有ポイントを上回っています.');
        }

        // 支払い金額 < 利用ポイント
        if ($itemHolder->getTotal() < 0) {
            // 利用ポイントが支払い金額を上回っていた場合は支払い金額が0円以上となるようにポイントを調整
            $overPoint = floor($itemHolder->getTotal() / $this->BaseInfo->getPointConversionRate());
            $itemHolder->setUsePoint($itemHolder->getUsePoint() + $overPoint);
            $this->throwInvalidItemException('利用ポイントがお支払い金額を上回っています.');
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
        $Customer->setPoint($Customer->getPoint() - $itemHolder->getUsePoint());
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

            // TODO: ポイントは税抜き分しか割引されない、ポイント明細は税抜きのままでいいのか？
            if ($item->isPoint()) {
                $point = round($item->getPrice() * ($pointRate / 100)) * $item->getQuantity();
            } else {
                // ポイント = 単価 * ポイント付与率 * 数量
                $point = round($item->getPriceIncTax() * ($pointRate / 100)) * $item->getQuantity();
            }

            return $carry + $point;
        }, 0);

        return $totalPoint < 0 ? 0 : $totalPoint;
    }

    /**
     * 明細追加処理.
     *
     * @param ItemHolderInterface $itemHolder
     * @param integer $discount
     */
    private function addPointDiscountItem(ItemHolderInterface $itemHolder, $discount)
    {
        $DiscountType = $this->entityManager->find(OrderItemType::class, OrderItemType::POINT);
        $TaxInclude = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $Taxation = $this->entityManager->find(TaxType::class, TaxType::NON_TAXABLE);

        // TODO TaxProcessorが先行して実行されるため, 税額等の値は個別にセットする.
        $OrderItem = new OrderItem();
        $OrderItem->setProductName($DiscountType->getName())
            ->setPrice($discount)
            ->setQuantity(1)
            ->setTax(0)
            ->setTaxRate(0)
            ->setTaxRuleId(null)
            ->setRoundingType(null)
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
     * @param integer $point int ポイント
     *
     * @return int 金額
     */
    private function pointToPrice($point)
    {
        return intval($point * $this->BaseInfo->getPointConversionRate()) * -1;
    }
}
