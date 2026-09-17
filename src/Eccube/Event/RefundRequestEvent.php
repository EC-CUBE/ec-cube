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

namespace Eccube\Event;

use Eccube\Entity\Master\RefundRequestStatus;
use Eccube\Entity\RefundRequest;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * 返品申請に関するイベント.
 *
 * ステータス変更時には変更前・変更後のステータスを保持する.
 */
class RefundRequestEvent extends Event
{
    public function __construct(
        private readonly RefundRequest $refundRequest,
        private readonly ?RefundRequestStatus $previousStatus = null,
        private readonly ?RefundRequestStatus $newStatus = null,
    ) {
    }

    public function getRefundRequest(): RefundRequest
    {
        return $this->refundRequest;
    }

    public function getPreviousStatus(): ?RefundRequestStatus
    {
        return $this->previousStatus;
    }

    public function getNewStatus(): ?RefundRequestStatus
    {
        return $this->newStatus;
    }
}
