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


namespace Eccube\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class CartController
{
    public function index(Application $app)
    {
        $Cart = $app['eccube.service.cart']->getCart();

        /* @var $BaseInfo \Eccube\Entity\BaseInfo */
        /* @var $Cart \Eccube\Entity\Cart */
        $BaseInfo = $app['eccube.repository.base_info']->get();

        $isDeliveryFree = false;
        $least = 0;
        $quantity = 0;
        if ($BaseInfo->getDeliveryFreeAmount()) {
            if ($BaseInfo->getDeliveryFreeAmount() <= $Cart->getTotalPrice()) {
                // 送料無料（金額）を超えている
                $isDeliveryFree = true;
            } else {
                $least = $BaseInfo->getDeliveryFreeAmount() - $Cart->getTotalPrice();
            }
        }

        if ($BaseInfo->getDeliveryFreeQuantity()) {
            if ($BaseInfo->getDeliveryFreeQuantity() <= $Cart->getTotalQuantity()) {
                // 送料無料（個数）を超えている
                $isDeliveryFree = true;
            } else {
                $quantity = $BaseInfo->getDeliveryFreeQuantity() - $Cart->getTotalQuantity();
            }
        }

        return $app->render(
            'Cart/index.twig',
            array(
                'Cart' => $Cart,
                'least' => $least,
                'quantity' => $quantity,
                'is_delivery_free' => $isDeliveryFree,
            )
        );
    }

    public function add(Application $app, Request $request)
    {
        $productClassId = $request->get('product_class_id');
        $quantity = $request->request->has('quantity') ? $request->get('quantity') : 1;
        $app['eccube.service.cart']->addProduct($productClassId, $quantity)->save();

        return $app->redirect($app->url('cart'));
    }

    public function up(Application $app, $productClassId)
    {
        $app['eccube.service.cart']->upProductQuantity($productClassId)->save();

        return $app->redirect($app->url('cart'));
    }

    public function down(Application $app, $productClassId)
    {
        $app['eccube.service.cart']->downProductQuantity($productClassId)->save();

        return $app->redirect($app->url('cart'));
    }

    public function remove(Application $app, $productClassId)
    {
        $app['eccube.service.cart']->removeProduct($productClassId)->save();

        return $app->redirect($app->url('cart'));
    }

    public function setQuantity(Application $app, $productClassId, $quantity)
    {
        $app['eccube.service.cart']->setProductQuantity($productClassId, $quantity)->save();

        return $app->redirect($app->url('cart'));
    }

    public function buystep(Application $app)
    {
        $app['eccube.service.cart']->lock();
        $app['eccube.service.cart']->save();

        return $app->redirect($app->url('shopping'));
    }
}
