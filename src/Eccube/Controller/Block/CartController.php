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


namespace Eccube\Controller\Block;

use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Entity\Cart;
use Eccube\Service\CartService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(service=CartController::class)
 */
class CartController
{
    /**
     * @Inject(CartService::class)
     * @var CartService
     */
    protected $cartService;

    /**
     * @Route("/block/cart", name="block_cart")
     * @Template("Block/cart.twig")
     */
    public function index(Application $app, Request $request)
    {
        $Carts = $this->cartService->getCarts();

        $totalQuantity = array_reduce($Carts, function($total, $Cart) {
            /** @var Cart $Cart */
            $total += $Cart->getTotalQuantity();
            return $total;
        }, 0);
        $totalPrice = array_reduce($Carts, function($total, $Cart) {
            /** @var Cart $Cart */
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
