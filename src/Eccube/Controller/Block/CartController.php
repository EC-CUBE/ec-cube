<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2018 LOCKON CO.,LTD. All Rights Reserved.
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CartController extends AbstractController
{
    /**
     * @var CartService
     */
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @Route("/block/cart", name="block_cart")
     * @Template("Block/cart.twig")
     */
    public function index()
    {
        $Carts = $this->cartService->getCarts();

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

        return [
            'totalQuantity' => $totalQuantity,
            'totalPrice' => $totalPrice,
            'Carts' => $Carts,
        ];
    }
}
