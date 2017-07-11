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


use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Application;
use Eccube\Entity\Cart;
use Eccube\Entity\Delivery;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\ProductType;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\ItemValidateException;
use Eccube\Service\PurchaseFlow\ValidatableItemHolderProcessor;

/**
 * 支払い方法が一致しない明細がないかどうか.
 */
class PaymentProcessor extends ValidatableItemHolderProcessor
{
    /**
     * @var Application
     */
    private $app;

    /**
     * DeliveryFeeProcessor constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected function validate(ItemHolderInterface $itemHolder)
    {
        // 明細の個数が1以下の場合はOK
        if (count($itemHolder->getItems()) <= 1) {
            return;
        }

        // a, ab, c
        $i = 0;
        $paymentIds = [];
        foreach ($itemHolder->getItems() as $item) {
            if (false === $item->isProduct()) {
                continue;
            }
            $Deliveries = $this->getDeliveries($item->getProductClass()->getProductType());
            $Payments = $this->getPayments($Deliveries);

            $ids = [];
            foreach ($Payments as $Payment) {
                $ids[] = $Payment->getId();
            }
            if ($i === 0) {
                $paymentIds = $ids;
                $i++;
                continue;
            }

            $paymentIds = array_intersect($paymentIds, $ids);
        }

        // 共通項がなければエラー
        if (empty($paymentIds)) {
            throw new ItemValidateException();
        }
    }

    private function getDeliveries(ProductType $ProductType)
    {
        $Deliveries = $this->app['orm.em']->getRepository(Delivery::class)
            ->findBy(
                [
                    'ProductType' => $ProductType,
                ]
            );

        return $Deliveries;
    }

    private function getPayments($Deliveries)
    {
        $Payments = new ArrayCollection();
        foreach ($Deliveries as $Delivery) {
            $PaymentOptions = $Delivery->getPaymentOptions();
            foreach ($PaymentOptions as $PaymentOption) {
                $Payments->add($PaymentOption->getPayment());
            }
        }

        return $Payments;
    }
}
