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
use Eccube\Entity\ProductClass;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    /**
     * カート画面.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        // カートの集計結果を取得
        $Cart = $app['eccube.service.cart']->getCart();
        $app['eccube.purchase.flow.cart']->execute($Cart);
        $app['eccube.service.cart']->save();

        foreach ($Cart->getErrors() as $error) {
            $app->addRequestError($error);
        }

        // TODO purchaseFlow/itemHolderから取得できるように
        $least = 0;
        $quantity = 0;
        $isDeliveryFree = false;

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

    /**
     * カートに入っている商品の個数を1増やす.
     *
     * @param Application $app
     * @param Request $request
     * @param $productClassId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function up(Application $app, Request $request, $productClassId)
    {
        $this->isTokenValid($app);

        log_info('カート加算処理開始', array('product_class_id' => $productClassId));

        /** @var ProductClass $ProductClass */
        $ProductClass = $app['eccube.repository.product_class']->find($productClassId);

        if (is_null($ProductClass)) {
            return $app->redirect($app->url('cart'));
        }

        $Cart = $app['eccube.service.cart']->getCart();
        $Exists = $Cart->getCartItemByIdentifier(ProductClass::class, $ProductClass->getId());

        if ($Exists) {
            $Exists->setQuantity($Exists->getQuantity() + 1);
        }

        $app['eccube.purchase.flow.cart']->execute($Cart);
        $app['eccube.service.cart']->save();

        foreach ($Cart->getErrors() as $error) {
            $app->addRequestError($error);
        }

        log_info('カート加算処理終了', array('product_class_id' => $productClassId));

        return $app->redirect($app->url('cart'));
    }

    /**
     * カートに入っている商品の個数を1減らす.
     * マイナスになる場合は, 商品をカートから削除する.
     *
     * @param Application $app
     * @param Request $request
     * @param $productClassId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function down(Application $app, Request $request, $productClassId)
    {
        $this->isTokenValid($app);

        log_info('カート減算処理開始', array('product_class_id' => $productClassId));

        /** @var ProductClass $ProductClass */
        $ProductClass = $app['eccube.repository.product_class']->find($productClassId);

        if (is_null($ProductClass)) {
            return $app->redirect($app->url('cart'));
        }

        $Cart = $app['eccube.service.cart']->getCart();
        $Exists = $Cart->getCartItemByIdentifier(ProductClass::class, $ProductClass->getId());

        if ($Exists) {
            // 個数の減算
            // 個数が0以下になる場合は、PurchaseFlowで削除されるため、ここではハンドリングしない.
            $Exists->setQuantity($Exists->getQuantity() - 1);
        }

        $app['eccube.purchase.flow.cart']->execute($Cart);
        $app['eccube.service.cart']->save();

        foreach ($Cart->getErrors() as $error) {
            $app->addRequestError($error);
        }

        log_info('カート減算処理完了', array('product_class_id' => $productClassId));

        return $app->redirect($app->url('cart'));
    }

    /**
     * カートに入っている商品を削除する.
     *
     * @param Application $app
     * @param Request $request
     * @param $productClassId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function remove(Application $app, Request $request, $productClassId)
    {
        $this->isTokenValid($app);

        $this->isTokenValid($app);

        log_info('カート削除処理開始', array('product_class_id' => $productClassId));

        /** @var ProductClass $ProductClass */
        $ProductClass = $app['eccube.repository.product_class']->find($productClassId);

        if (is_null($ProductClass)) {
            return $app->redirect($app->url('cart'));
        }

        $Cart = $app['eccube.service.cart']->getCart();
        $Exists = $Cart->getCartItemByIdentifier(ProductClass::class, $ProductClass->getId());

        if ($Exists) {
            // 明細の削除
            // PurchaseFlowに削除させるため、0を設定.
            $Exists->setQuantity(0);
        }

        $app['eccube.purchase.flow.cart']->execute($Cart);
        $app['eccube.service.cart']->save();

        foreach ($Cart->getErrors() as $error) {
            $app->addRequestError($error);
        }

        log_info('カート削除処理開始', array('product_class_id' => $productClassId));

        return $app->redirect($app->url('cart'));
    }

    /**
     * カートをロック状態に設定し、購入確認画面へ遷移する.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function buystep(Application $app, Request $request)
    {
        // FRONT_CART_BUYSTEP_INITIALIZE
        $event = new EventArgs(
            array(),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_CART_BUYSTEP_INITIALIZE, $event);

        $app['eccube.service.cart']->lock();
        $app['eccube.service.cart']->save();

        // FRONT_CART_BUYSTEP_COMPLETE
        $event = new EventArgs(
            array(),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_CART_BUYSTEP_COMPLETE, $event);

        if ($event->hasResponse()) {
            return $event->getResponse();
        }

        return $app->redirect($app->url('shopping'));
    }
}
