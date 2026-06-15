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

use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\RefundRequestStatus;
use Eccube\Entity\Order;
use Eccube\Entity\RefundRequest;
use Eccube\Service\RefundRequestService;
use Eccube\Tests\EccubeTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;

final class RefundRequestServiceTest extends EccubeTestCase
{
    use MailerAssertionsTrait;

    private ?RefundRequestService $refundRequestService = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->refundRequestService = static::getContainer()->get(RefundRequestService::class);
    }

    public function testCreateRefundRequest(): void
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $this->setOrderStatus($Order, OrderStatus::DELIVERED);
        $this->entityManager->flush();

        $OrderItem = $Order->getProductOrderItems()[0];

        $RefundRequest = new RefundRequest();
        $RefundRequest->setOrder($Order);
        $RefundRequest->setOrderItem($OrderItem);
        $RefundRequest->setCustomer($Customer);
        $RefundRequest->setQuantity('1');
        $RefundRequest->setReason('商品に破損がありました');

        $result = $this->refundRequestService->createRefundRequest($RefundRequest);

        $this->assertNotNull($result->getId());
        $this->assertSame(RefundRequestStatus::NEW, $result->getRefundRequestStatus()->getId());
        $this->assertSame('1', $result->getQuantity());
        $this->assertSame('商品に破損がありました', $result->getReason());
        $this->assertEmailCount(1);
    }

    public function testCreateRefundRequestWithoutFiles(): void
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $this->setOrderStatus($Order, OrderStatus::DELIVERED);
        $this->entityManager->flush();

        $OrderItem = $Order->getProductOrderItems()[0];

        $RefundRequest = new RefundRequest();
        $RefundRequest->setOrder($Order);
        $RefundRequest->setOrderItem($OrderItem);
        $RefundRequest->setCustomer($Customer);
        $RefundRequest->setQuantity('2');
        $RefundRequest->setReason('サイズが合いませんでした');

        $result = $this->refundRequestService->createRefundRequest($RefundRequest, []);

        $this->assertNotNull($result->getId());
        $this->assertCount(0, $result->getRefundRequestFiles());
    }

    public function testChangeStatus(): void
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $this->setOrderStatus($Order, OrderStatus::DELIVERED);
        $this->entityManager->flush();

        $OrderItem = $Order->getProductOrderItems()[0];

        $RefundRequest = new RefundRequest();
        $RefundRequest->setOrder($Order);
        $RefundRequest->setOrderItem($OrderItem);
        $RefundRequest->setCustomer($Customer);
        $RefundRequest->setQuantity('1');
        $RefundRequest->setReason('テスト理由');

        $this->refundRequestService->createRefundRequest($RefundRequest);

        $this->refundRequestService->changeStatus($RefundRequest, 'start_processing');
        $this->assertSame(RefundRequestStatus::PROCESSING, $RefundRequest->getRefundRequestStatus()->getId());

        $this->refundRequestService->changeStatus($RefundRequest, 'accept');
        $this->assertSame(RefundRequestStatus::ACCEPTED, $RefundRequest->getRefundRequestStatus()->getId());
    }

    public function testChangeStatusInvalid(): void
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $this->setOrderStatus($Order, OrderStatus::DELIVERED);
        $this->entityManager->flush();

        $OrderItem = $Order->getProductOrderItems()[0];

        $RefundRequest = new RefundRequest();
        $RefundRequest->setOrder($Order);
        $RefundRequest->setOrderItem($OrderItem);
        $RefundRequest->setCustomer($Customer);
        $RefundRequest->setQuantity('1');
        $RefundRequest->setReason('テスト理由');

        $this->refundRequestService->createRefundRequest($RefundRequest);

        $this->expectException(\InvalidArgumentException::class);
        $this->refundRequestService->changeStatus($RefundRequest, 'accept');
    }

    public function testCanApplyTransition(): void
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $this->setOrderStatus($Order, OrderStatus::DELIVERED);
        $this->entityManager->flush();

        $OrderItem = $Order->getProductOrderItems()[0];

        $RefundRequest = new RefundRequest();
        $RefundRequest->setOrder($Order);
        $RefundRequest->setOrderItem($OrderItem);
        $RefundRequest->setCustomer($Customer);
        $RefundRequest->setQuantity('1');
        $RefundRequest->setReason('テスト理由');

        $this->refundRequestService->createRefundRequest($RefundRequest);

        $this->assertTrue($this->refundRequestService->canApplyTransition($RefundRequest, 'start_processing'));
        $this->assertFalse($this->refundRequestService->canApplyTransition($RefundRequest, 'accept'));
    }

    public function testGetAvailableTransitions(): void
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $this->setOrderStatus($Order, OrderStatus::DELIVERED);
        $this->entityManager->flush();

        $OrderItem = $Order->getProductOrderItems()[0];

        $RefundRequest = new RefundRequest();
        $RefundRequest->setOrder($Order);
        $RefundRequest->setOrderItem($OrderItem);
        $RefundRequest->setCustomer($Customer);
        $RefundRequest->setQuantity('1');
        $RefundRequest->setReason('テスト理由');

        $this->refundRequestService->createRefundRequest($RefundRequest);

        $transitions = $this->refundRequestService->getAvailableTransitions($RefundRequest);
        $this->assertArrayHasKey('start_processing', $transitions);
        $this->assertCount(1, $transitions);
    }

    private function setOrderStatus(Order $Order, int $statusId): void
    {
        $Status = $this->entityManager->find(OrderStatus::class, $statusId);
        $Order->setOrderStatus($Status);
    }
}
