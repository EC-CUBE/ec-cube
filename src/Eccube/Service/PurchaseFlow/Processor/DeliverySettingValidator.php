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

use Eccube\Entity\ItemInterface;
use Eccube\Repository\DeliveryRepository;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 販売種別に配送業者が設定されているかどうか.
 */
class DeliverySettingValidator extends ItemValidator
{
    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * DeliverySettingValidator constructor.
     */
    public function __construct(DeliveryRepository $deliveryRepository)
    {
        $this->deliveryRepository = $deliveryRepository;
    }

    /**
     * validate
     *
     * @throws InvalidItemException
     */

    /**
     * @throws InvalidItemException
     */
    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        if (!$item->isProduct()) {
            return;
        }

        $SaleType = $item->getProductClass()->getSaleType();
        $Deliveries = $this->deliveryRepository->findBy(['SaleType' => $SaleType, 'visible' => true]);

        if (empty($Deliveries)) {
            $this->throwInvalidItemException('front.shopping.in_preparation', $item->getProductClass());
        }
    }

    /**
     * handle
     */
    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        $item->setQuantity(0);
    }
}
