<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Service\AgentCommerce\Acp;

use Eccube\Entity\Master\CheckoutSessionStatus;
use Eccube\Service\AgentCommerce\Acp\AcpStatusMapper;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1: EC-CUBE 正規化ステータス → ACP status 語彙のマッピング.
 */
final class AcpStatusMapperTest extends TestCase
{
    private AcpStatusMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new AcpStatusMapper();
    }

    private function statusMaster(int $id): CheckoutSessionStatus
    {
        return (new CheckoutSessionStatus())->setId($id);
    }

    public function testReadyMapsToReadyForPayment(): void
    {
        $this->assertSame('ready_for_payment', $this->mapper->toAcpStatus($this->statusMaster(CheckoutSessionStatus::READY)));
    }

    public function testCompletedCanceledExpired(): void
    {
        $this->assertSame('completed', $this->mapper->toAcpStatus($this->statusMaster(CheckoutSessionStatus::COMPLETED)));
        $this->assertSame('canceled', $this->mapper->toAcpStatus($this->statusMaster(CheckoutSessionStatus::CANCELED)));
        $this->assertSame('expired', $this->mapper->toAcpStatus($this->statusMaster(CheckoutSessionStatus::EXPIRED)));
    }

    public function testInProgressMapsToCompleteInProgress(): void
    {
        $this->assertSame('complete_in_progress', $this->mapper->toAcpStatus($this->statusMaster(CheckoutSessionStatus::IN_PROGRESS)));
    }

    public function testRequiresActionWith3dsMapsToAuthenticationRequired(): void
    {
        $this->assertSame(
            'authentication_required',
            $this->mapper->toAcpStatus($this->statusMaster(CheckoutSessionStatus::REQUIRES_ACTION), ['intervention' => '3ds']),
            '3DS の追加対応待ちは authentication_required'
        );
    }

    public function testRequiresActionWithoutAuthMapsToEscalation(): void
    {
        $this->assertSame(
            'requires_escalation',
            $this->mapper->toAcpStatus($this->statusMaster(CheckoutSessionStatus::REQUIRES_ACTION), ['continue_url' => 'https://example.com/handoff']),
            '3DS でない外部ハンドオフは requires_escalation'
        );
    }

    public function testIncompleteWithBlockingErrorMapsToNotReadyForPayment(): void
    {
        $this->assertSame(
            'not_ready_for_payment',
            $this->mapper->toAcpStatus($this->statusMaster(CheckoutSessionStatus::INCOMPLETE), [], true),
            'ブロッキングエラーがある INCOMPLETE は not_ready_for_payment'
        );
    }

    public function testIncompleteWithoutErrorMapsToIncomplete(): void
    {
        $this->assertSame('incomplete', $this->mapper->toAcpStatus($this->statusMaster(CheckoutSessionStatus::INCOMPLETE)));
        $this->assertSame('incomplete', $this->mapper->toAcpStatus(null));
    }
}
