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
use Twig\Attribute\AsTwigFunction;

class CartServiceExtension
{
    public function __construct(protected CartService $cartService)
    {
    }

    #[AsTwigFunction(name: 'get_cart', isSafe: ['all'])]
    public function get_cart(): ?Cart
    {
        return $this->cartService->getCart();
    }

    /**
     * @return Cart[]
     */
    #[AsTwigFunction(name: 'get_all_carts', isSafe: ['all'])]
    public function get_all_carts(): array
    {
        return $this->cartService->getCarts();
    }

    #[AsTwigFunction(name: 'get_carts_total_price', isSafe: ['all'])]
    public function get_carts_total_price(): string
    {
        $Carts = $this->cartService->getCarts();

        return array_reduce($Carts, fn (string $total, Cart $Cart) => bcadd($total, $Cart->getTotalPrice()), '0');
    }

    #[AsTwigFunction(name: 'get_carts_total_quantity', isSafe: ['all'])]
    public function get_carts_total_quantity(): string
    {
        $Carts = $this->cartService->getCarts();

        return array_reduce($Carts, fn ($total, Cart $Cart) => bcadd($total, $Cart->getTotalQuantity()), '0');
    }
}
