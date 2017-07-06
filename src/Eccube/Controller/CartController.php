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
use Eccube\Entity\CartItem;
use Eccube\Entity\ProductClass;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
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

        // FRONT_CART_INDEX_INITIALIZE
        $event = new EventArgs(
            array(),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_CART_INDEX_INITIALIZE, $event);

        // FRONT_CART_INDEX_COMPLETE
        $event = new EventArgs(
            array(),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_CART_INDEX_COMPLETE, $event);

        if ($event->hasResponse()) {
            return $event->getResponse();
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

        // FRONT_CART_UP_INITIALIZE
        $event = new EventArgs(
            array(
                'ProductClass' => $ProductClass
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_CART_UP_INITIALIZE, $event);

        $Cart = $app['eccube.service.cart']->getCart();

        $Exists = $Cart->getCartItemByIdentifier(ProductClass::class, $ProductClass->getId());

        if ($Exists) {
            $Exists->setQuantity($Exists->getQuantity() + 1);
        } else {
            $CartItem = new CartItem();
            $CartItem
                ->setClassName(ProductClass::class)
                ->setClassId($productClassId)
                ->setObject($ProductClass)
                ->setPrice($ProductClass->getPrice02IncTax())
                ->setQuantity(1);
            $Cart->addItem($CartItem);
        }

        $app['eccube.purchase.flow.cart']->execute($Cart);

        // FRONT_CART_UP_COMPLETE
        $event = new EventArgs(
            array(
                'productClassId' => $productClassId,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_CART_UP_COMPLETE, $event);

        $errors = $Cart->getErrors();

        if (empty($errors)) {
            $app['eccube.service.cart']->save();
        } else {
            foreach($errors as $error) {
                $app->addRequestError($error);
            }
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

        // FRONT_CART_DOWN_INITIALIZE
        $event = new EventArgs(
            array(
                'productClassId' => $productClassId,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_CART_DOWN_INITIALIZE, $event);

        try {

            log_info('カート減算処理開始', array('product_class_id' => $productClassId));

            $productClassId = $event->getArgument('productClassId');
            $app['eccube.service.cart']->downProductQuantity($productClassId)->save();

            // FRONT_CART_UP_COMPLETE
            $event = new EventArgs(
                array(
                    'productClassId' => $productClassId,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_CART_DOWN_COMPLETE, $event);

            if ($event->hasResponse()) {
                return $event->getResponse();
            }

            log_info('カート減算処理完了', array('product_class_id' => $productClassId));

        } catch (CartException $e) {
            log_info('カート減算エラー', array($e->getMessage()));

            // FRONT_CART_DOWN_EXCEPTION
            $event = new EventArgs(
                array(
                    'exception' => $e,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_CART_DOWN_EXCEPTION, $event);

            if ($event->hasResponse()) {
                return $event->getResponse();
            }

            $app->addRequestError($e->getMessage());
        }

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

        $Cart = Cart::restore();
        $Cart->remove($productClassId);
        $Regi->execute($Cart);

        if ($Regi->hasError()) {
            $errors = $Regi->getErrors();
            foreach ($errors as $error) {
                $app->addRequestError($error);
            }
        }


        log_info('カート削除処理開始', array('product_class_id' => $productClassId));

        // FRONT_CART_REMOVE_INITIALIZE
        $event = new EventArgs(
            array(
                'productClassId' => $productClassId,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_CART_REMOVE_INITIALIZE, $event);

        $productClassId = $event->getArgument('productClassId');
        $app['eccube.service.cart']->removeProduct($productClassId)->save();

        log_info('カート削除処理完了', array('product_class_id' => $productClassId));

        // FRONT_CART_REMOVE_COMPLETE
        $event = new EventArgs(
            array(
                'productClassId' => $productClassId,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_CART_REMOVE_COMPLETE, $event);

        if ($event->hasResponse()) {
            return $event->getResponse();
        }

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
