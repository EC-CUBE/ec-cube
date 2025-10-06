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

use Eccube\Entity\ItemHolderInterface;

class PurchaseFlowResult
{
    /** @var ItemHolderInterface */
    private $itemHolder;

    /** @var ProcessResult[] */
    private $processResults = [];

    /**
     * PurchaseFlowResult constructor.
     *
     * @param ItemHolderInterface $itemHolder
     */
    public function __construct(ItemHolderInterface $itemHolder)
    {
        $this->itemHolder = $itemHolder;
    }

    /**
     * @param ProcessResult $processResult
     *
     * @return void
     */
    public function addProcessResult(ProcessResult $processResult): void
    {
        $this->processResults[] = $processResult;
    }

    /**
     * @return array|ProcessResult[]
     */
    public function getErrors(): array
    {
        return array_filter($this->processResults, function (ProcessResult $processResult) {
            return $processResult->isError();
        });
    }

    /**
     * @return array|ProcessResult[]
     */
    public function getWarning(): array
    {
        return array_filter($this->processResults, function (ProcessResult $processResult) {
            return $processResult->isWarning();
        });
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return !empty($this->getErrors());
    }

    /**
     * @return bool
     */
    public function hasWarning(): bool
    {
        return !empty($this->getWarning());
    }

    /**
     * @return ItemHolderInterface
     */
    public function getItemHolder(): ItemHolderInterface
    {
        return $this->itemHolder;
    }
}
