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

namespace Eccube\Service\PurchaseFlow\Processor;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Service\PointHelper;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;

/**
 * 受注編集におけるポイント処理.
 */
class PointDiffProcessor extends ItemHolderValidator implements PurchaseProcessor
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
     * PointDiffProcessor constructor.
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
     * ItemHolderValidator
     */

    /**
     * {@inheritdoc}
     */
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$this->supports($itemHolder, $context)) {
            return;
        }

        $diffUsePoint = $this->getDiffOfUsePoint($itemHolder, $context);

        // 所有ポイント < 新規利用ポイント
        $Customer = $itemHolder->getCustomer();
        if ($Customer->getPoint() < $diffUsePoint) {
            $this->throwInvalidItemException('purchase_flow.over_customer_point');
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
        if (!$this->supports($itemHolder, $context)) {
            return;
        }

        $diffUsePoint = $this->getDiffOfUsePoint($itemHolder, $context);

        // ユーザの保有ポイントを減算
        $this->pointHelper->prepare($itemHolder, $diffUsePoint);
    }

    /**
     * {@inheritdoc}
     */
    public function commit(ItemHolderInterface $target, PurchaseContext $context)
    {
        // 何もしない
    }

    /**
     * {@inheritdoc}
     */
    public function rollback(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$this->supports($itemHolder, $context)) {
            return;
        }

        $diffUsePoint = $this->getDiffOfUsePoint($itemHolder, $context);

        $this->pointHelper->rollback($itemHolder, $diffUsePoint);
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
     * - OrderStatusが新規受付、入金済み、対応中、発送済みのどれかであること
     * - 会員のOrderであること.
     * - PurchaseContextでOriginHolderが渡ってきている
     *
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @return bool
     */
    private function supports(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$this->pointHelper->isPointEnabled()) {
            return false;
        }

        if (!$itemHolder instanceof Order) {
            return false;
        }

        if (!$itemHolder->getOrderStatus()) {
            return false;
        }

        switch ($itemHolder->getOrderStatus()->getId()) {
            case OrderStatus::NEW:
            case OrderStatus::IN_PROGRESS:
            case OrderStatus::DELIVERED:
            case OrderStatus::PAID:
                break;
            default:
                return false;
        }

        if (!$itemHolder->getCustomer()) {
            return false;
        }

        if (is_null($context->getOriginHolder())) {
            return false;
        }

        return true;
    }

    /**
     * 利用ポイントの差を計算する
     * この差が新規利用ポイントとなる
     *
     * 使用ポイントが増えた場合プラスとなる
     * 50 -> 100 : 50
     * 100 -> 50 : -50
     *
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @return int
     */
    protected function getDiffOfUsePoint(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if ($context->getOriginHolder()) {
            $fromUsePoint = $context->getOriginHolder()->getUsePoint();
        } else {
            $fromUsePoint = 0;
        }
        $toUsePoint = $itemHolder->getUsePoint();

        return $toUsePoint - $fromUsePoint;
    }
}
