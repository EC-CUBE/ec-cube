<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Block;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Cart;
use Eccube\Service\CartService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    /**
     * @var CartService
     */
    protected $cartService;

    public function __construct(
        CartService $cartService,
    ) {
        $this->cartService = $cartService;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[Route(path: '/block/cart', name: 'block_cart', methods: ['GET'])]
    #[Route(path: '/block/cart_sp', name: 'block_cart_sp', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $Carts = $this->cartService->getCarts();

        // 二重に実行され, 注文画面でのエラーハンドリングができないので
        // ここではpurchaseFlowは実行しない

        $totalQuantity = array_reduce($Carts, function ($total, $Cart) {
            /** @var Cart $Cart */
            $total = bcadd($total, $Cart->getTotalQuantity());

            return $total;
        }, '0');
        $totalPrice = array_reduce($Carts, function ($total, $Cart) {
            /** @var Cart $Cart */
            $total = bcadd($total, $Cart->getTotalPrice());

            return $total;
        }, '0');

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
}
