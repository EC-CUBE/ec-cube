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

use Eccube\Entity\ItemInterface;

/**
 * 明細単位の妥当性検証.
 */
abstract class ItemValidator
{
    use ValidatorTrait;

    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     *
     * @return ProcessResult
     */
    final public function execute(ItemInterface $item, PurchaseContext $context)
    {
        try {
            $this->validate($item, $context);

            return ProcessResult::success(null, static::class);
        } catch (InvalidItemException $e) {
            $this->handle($item, $context);

            return ProcessResult::warn($e->getMessage(), static::class);
        }
    }

    /**
     * 妥当性検証を行う.
     *
     * @param ItemInterface $item
     * @param PurchaseContext $context
     */
    abstract protected function validate(ItemInterface $item, PurchaseContext $context);

    /**
     * 検証エラー時に後処理を行う.
     *
     * @param ItemInterface $item
     * @param PurchaseContext $context
     */
    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
    }
}
