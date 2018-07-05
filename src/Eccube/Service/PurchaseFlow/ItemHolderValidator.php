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

namespace Eccube\Service\PurchaseFlow;

use Eccube\Entity\ItemHolderInterface;

/**
 * カート/受注の妥当性検証を行う.
 */
abstract class ItemHolderValidator
{
    use ValidatorTrait;

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext     $context
     *
     * @return ProcessResult
     */
    final public function execute(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        try {
            $this->validate($itemHolder, $context);

            return ProcessResult::success();
        } catch (InvalidItemException $e) {
            return $e->isWarning() ? ProcessResult::warn($e->getMessage()) : ProcessResult::error($e->getMessage());
        }
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @throws InvalidItemException
     */
    abstract protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context);

    protected function handle(ItemHolderInterface $itemHolder)
    {
    }
}
