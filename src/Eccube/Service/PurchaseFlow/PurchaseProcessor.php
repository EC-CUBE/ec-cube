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

namespace Eccube\Service\PurchaseFlow;

use Eccube\Entity\ItemHolderInterface;

/**
 * 受注の仮確定/確定/確定取り消し処理を行うインターフェイス。
 *
 * Interface PurchaseProcessor
 */
interface PurchaseProcessor
{
    /**
     * 受注の仮確定処理を行います。
     *
     * @param ItemHolderInterface $target
     * @param PurchaseContext $context
     *
     * @throws PurchaseException
     */
    public function prepare(ItemHolderInterface $target, PurchaseContext $context);

    /**
     * 受注の確定処理を行います。
     *
     * @param ItemHolderInterface $target
     * @param PurchaseContext     $context
     *
     * @throws PurchaseException
     */
    public function commit(ItemHolderInterface $target, PurchaseContext $context);

    /**
     * 仮確定した受注データの取り消し処理を行います。
     *
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext     $context
     */
    public function rollback(ItemHolderInterface $itemHolder, PurchaseContext $context);
}
