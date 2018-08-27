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
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\PurchaseFlow\DiscountProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;

/**
 * 購入フローにおけるポイント処理.
 */
class PointProcessor implements DiscountProcessor, PurchaseProcessor
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
     * DiscountProcessors
     */

    /**
     * {@inheritdoc}
     */
    public function removeDiscountItem(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$this->supports($itemHolder)) {
            return;
        }

        $this->removePointDiscountItem($itemHolder);
    }

    /**
     * {@inheritdoc}
     */
    public function addDiscountItem(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$this->supports($itemHolder)) {
            return;
        }

        $usePoint = $itemHolder->getUsePoint();
        $discount = $this->pointToPrice($usePoint);

        // 利用ポイントがある場合は割引明細を追加
        if ($usePoint > 0) {
            $result = null;

            // 支払い金額 < 利用ポイントによる値引き額.
            if ($itemHolder->getTotal() + $discount < 0) {
                $minus = $itemHolder->getTotal() + $discount;
                // 利用ポイントが支払い金額を上回っていた場合は支払い金額が0円以上となるようにポイントを調整
                $overPoint = floor($minus / $this->BaseInfo->getPointConversionRate());
                $usePoint = $itemHolder->getUsePoint() + $overPoint;
                $discount = $this->pointToPrice($usePoint);
                $result = ProcessResult::warn('利用ポイントがお支払い金額を上回っています', self::class);
            }

            // 所有ポイント < 利用ポイント
            $Customer = $itemHolder->getCustomer();
            if ($Customer->getPoint() < $usePoint) {
                // 利用ポイントが所有ポイントを上回っていた場合は所有ポイントで上書き
                $usePoint = $Customer->getPoint();
                $discount = $this->pointToPrice($usePoint);
                $result = ProcessResult::warn('利用ポイントが所有ポイントを上回っています', self::class);
            }

            $itemHolder->setUsePoint($usePoint);
            $this->addPointDiscountItem($itemHolder, $discount);

            if ($result) {
                return $result;
            }
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
