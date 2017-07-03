<?php

namespace Eccube\Service;


use Eccube\Entity\ItemHolderInterface;

interface ItemHolderProcessor
{
    public function process(ItemHolderInterface $itemHolder);
}