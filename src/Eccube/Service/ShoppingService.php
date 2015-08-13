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

namespace Eccube\Service;

use Eccube\Application;
use Eccube\Util\Str;

class ShoppingService
{
    /** @var \Eccube\Application */
    public $app;

    /** @var \Eccube\Service\CartService */
    protected $cartService;

    /** @var \Eccube\Service\OrderService */
    protected $orderService;


    public function __construct(Application $app, $cartService, $orderService)
    {
        $this->app = $app;
        $this->cartService = $cartService;
        $this->orderService = $orderService;
    }

    /**
     * セッションにセットされた受注情報を取得
     *
     * @return null|object
     */
    public function getOrder()
    {

        // 受注データを取得
        $preOrderId = $this->cartService->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array(
            'pre_order_id' => $preOrderId,
            'OrderStatus' => $this->app['config']['order_processing']
        ));

        return $Order;

    }

    /**
     * 受注情報を作成
     *
     * @param $Customer
     * @return \Eccube\Entity\Order
     */
    public function createOrder($Customer)
    {
        // ランダムなpre_order_idを作成
        $preOrderId = sha1(Str::random(32));

        // 受注情報、受注明細情報、お届け先情報、配送商品情報を作成
        $Order = $this->orderService->registerPreOrderFromCartItems($this->cartService->getCart()->getCartItems(), $Customer,
            $preOrderId);

        $this->cartService->setPreOrderId($preOrderId);
        $this->cartService->save();

        return $Order;
    }
}
