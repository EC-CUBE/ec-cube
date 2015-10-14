<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Eccube\Tests\Repository;

use Eccube\Application;

class PaymentRepositoryTest extends AbstractRepositoryTestCase
{

    public function test_findPayment()
    {
        $app = $this->createApplication();

        $productTypes = array(7, 6);
        $productTypes = array_unique($productTypes);

        // $paymentOption = $app['eccube.repository.payment_option']->getPaymentOption($productTypes);
        $deliveries = $app['eccube.repository.delivery']->getDeliveries($productTypes);

        // 支払方法を取得
        $payments = $app['eccube.repository.payment']->findAllowedPayments($deliveries);

        if (count($productTypes) > 1) {
            $deliveries = $app['eccube.repository.delivery']->findAllowedDeliveries($productTypes, $payments);
        }

    }

}
