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

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Service\CartService;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseException;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PreOrderIdValidator implements PurchaseProcessor
{
    /**
     * @var CartService
     */
    private $cartService;

    /**
     * PreOrderIdValidator constructor.
     *
     * @param CartService $cartService
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * 受注の仮確定処理を行います。
     *
     * @param ItemHolderInterface $target
     * @param PurchaseContext $context
     *
     * @throws PurchaseException
     */
    public function prepare(ItemHolderInterface $target, PurchaseContext $context)
    {
        // 処理なし
    }

    /**
     * 受注の確定処理を行います。
     *
     * @param ItemHolderInterface $target
     * @param PurchaseContext $context
     *
     * @throws PurchaseException
     */
    public function commit(ItemHolderInterface $target, PurchaseContext $context)
    {
        // 処理なし
    }

    /**
     * 仮確定した受注データの取り消し処理を行います。
     *
     * 別のorder_idが渡されてきた場合に処理が継続されないようにするため、
     * orderのpre_order_idがsessionのpre_order_idと一致するか確認する
     *
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     */
    public function rollback(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        // $itemHolderが受注の場合のみチェック
        if (!$itemHolder instanceof Order) {
            return;
        }

        $Cart = $this->cartService->getCart();

        // CartがなければBad Requestエラー
        if (!$Cart) {
            throw new BadRequestHttpException();
        }

        $cartPreOrderId = $this->cartService->getCart()->getPreOrderId();
        $orderPreOrderId = $itemHolder->getPreOrderId();

        // orderのpre_order_idがsessionのpre_order_idが一致していなければBad Requestエラー
        if ($cartPreOrderId != $orderPreOrderId) {
            throw new BadRequestHttpException();
        }
    }
}
