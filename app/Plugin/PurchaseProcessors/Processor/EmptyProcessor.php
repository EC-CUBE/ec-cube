<?php

namespace Plugin\PurchaseProcessors\Processor;

use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\ItemProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;

class EmptyProcessor implements ItemProcessor
{
    /**
     * @param ItemInterface $item
     * @return ProcessResult
     */
    public function process(ItemInterface $item)
    {
        log_info('empty processor executed', [__METHOD__]);
        return ProcessResult::success();
    }
}
