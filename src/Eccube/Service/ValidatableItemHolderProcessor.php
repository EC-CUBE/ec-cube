<?php

namespace Eccube\Service;

use Eccube\Entity\Cart;
use Eccube\Entity\ItemHolderInterface;

abstract class ValidatableItemHolderProcessor implements ItemHolderProcessor
{
    public final function process(ItemHolderInterface $itemHolder)
    {
        try {
            $this->validate($itemHolder);
        } catch (ItemValidateException $e) {
            if ($itemHolder instanceof Cart) {
                $this->handle($itemHolder);
            }
            throw $e;
        }
    }

    protected abstract function validate(ItemHolderInterface $item);

    protected function handle(ItemHolderInterface $item) {}
}
