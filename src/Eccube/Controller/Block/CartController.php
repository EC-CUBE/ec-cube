<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Block;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Cart;
use Eccube\Service\CartService;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\PurchaseFlow\PurchaseFlowResult;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * @var PurchaseFlow
     */
    protected $purchaseFlow;

    public function __construct(
        CartService $cartService,
        PurchaseFlow $cartPurchaseFlow
    ) {
        $this->cartService = $cartService;
        $this->purchaseFlow = $cartPurchaseFlow;
    }

    /**
     * @Route("/block/cart", name="block_cart")
     * @Route("/block/cart_sp", name="block_cart_sp")
     */
    public function index(Request $request)
    {
        $Carts = $this->cartService->getCarts();

        // 二重に実行され, 注文画面でのエラーハンドリングができないので
        // ここではpurchaseFlowは実行しない

        $totalQuantity = array_reduce($Carts, function ($total, $Cart) {
            /* @var Cart $Cart */
            $total += $Cart->getTotalQuantity();

            return $total;
        }, 0);
        $totalPrice = array_reduce($Carts, function ($total, $Cart) {
            /* @var Cart $Cart */
            $total += $Cart->getTotalPrice();

            return $total;
        }, 0);

        $route = $request->attributes->get('_route');

        if ($route == 'block_cart_sp') {
            return $this->render('Block/nav_sp.twig', [
                'totalQuantity' => $totalQuantity,
                'totalPrice' => $totalPrice,
                'Carts' => $Carts,
            ]);
        } else {
            return $this->render('Block/cart.twig', [
                'totalQuantity' => $totalQuantity,
                'totalPrice' => $totalPrice,
                'Carts' => $Carts,
            ]);
        }
    }

    protected function execPurchaseFlow($Carts)
    {
        /** @var PurchaseFlowResult[] $flowResults */
        $flowResults = array_map(function ($Cart) {
            $purchaseContext = new PurchaseContext($Cart, $this->getUser());

            return $this->purchaseFlow->validate($Cart, $purchaseContext);
        }, $Carts);
    }
}
