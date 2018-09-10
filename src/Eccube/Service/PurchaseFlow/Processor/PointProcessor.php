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
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Service\PointHelper;
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
     * @var PointHelper
     */
    protected $pointHelper;

    /**
     * PointProcessor constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param PointHelper $pointHelper
     */
    public function __construct(EntityManagerInterface $entityManager, PointHelper $pointHelper)
    {
        $this->entityManager = $entityManager;
        $this->pointHelper = $pointHelper;
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

        $this->pointHelper->removePointDiscountItem($itemHolder);
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
        $discount = $this->pointHelper->pointToDiscount($usePoint);

        // 利用ポイントがある場合は割引明細を追加
        if ($usePoint > 0) {
            $result = null;

            // 購入フロー実行時
            if ($context->isShoppingFlow()) {
                // 支払い金額 < 利用ポイントによる値引き額.
                if ($itemHolder->getTotal() + $discount < 0) {
                    $minus = $itemHolder->getTotal() + $discount;
                    // 利用ポイントが支払い金額を上回っていた場合は支払い金額が0円以上となるようにポイントを調整
                    $overPoint = $this->pointHelper->priceToPoint($minus);
                    $usePoint = $itemHolder->getUsePoint() + $overPoint;
                    $discount = $this->pointHelper->pointToDiscount($usePoint);
                    $result = ProcessResult::warn(trans('purchase_flow.over_payment_total'), self::class);
                }

                // 所有ポイント < 利用ポイント
                $Customer = $itemHolder->getCustomer();
                if ($Customer->getPoint() < $usePoint) {
                    // 利用ポイントが所有ポイントを上回っていた場合は所有ポイントで上書き
                    $usePoint = $Customer->getPoint();
                    $discount = $this->pointHelper->pointToDiscount($usePoint);
                    $result = ProcessResult::warn(trans('purchase_flow.over_customer_point'), self::class);
                }
                // 受注登録・編集実行時
            } else {
                // 支払い金額 < 利用ポイントによる値引き額.
                if ($itemHolder->getTotal() + $discount < 0) {
                    $result = ProcessResult::error(trans('purchase_flow.over_payment_total'), self::class);
                }
            }

            $itemHolder->setUsePoint($usePoint);
            $this->pointHelper->addPointDiscountItem($itemHolder, $discount);

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
        $this->pointHelper->prepare($itemHolder, $itemHolder->getUsePoint());
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

        $this->pointHelper->rollback($itemHolder, $itemHolder->getUsePoint());
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
        if (!$this->pointHelper->isPointEnabled()) {
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
}
