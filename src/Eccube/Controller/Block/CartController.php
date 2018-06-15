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

    public function __construct(
        CartService $cartService,
        PurchaseFlow $cartPurchaseFlow
    ) {
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
        // TODO ここで集計しないほうがよい？
        $this->execPurchaseFlow($Carts);

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

    protected function execPurchaseFlow($Carts)
    {
        /** @var PurchaseFlowResult[] $flowResults */
        $flowResults = array_map(function ($Cart) {
            $purchaseContext = new PurchaseContext($Cart, $this->getUser());

            return $this->purchaseFlow->calculate($Cart, $purchaseContext);
        }, $Carts);
    }
}
