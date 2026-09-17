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

namespace Eccube\Tests\Service;

use Eccube\Entity\Master\RefundRequestStatus;
use Eccube\Entity\RefundRequest;
use Eccube\Service\RefundRequestStateMachine;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class RefundRequestStateMachineTest extends EccubeTestCase
{
    private ?RefundRequestStateMachine $stateMachine = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stateMachine = static::getContainer()->get(RefundRequestStateMachine::class);
    }

    #[DataProvider(methodName: 'canProvider')]
    public function testCan(string $transition, int $fromId, bool $expected): void
    {
        $RefundRequest = $this->createRefundRequestWithStatus($fromId);
        $this->assertSame($expected, $this->stateMachine->can($RefundRequest, $transition));
    }

    public static function canProvider(): \Iterator
    {
        // start_processing: NEW → PROCESSING
        yield ['start_processing', RefundRequestStatus::NEW,            true];
        yield ['start_processing', RefundRequestStatus::PROCESSING,     false];
        yield ['start_processing', RefundRequestStatus::ACCEPTED,       false];
        yield ['start_processing', RefundRequestStatus::DECLINED,       false];
        yield ['start_processing', RefundRequestStatus::INFO_REQUESTED, false];

        // accept: PROCESSING → ACCEPTED
        yield ['accept', RefundRequestStatus::NEW,            false];
        yield ['accept', RefundRequestStatus::PROCESSING,     true];
        yield ['accept', RefundRequestStatus::ACCEPTED,       false];
        yield ['accept', RefundRequestStatus::DECLINED,       false];
        yield ['accept', RefundRequestStatus::INFO_REQUESTED, false];

        // decline: PROCESSING → DECLINED
        yield ['decline', RefundRequestStatus::NEW,            false];
        yield ['decline', RefundRequestStatus::PROCESSING,     true];
        yield ['decline', RefundRequestStatus::ACCEPTED,       false];
        yield ['decline', RefundRequestStatus::DECLINED,       false];
        yield ['decline', RefundRequestStatus::INFO_REQUESTED, false];

        // request_info: PROCESSING → INFO_REQUESTED
        yield ['request_info', RefundRequestStatus::NEW,            false];
        yield ['request_info', RefundRequestStatus::PROCESSING,     true];
        yield ['request_info', RefundRequestStatus::ACCEPTED,       false];
        yield ['request_info', RefundRequestStatus::DECLINED,       false];
        yield ['request_info', RefundRequestStatus::INFO_REQUESTED, false];

        // resume_processing: INFO_REQUESTED → PROCESSING
        yield ['resume_processing', RefundRequestStatus::NEW,            false];
        yield ['resume_processing', RefundRequestStatus::PROCESSING,     false];
        yield ['resume_processing', RefundRequestStatus::ACCEPTED,       false];
        yield ['resume_processing', RefundRequestStatus::DECLINED,       false];
        yield ['resume_processing', RefundRequestStatus::INFO_REQUESTED, true];
    }

    public function testApplyStartProcessing(): void
    {
        $RefundRequest = $this->createRefundRequestWithStatus(RefundRequestStatus::NEW);
        $this->stateMachine->applyTransition($RefundRequest, 'start_processing');
        $this->assertSame(RefundRequestStatus::PROCESSING, $RefundRequest->getRefundRequestStatus()->getId());
    }

    public function testApplyAccept(): void
    {
        $RefundRequest = $this->createRefundRequestWithStatus(RefundRequestStatus::PROCESSING);
        $this->stateMachine->applyTransition($RefundRequest, 'accept');
        $this->assertSame(RefundRequestStatus::ACCEPTED, $RefundRequest->getRefundRequestStatus()->getId());
    }

    public function testApplyDecline(): void
    {
        $RefundRequest = $this->createRefundRequestWithStatus(RefundRequestStatus::PROCESSING);
        $this->stateMachine->applyTransition($RefundRequest, 'decline');
        $this->assertSame(RefundRequestStatus::DECLINED, $RefundRequest->getRefundRequestStatus()->getId());
    }

    public function testApplyRequestInfo(): void
    {
        $RefundRequest = $this->createRefundRequestWithStatus(RefundRequestStatus::PROCESSING);
        $this->stateMachine->applyTransition($RefundRequest, 'request_info');
        $this->assertSame(RefundRequestStatus::INFO_REQUESTED, $RefundRequest->getRefundRequestStatus()->getId());
    }

    public function testApplyResumeProcessing(): void
    {
        $RefundRequest = $this->createRefundRequestWithStatus(RefundRequestStatus::INFO_REQUESTED);
        $this->stateMachine->applyTransition($RefundRequest, 'resume_processing');
        $this->assertSame(RefundRequestStatus::PROCESSING, $RefundRequest->getRefundRequestStatus()->getId());
    }

    public function testApplyInvalidTransition(): void
    {
        $RefundRequest = $this->createRefundRequestWithStatus(RefundRequestStatus::NEW);
        $this->expectException(\InvalidArgumentException::class);
        $this->stateMachine->applyTransition($RefundRequest, 'accept');
    }

    public function testGetAvailableTransitionsFromNew(): void
    {
        $RefundRequest = $this->createRefundRequestWithStatus(RefundRequestStatus::NEW);
        $transitions = $this->stateMachine->getAvailableTransitions($RefundRequest);

        $this->assertArrayHasKey('start_processing', $transitions);
        $this->assertCount(1, $transitions);
        $this->assertSame(RefundRequestStatus::PROCESSING, $transitions['start_processing']->getId());
    }

    public function testGetAvailableTransitionsFromProcessing(): void
    {
        $RefundRequest = $this->createRefundRequestWithStatus(RefundRequestStatus::PROCESSING);
        $transitions = $this->stateMachine->getAvailableTransitions($RefundRequest);

        $this->assertCount(3, $transitions);
        $this->assertArrayHasKey('accept', $transitions);
        $this->assertArrayHasKey('decline', $transitions);
        $this->assertArrayHasKey('request_info', $transitions);
    }

    public function testGetAvailableTransitionsFromAccepted(): void
    {
        $RefundRequest = $this->createRefundRequestWithStatus(RefundRequestStatus::ACCEPTED);
        $transitions = $this->stateMachine->getAvailableTransitions($RefundRequest);

        $this->assertCount(0, $transitions);
    }

    public function testGetAvailableTransitionsFromDeclined(): void
    {
        $RefundRequest = $this->createRefundRequestWithStatus(RefundRequestStatus::DECLINED);
        $transitions = $this->stateMachine->getAvailableTransitions($RefundRequest);

        $this->assertCount(0, $transitions);
    }

    public function testGetAvailableTransitionsFromInfoRequested(): void
    {
        $RefundRequest = $this->createRefundRequestWithStatus(RefundRequestStatus::INFO_REQUESTED);
        $transitions = $this->stateMachine->getAvailableTransitions($RefundRequest);

        $this->assertArrayHasKey('resume_processing', $transitions);
        $this->assertCount(1, $transitions);
    }

    public function testCanWithNullStatus(): void
    {
        $RefundRequest = new RefundRequest();
        $this->assertFalse($this->stateMachine->can($RefundRequest, 'start_processing'));
    }

    private function createRefundRequestWithStatus(int $statusId): RefundRequest
    {
        $Status = $this->entityManager->find(RefundRequestStatus::class, $statusId);
        $RefundRequest = new RefundRequest();
        $RefundRequest->setRefundRequestStatus($Status);

        return $RefundRequest;
    }
}
