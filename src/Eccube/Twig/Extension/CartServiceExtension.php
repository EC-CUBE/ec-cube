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
    public function __construct(protected CartService $cartService)
    {
    }

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_cart', $this->get_cart(...), ['is_safe' => ['all']]),
            new TwigFunction('get_all_carts', $this->get_all_carts(...), ['is_safe' => ['all']]),
            new TwigFunction('get_carts_total_price', $this->get_carts_total_price(...), ['is_safe' => ['all']]),
            new TwigFunction('get_carts_total_quantity', $this->get_carts_total_quantity(...), ['is_safe' => ['all']]),
        ];
    }

    public function get_cart(): ?Cart
    {
        return $this->cartService->getCart();
    }

    /**
     * @return Cart[]
     */
    public function get_all_carts(): array
    {
        return $this->cartService->getCarts();
    }

    public function get_carts_total_price(): string
    {
        $Carts = $this->cartService->getCarts();

        return array_reduce($Carts, fn (string $total, Cart $Cart) => bcadd($total, $Cart->getTotalPrice()), '0');
    }

    public function get_carts_total_quantity(): string
    {
        $Carts = $this->cartService->getCarts();

        return array_reduce($Carts, fn ($total, Cart $Cart) => bcadd($total, $Cart->getTotalQuantity()), '0');
    }
}
