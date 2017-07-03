<?php

namespace Eccube\Service;


use Eccube\Entity\ItemInterface;

interface ItemProcessor
{
    public function process(ItemInterface $item);
}