<?php

namespace Eccube\Service\PurchaseFlow;

use Eccube\Entity\Cart;
use Eccube\Entity\ItemHolderInterface;

abstract class ValidatableItemHolderProcessor implements ItemHolderProcessor
{
    public final function process(ItemHolderInterface $itemHolder)
    {
        try {
            $this->validate($itemHolder);

            return ProcessResult::success();
        } catch (ItemValidateException $e) {
            if ($itemHolder instanceof Cart) {
                $this->handle($itemHolder);
            }

            return ProcessResult::fail($e->getMessage());
        }
    }

    protected abstract function validate(ItemHolderInterface $itemHolder);

    protected function handle(ItemHolderInterface $itemHolder) {}
}
