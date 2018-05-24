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

use Eccube\Entity\CartItem;
use Eccube\Entity\ItemInterface;

abstract class ValidatableItemProcessor implements ItemProcessor
{
    use ValidatorTrait;

    /**
     * @param ItemInterface   $item
     * @param PurchaseContext $context
     *
     * @return ProcessResult
     */
    public function process(ItemInterface $item, PurchaseContext $context)
    {
        try {
            $this->validate($item, $context);

            return ProcessResult::success();
        } catch (InvalidItemException $e) {
            if ($item instanceof CartItem) {
                $this->handle($item, $context);
            }

            return ProcessResult::warn($e->getMessage());
        }
    }

    abstract protected function validate(ItemInterface $item, PurchaseContext $context);

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
    }
}
