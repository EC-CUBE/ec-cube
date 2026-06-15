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

namespace Eccube\Tests\Repository;

use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\RefundRequestStatus;
use Eccube\Entity\Order;
use Eccube\Entity\RefundRequest;
use Eccube\Repository\RefundRequestRepository;
use Eccube\Tests\EccubeTestCase;

final class RefundRequestRepositoryTest extends EccubeTestCase
{
    private ?RefundRequestRepository $refundRequestRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->refundRequestRepository = $this->entityManager->getRepository(RefundRequest::class);
    }

    public function testGetQueryBuilderBySearchDataEmpty(): void
    {
        $this->createTestRefundRequest();

        $qb = $this->refundRequestRepository->getQueryBuilderBySearchData([]);
        $result = $qb->getQuery()->getResult();

        $this->assertNotEmpty($result);
    }

    public function testGetQueryBuilderBySearchDataMultiById(): void
    {
        $RefundRequest = $this->createTestRefundRequest();

        $qb = $this->refundRequestRepository->getQueryBuilderBySearchData([
            'multi' => (string) $RefundRequest->getId(),
        ]);
        $result = $qb->getQuery()->getResult();

        $this->assertCount(1, $result);
        $this->assertSame($RefundRequest->getId(), $result[0]->getId());
    }

    public function testGetQueryBuilderBySearchDataMultiByOrderNo(): void
    {
        $RefundRequest = $this->createTestRefundRequest();
        $orderNo = $RefundRequest->getOrder()->getOrderNo();

        $qb = $this->refundRequestRepository->getQueryBuilderBySearchData([
            'multi' => $orderNo,
        ]);
        $result = $qb->getQuery()->getResult();

        $this->assertNotEmpty($result);
    }

    public function testGetQueryBuilderBySearchDataByStatus(): void
    {
        $this->createTestRefundRequest();

        $Status = $this->entityManager->find(RefundRequestStatus::class, RefundRequestStatus::NEW);
        $qb = $this->refundRequestRepository->getQueryBuilderBySearchData([
            'status' => [$Status],
        ]);
        $result = $qb->getQuery()->getResult();

        $this->assertNotEmpty($result);
        foreach ($result as $rr) {
            $this->assertSame(RefundRequestStatus::NEW, $rr->getRefundRequestStatus()->getId());
        }
    }

    public function testGetQueryBuilderBySearchDataByCreateDate(): void
    {
        $this->createTestRefundRequest();

        $yesterday = new \DateTime('-1 day');
        $tomorrow = new \DateTime('+1 day');

        $qb = $this->refundRequestRepository->getQueryBuilderBySearchData([
            'create_date_start' => $yesterday,
            'create_date_end' => $tomorrow,
        ]);
        $result = $qb->getQuery()->getResult();

        $this->assertNotEmpty($result);
    }

    public function testFindByOrderItemAndCustomer(): void
    {
        $RefundRequest = $this->createTestRefundRequest();
        $OrderItem = $RefundRequest->getOrderItem();
        $Customer = $RefundRequest->getCustomer();

        $result = $this->refundRequestRepository->findByOrderItemAndCustomer($OrderItem, $Customer);

        $this->assertCount(1, $result);
        $this->assertSame($RefundRequest->getId(), $result[0]->getId());
    }

    public function testFindByOrderItemAndCustomerEmpty(): void
    {
        $this->createTestRefundRequest();
        $OtherCustomer = $this->createCustomer();
        $OrderItem = $this->createTestRefundRequest()->getOrderItem();

        $result = $this->refundRequestRepository->findByOrderItemAndCustomer($OrderItem, $OtherCustomer);

        $this->assertEmpty($result);
    }

    public function testGetRefundRequestCountsByCustomer(): void
    {
        $RefundRequest = $this->createTestRefundRequest();
        $Customer = $RefundRequest->getCustomer();
        $orderItemId = $RefundRequest->getOrderItem()->getId();

        $counts = $this->refundRequestRepository->getRefundRequestCountsByCustomer($Customer);

        $this->assertArrayHasKey($orderItemId, $counts);
        $this->assertSame(1, $counts[$orderItemId]);
    }

    public function testGetRefundRequestCountsByCustomerEmpty(): void
    {
        $Customer = $this->createCustomer();
        $counts = $this->refundRequestRepository->getRefundRequestCountsByCustomer($Customer);

        $this->assertEmpty($counts);
    }

    public function testGetRefundRequestCountsByCustomerMultiple(): void
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $this->setOrderStatus($Order, OrderStatus::DELIVERED);
        $this->entityManager->flush();

        $OrderItems = $Order->getProductOrderItems();
        $OrderItem = $OrderItems[0];
        $NewStatus = $this->entityManager->find(RefundRequestStatus::class, RefundRequestStatus::NEW);

        $rr1 = new RefundRequest();
        $rr1->setOrder($Order);
        $rr1->setOrderItem($OrderItem);
        $rr1->setCustomer($Customer);
        $rr1->setQuantity('1');
        $rr1->setReason('理由1');
        $rr1->setRefundRequestStatus($NewStatus);
        $this->entityManager->persist($rr1);

        $rr2 = new RefundRequest();
        $rr2->setOrder($Order);
        $rr2->setOrderItem($OrderItem);
        $rr2->setCustomer($Customer);
        $rr2->setQuantity('1');
        $rr2->setReason('理由2');
        $rr2->setRefundRequestStatus($NewStatus);
        $this->entityManager->persist($rr2);

        $this->entityManager->flush();

        $counts = $this->refundRequestRepository->getRefundRequestCountsByCustomer($Customer);

        $this->assertSame(2, $counts[$OrderItem->getId()]);
    }

    private function createTestRefundRequest(): RefundRequest
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $this->setOrderStatus($Order, OrderStatus::DELIVERED);
        $this->entityManager->flush();

        $OrderItem = $Order->getProductOrderItems()[0];

        $NewStatus = $this->entityManager->find(RefundRequestStatus::class, RefundRequestStatus::NEW);

        $RefundRequest = new RefundRequest();
        $RefundRequest->setOrder($Order);
        $RefundRequest->setOrderItem($OrderItem);
        $RefundRequest->setCustomer($Customer);
        $RefundRequest->setQuantity('1');
        $RefundRequest->setReason('テスト返品理由');
        $RefundRequest->setRefundRequestStatus($NewStatus);

        $this->entityManager->persist($RefundRequest);
        $this->entityManager->flush();

        return $RefundRequest;
    }

    private function setOrderStatus(Order $Order, int $statusId): void
    {
        $Status = $this->entityManager->find(OrderStatus::class, $statusId);
        $Order->setOrderStatus($Status);
    }
}
