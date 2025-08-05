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
    /** @var ProcessResult[] */
    private $processResults = [];

    /**
     * PurchaseFlowResult constructor.
     *
     * @param ItemHolderInterface $itemHolder
     */
    public function __construct(private readonly ItemHolderInterface $itemHolder)
    {
    }

    public function addProcessResult(ProcessResult $processResult)
    {
        $this->processResults[] = $processResult;
    }

    /**
     * @return array|ProcessResult[]
     */
    public function getErrors()
    {
        return array_filter($this->processResults, fn (ProcessResult $processResult) => $processResult->isError());
    }

    /**
     * @return array|ProcessResult[]
     */
    public function getWarning()
    {
        return array_filter($this->processResults, fn (ProcessResult $processResult) => $processResult->isWarning());
    }

    public function hasError()
    {
        return !empty($this->getErrors());
    }

    public function hasWarning()
    {
        return !empty($this->getWarning());
    }
}
