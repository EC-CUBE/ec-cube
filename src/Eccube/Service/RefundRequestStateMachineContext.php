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

namespace Eccube\Service;

use Eccube\Entity\RefundRequest;

/**
 * Symfony Workflow の marking_store として使うコンテキスト.
 *
 * RefundRequest の現在ステータスを文字列マーキングとして保持し、
 * ステートマシンの遷移対象となる。
 */
class RefundRequestStateMachineContext
{
    public function __construct(private string $status, private readonly RefundRequest $RefundRequest)
    {
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getRefundRequest(): RefundRequest
    {
        return $this->RefundRequest;
    }

    /**
     * Alias of getStatus()
     */
    public function getMarking(): string
    {
        return $this->getStatus();
    }

    /**
     * Alias of setStatus()
     */
    public function setMarking(string $status): void
    {
        $this->setStatus($status);
    }
}
