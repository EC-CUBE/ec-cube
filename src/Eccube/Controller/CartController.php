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

use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Entity\Cart;
use Eccube\Entity\ProductClass;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\CartService;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(service=CartController::class)
 */
class CartController extends AbstractController
{
    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject(ProductClassRepository::class)
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * @Inject("eccube.purchase.flow.cart")
     * @var PurchaseFlow
     */
    protected $purchaseFlow;

    /**
     * @Inject(CartService::class)
     * @var CartService
     */
    protected $cartService;

    /**
     * カート画面.
     *
     * @Route("/cart", name="cart")
     * @Template("Cart/index.twig")
     */
    public function index(Application $app, Request $request)
    {
        // カートを取得して明細の正規化を実行
        $Carts = $this->cartService->getCarts();

        $this->execPurchaseFlow($app, $Carts);

        // TODO itemHolderから取得できるように
        $least = 0;
        $quantity = 0;
        $isDeliveryFree = false;

        $totalQuantity = array_reduce($Carts, function($total, $Cart) {
            /** @var Cart $Cart */
            $total += $Cart->getQuantity();
            return $total;
        }, 0);
        $totalPrice = array_reduce($Carts, function($total, $Cart) {
            /** @var Cart $Cart */
            $total += $Cart->getTotalPrice();
            return $total;
        }, 0);

        return [
            'totalPrice' => $totalPrice,
            'totalQuantity' => $totalQuantity,
            'Carts' => $Carts,
            'least' => $least,
            'quantity' => $quantity,
            'is_delivery_free' => $isDeliveryFree,
        ];
    }

    protected function execPurchaseFlow(Application $app, $Carts)
    {
        $flowResults = array_map(function($Cart) use ($app) {
            return $this->purchaseFlow->calculate($Cart, $app['eccube.purchase.context']());
        }, $Carts);


        // 復旧不可のエラーが発生した場合はカートをクリアして再描画
        $hasError = false;
        foreach ($flowResults as $result) {
            if ($result->hasError()) {
                $hasError = true;
                foreach ($result->getErrors() as $error) {
                    $app->addRequestError($error->getMessage());
                }
            }
        }
        if ($hasError) {
            $this->cartService->clear();
            $this->cartService->save();
            return $app->redirect($app->url('cart'));
        }

        $this->cartService->save();

        foreach ($flowResults as $index=>$result) {
            foreach ($result->getWarning() as $warning) {
                $app->addRequestError($warning->getMessage(), "front.cart.${index}");
            }
        }
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
     */
    public function handleCartItem(Application $app, Request $request, $operation, $productClassId)
    {
        log_info('カート明細操作開始', ['operation' => $operation, 'product_class_id' => $productClassId]);

        $this->isTokenValid($app);

        /** @var ProductClass $ProductClass */
        $ProductClass = $this->productClassRepository->find($productClassId);

        if (is_null($ProductClass)) {
            log_info('商品が存在しないため、カート画面へredirect', ['operation' => $operation, 'product_class_id' => $productClassId]);

            return $app->redirect($app->url('cart'));
        }

        // 明細の増減・削除
        switch ($operation) {
            case 'up':
                $this->cartService->addProduct($ProductClass, 1);
                break;
            case 'down':
                $this->cartService->addProduct($ProductClass, -1);
                break;
            case 'remove':
                $this->cartService->removeProduct($ProductClass);
                break;
        }

        // カートを取得して明細の正規化を実行
        $Carts = $this->cartService->getCarts();
        $this->execPurchaseFlow($app, $Carts);

        log_info('カート演算処理終了', ['operation' => $operation, 'product_class_id' => $productClassId]);

        return $app->redirect($app->url('cart'));
    }

    /**
     * カートをロック状態に設定し、購入確認画面へ遷移する.
     *
     * @Route("/cart/buystep/{index}", name="cart_buystep", requirements={"index" = "\d+"}, defaults={"index" = 0})
     */
    public function buystep(Application $app, Request $request, $index)
    {
        // FRONT_CART_BUYSTEP_INITIALIZE
        $event = new EventArgs(
            array(),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_CART_BUYSTEP_INITIALIZE, $event);

        $this->cartService->setPrimary($index);
        $this->cartService->lock();
        $this->cartService->save();

        // FRONT_CART_BUYSTEP_COMPLETE
        $event = new EventArgs(
            array(),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_CART_BUYSTEP_COMPLETE, $event);

        if ($event->hasResponse()) {
            return $event->getResponse();
        }

        return $app->redirect($app->url('shopping'));
    }
}
