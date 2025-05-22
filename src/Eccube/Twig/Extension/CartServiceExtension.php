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

namespace Eccube\Twig\Extension;

use Eccube\Entity\Cart;
use Eccube\Service\CartService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CartServiceExtension extends AbstractExtension
{
    /**
     * @var CartService
     */
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_cart', [$this, 'get_cart'], ['is_safe' => ['all']]),
            new TwigFunction('get_all_carts', [$this, 'get_all_carts'], ['is_safe' => ['all']]),
            new TwigFunction('get_carts_total_price', [$this, 'get_carts_total_price'], ['is_safe' => ['all']]),
            new TwigFunction('get_carts_total_quantity', [$this, 'get_carts_total_quantity'], ['is_safe' => ['all']]),
        ];
    }

    public function get_cart()
    {
        return $this->cartService->getCart();
    }

    public function get_all_carts()
    {
        return $this->cartService->getCarts();
    }

    public function get_carts_total_price()
    {
        $Carts = $this->cartService->getCarts();
        $totalPrice = array_reduce($Carts, function ($total, Cart $Cart) {
            $total += $Cart->getTotalPrice();

            return $total;
        }, 0);

        return $totalPrice;
    }

    public function get_carts_total_quantity()
    {
        $Carts = $this->cartService->getCarts();
        $totalQuantity = array_reduce($Carts, function ($total, Cart $Cart) {
            $total += $Cart->getTotalQuantity();

            return $total;
        }, 0);

        return $totalQuantity;
    }
}
