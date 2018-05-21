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

use Eccube\Controller\AbstractController;
use Eccube\Entity\Cart;
use Eccube\Service\CartService;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\PurchaseFlow\PurchaseFlowResult;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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

    /**
     * CartController constructor.
     * @param ProductClassRepository $productClassRepository
     * @param CartService $cartService
     * @param PurchaseFlow $cartPurchaseFlow
     */
    public function __construct(
        CartService $cartService,
        PurchaseFlow $cartPurchaseFlow
    )
    {
        $this->cartService = $cartService;
        $this->purchaseFlow = $cartPurchaseFlow;
    }

    /**
     * @Route("/block/cart", name="block_cart")
     * @Template("Block/cart.twig")
     */
    public function index()
    {
        $Carts = $this->cartService->getCarts();
        $flowResults = array_map(function($Cart) {
            $purchaseContext = new PurchaseContext($Cart, $this->getUser());

            return $this->purchaseFlow->calculate($Cart, $purchaseContext);
        }, $Carts);


        $totalQuantity = array_reduce($Carts, function ($total, $Cart) {
            /** @var Cart $Cart */
            $total += $Cart->getQuantity();

            return $total;
        }, 0);
        $totalPrice = array_reduce($Carts, function ($total, $Cart) {
            /** @var Cart $Cart */
            $total += $Cart->getTotal();
            dump($total);
            return $total;
        }, 0);

        return [
            'totalQuantity' => $totalQuantity,
            'totalPrice' => $totalPrice,
            'Carts' => $Carts,
        ];
    }
}
