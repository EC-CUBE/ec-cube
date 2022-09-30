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

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Repository\DeliveryTimeRepository;
use Eccube\Entity\DeliveryTime;
use Eccube\Entity\Shipping;
/**
 * お届け時間法が一致しない明細がないかどうか.
 */
class DeliveryTimeValidator extends ItemHolderValidator
{
    /**
     * @var DeliveryTimeRepository
     */
    protected $deliveryTimeRepository;

    /**
     * DeliveryTimeProcessor constructor.
     *
     * @param DeliveryTimeRepository $deliveryRepository
     */
    public function __construct(DeliveryTimeRepository $deliveryTimeRepository)
    {
        $this->deliveryTimeRepository = $deliveryTimeRepository;
    }

    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {

        if (count($itemHolder->getItems()) <= 1) {
            return;
        }
        
        if ($itemHolder instanceof Order) { 
            $shippings = $itemHolder->getShippings();
            if (null === $shippings) {
                return;
            }
            $deliveryTimes = $this->getDeliveryTimes($shippings);
            foreach($deliveryTimes as $deliveryTime){
                if(!$deliveryTime->isVisible()){
                    $this->throwInvalidItemException('front.shopping.not_available_delivery_time');
                }
            }
        }
    }

    /**
     * @param Shipping[] $Shippings
     *
     * @return ArrayCollection|DeliveryTime[]
     */
    private function getDeliveryTimes($Shippings)
    {
        $DeliveryTimes = new ArrayCollection();
        foreach ($Shippings as $shipping) {
            if($shipping->getTimeId() != null){
                $DeliveryTimes->add($this->deliveryTimeRepository->find($shipping->getTimeId()));
            }
        }
        return $DeliveryTimes;
    }
}
