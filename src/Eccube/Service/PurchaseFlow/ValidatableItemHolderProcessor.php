<?php

namespace Eccube\Service\PurchaseFlow;

use Eccube\Entity\ItemHolderInterface;

abstract class ValidatableItemHolderProcessor implements ItemHolderProcessor
{
    public final function process(ItemHolderInterface $itemHolder)
    {
        try {
            $this->validate($itemHolder);

            return ProcessResult::success();
        } catch (ItemValidateException $e) {
            return ProcessResult::error($e->getMessage());
        }
    }

    protected abstract function validate(ItemHolderInterface $itemHolder);

    protected function handle(ItemHolderInterface $itemHolder) {}
}
