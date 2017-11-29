<?php

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Annotation\Inject;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;

/**
 * 受注情報の日付更新.
 */
class UpdateDatePurchaseProcessor implements PurchaseProcessor
{
    /**
     * @var array
     */
    protected $appConfig;

    /**
     * UpdateDatePurchaseProcessor constructor.
     *
     * @param array $appConfig
     */
    public function __construct(array $appConfig)
    {
        $this->appConfig = $appConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ItemHolderInterface $TargetOrder, PurchaseContext $context)
    {
        $dateTime = new \DateTime();
        $OriginOrder = $context->getOriginHolder();

        // 編集
        if ($TargetOrder->getId()) {
            // 発送済
            if ($TargetOrder->getOrderStatus()->getId() == $this->appConfig['order_deliv']) {
                // 編集前と異なる場合のみ更新
                if ($TargetOrder->getOrderStatus()->getId() != $OriginOrder->getOrderStatus()->getId()) {
                    $TargetOrder->setCommitDate($dateTime);
                    // お届け先情報の発送日も更新する.
                    $Shippings = $TargetOrder->getShippings();
                    foreach ($Shippings as $Shipping) {
                        $Shipping->setShippingCommitDate($dateTime);
                    }
                }
                // 入金済
            } elseif ($TargetOrder->getOrderStatus()->getId() == $this->appConfig['order_pre_end']) {
                // 編集前と異なる場合のみ更新
                if ($TargetOrder->getOrderStatus()->getId() != $OriginOrder->getOrderStatus()->getId()) {
                    $TargetOrder->setPaymentDate($dateTime);
                }
            }
            // 新規
        } else {
            // 発送済
            if ($TargetOrder->getOrderStatus()->getId() == $this->appConfig['order_deliv']) {
                $TargetOrder->setCommitDate($dateTime);
                // お届け先情報の発送日も更新する.
                $Shippings = $TargetOrder->getShippings();
                foreach ($Shippings as $Shipping) {
                    $Shipping->setShippingCommitDate($dateTime);
                }
                // 入金済
            } elseif ($TargetOrder->getOrderStatus()->getId() == $this->appConfig['order_pre_end']) {
                $TargetOrder->setPaymentDate($dateTime);
            }
            // 受注日時
            $TargetOrder->setOrderDate($dateTime);
        }
        
        return ProcessResult::success();
    }
}
