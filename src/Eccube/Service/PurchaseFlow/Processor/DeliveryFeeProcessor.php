<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Service\PurchaseFlow\Processor;


use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\ShipmentItem;
use Eccube\Entity\Shipping;
use Eccube\Service\PurchaseFlow\ItemHolderProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;

/**
 * 送料明細追加.
 */
class DeliveryFeeProcessor implements ItemHolderProcessor
{

    private $app;

    /**
     * DeliveryFeeProcessor constructor.
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @return ProcessResult
     */
    public function process(ItemHolderInterface $itemHolder)
    {
        if ($this->containsDeliveryFeeItem($itemHolder) == false) {
            $this->addDeliveryFeeItem($itemHolder);
        }
        return ProcessResult::success();
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @return bool
     */
    private function containsDeliveryFeeItem(ItemHolderInterface $itemHolder)
    {
        foreach ($itemHolder->getItems() as $item) {
            if ($item->isDeliveryFee()) {
                return true;
            }
        }
        return false;
    }

    /**
     * TODO 送料無料計算
     * @param ItemHolderInterface $itemHolder
     */
    private function addDeliveryFeeItem(ItemHolderInterface $itemHolder)
    {
        $DeliveryFeeType = $this->app['eccube.repository.master.order_item_type']->find(OrderItemType::DELIVERY_FEE);
        // TODO
        $TaxInclude = $this->app['orm.em']->getRepository(TaxDisplayType::class)->find(TaxDisplayType::INCLUDED);
        $Taxion = $this->app['orm.em']->getRepository(TaxType::class)->find(TaxType::TAXATION);

        /** @var Order $Order */
        $Order = $itemHolder;
        /* @var Shipping $Shipping */
        foreach ($Order->getShippings() as $Shipping) {
            $ShipmentItem = new ShipmentItem();
            $ShipmentItem->setProductName("送料")
                ->setPrice($Shipping->getShippingDeliveryFee())
                ->setPriceIncTax($Shipping->getShippingDeliveryFee())
                ->setTaxRate(8)
                ->setQuantity(1)
                ->setOrderItemType($DeliveryFeeType)
                ->setShipping($Shipping)
                ->setTaxDisplayType($TaxInclude)
                ->setTaxType($Taxion);

            $itemHolder->addItem($ShipmentItem);
            $Shipping->addShipmentItem($ShipmentItem);
        }
    }
}
