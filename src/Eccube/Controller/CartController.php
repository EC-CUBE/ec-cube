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
use Eccube\Service\PurchaseFlow\Processor\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlowResult;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    /**
     * 商品追加用コントローラ(デバッグ用)
     *
     * @Route("/cart/test")
     * @param Application $app
     */
    public function addTestProduct(Application $app)
    {
        $app['eccube.service.cart']->addProduct(10, 2);
        $app['eccube.service.cart']->save();

        return $app->redirect($app->url('cart'));
    }

    /**
     * カート画面.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        // カートを取得して明細の正規化を実行
        $Cart = $app['eccube.service.cart']->getCart();
        /** @var PurchaseFlowResult $result */
        $result = $app['eccube.purchase.flow.cart']->calculate($Cart, PurchaseContext::create($app));

        // 復旧不可のエラーが発生した場合はカートをクリアして再描画
        if ($result->hasError()) {
            foreach ($result->getErrors() as $error) {
                $app->addRequestError($error->getMessage());
            }
            $app['eccube.service.cart']->clear();
            $app['eccube.service.cart']->save();

            return $app->redirect($app->url('cart'));
        }

        $app['eccube.service.cart']->save();

        foreach ($result->getWarning() as $warning) {
            $app->addRequestError($warning->getMessage());
        }

        // TODO itemHolderから取得できるように
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
     * カート明細の加算/減算/削除を行う.
     *
     * - 加算
     *      - 明細の個数を1増やす
     * - 減算
     *      - 明細の個数を1減らす
     *      - 個数が0になる場合は、明細を削除する
     * - 削除
     *      - 明細を削除する
     *
     * @Method("PUT")
     * @Route(
     *     path="/cart/{operation}/{productClassId}",
     *     name="cart_handle_item",
     *     requirements={
     *          "operation": "up|down|remove",
     *          "productClassId": "\d+"
     *     }
     * )
     * @param Application $app
     * @param Request $request
     * @param $operation
     * @param $productClassId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handleCartItem(Application $app, Request $request, $operation, $productClassId)
    {
        log_info('カート明細操作開始', ['operation' => $operation, 'product_class_id' => $productClassId]);

        $this->isTokenValid($app);

        /** @var ProductClass $ProductClass */
        $ProductClass = $app['eccube.repository.product_class']->find($productClassId);

        if (is_null($ProductClass)) {
            log_info('商品が存在しないため、カート画面へredirect', ['operation' => $operation, 'product_class_id' => $productClassId]);

            return $app->redirect($app->url('cart'));
        }

        // 明細の増減・削除
        switch ($operation) {
            case 'up':
                $app['eccube.service.cart']->addProduct($ProductClass, 1);
                break;
            case 'down':
                $app['eccube.service.cart']->addProduct($ProductClass, -1);
                break;
            case 'remove':
                $app['eccube.service.cart']->removeProduct($ProductClass);
                break;
        }

        // カートを取得して明細の正規化を実行
        $Cart = $app['eccube.service.cart']->getCart();
        /** @var PurchaseFlowResult $result */

        $result = $app['eccube.purchase.flow.cart']->calculate($Cart, PurchaseContext::create($app));

        // 復旧不可のエラーが発生した場合はカートをクリアしてカート一覧へ
        if ($result->hasError()) {
            foreach ($result->getErrors() as $error) {
                $app->addRequestError($error->getMessage());
            }
            $app['eccube.service.cart']->clear();
            $app['eccube.service.cart']->save();

            return $app->redirect($app->url('cart'));
        }

        $app['eccube.service.cart']->save();

        foreach ($result->getWarning() as $warning) {
            $app->addRequestError($warning->getMessage());
        }

        log_info('カート演算処理終了', ['operation' => $operation, 'product_class_id' => $productClassId]);

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
