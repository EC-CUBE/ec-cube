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
use Eccube\Service\PurchaseFlow\PurchaseProcessor;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PreOrderIdValidator implements PurchaseProcessor
{
    /**
     * PreOrderIdValidator constructor.
     */
    public function __construct(private readonly CartService $cartService)
    {
    }

    /**
     * 受注の仮確定処理を行います。
     */
    #[\Override]
    public function prepare(ItemHolderInterface $target, PurchaseContext $context): void
    {
        // 処理なし
    }

    /**
     * 受注の確定処理を行います。
     */
    #[\Override]
    public function commit(ItemHolderInterface $target, PurchaseContext $context): void
    {
        // 処理なし
    }

    /**
     * 仮確定した受注データの取り消し処理を行います。
     *
     * 別のorder_idが渡されてきた場合に処理が継続されないようにするため、
     * orderのpre_order_idがsessionのpre_order_idと一致するか確認する
     *
     * @param ItemHolderInterface $itemHolder 受注 or カート
     * @param PurchaseContext $context 購入フローのコンテキスト
     *
     * @throws BadRequestHttpException pre_order_idが一致しない場合 OR Cartがない場合 OR $itemHolderが受注でない場合
     */
    #[\Override]
    public function rollback(ItemHolderInterface $itemHolder, PurchaseContext $context): void
    {
        // $itemHolderが受注の場合のみチェック
        if (!$itemHolder instanceof Order) {
            return;
        }

        // エージェントコマース (ACP/UCP) の受注はセッションを持たない。操作中カートと受注の
        // 紐付け・越境防止は CheckoutSession (推測不能な session_id + protocol 照合 +
        // cart/order の pre_order_id 整合) が controller 層で担保するため、session 依存の
        // 本チェックは適用外とする。
        if ($itemHolder->getAgentProtocol() !== null) {
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
