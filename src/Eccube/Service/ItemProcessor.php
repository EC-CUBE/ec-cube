<?php

namespace Eccube\Service;


use Eccube\Entity\ItemInterface;

interface ItemProcessor
{
    /**
     * @param ItemInterface $item
     * @return ProcessResult
     */
    public function process(ItemInterface $item);
}