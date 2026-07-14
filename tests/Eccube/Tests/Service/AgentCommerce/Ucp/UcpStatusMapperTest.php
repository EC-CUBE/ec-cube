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

namespace Eccube\Tests\Service\AgentCommerce\Ucp;

use Eccube\Entity\Master\CheckoutSessionStatus;
use Eccube\Service\AgentCommerce\Ucp\UcpStatusMapper;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1 (純ロジック) tests for UcpStatusMapper.
 *
 * 正規化ステータスマスタ -> UCP status 語彙の境界変換 (ready -> ready_for_complete) を検証する。
 */
final class UcpStatusMapperTest extends TestCase
{
    private UcpStatusMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new UcpStatusMapper();
    }

    private function makeStatus(int $id): CheckoutSessionStatus
    {
        $status = new CheckoutSessionStatus();
        $status->setId($id);

        return $status;
    }

    public function testReadyMapsToReadyForComplete(): void
    {
        $this->assertSame('ready_for_complete', $this->mapper->toUcpStatus($this->makeStatus(CheckoutSessionStatus::READY)), 'EC-CUBE の ready は UCP の ready_for_complete へ変換する');
    }

    public function testIncompleteMapsToIncomplete(): void
    {
        $this->assertSame('incomplete', $this->mapper->toUcpStatus($this->makeStatus(CheckoutSessionStatus::INCOMPLETE)));
    }

    public function testCompletedAndCanceled(): void
    {
        $this->assertSame('completed', $this->mapper->toUcpStatus($this->makeStatus(CheckoutSessionStatus::COMPLETED)));
        $this->assertSame('canceled', $this->mapper->toUcpStatus($this->makeStatus(CheckoutSessionStatus::CANCELED)));
    }

    public function testExpiredFallsBackToCanceled(): void
    {
        $this->assertSame('canceled', $this->mapper->toUcpStatus($this->makeStatus(CheckoutSessionStatus::EXPIRED)), 'UCP 語彙に無い expired は終端の canceled として表現する');
    }

    public function testNullStatusDefaultsToIncomplete(): void
    {
        $this->assertSame('incomplete', $this->mapper->toUcpStatus(null));
    }
}
