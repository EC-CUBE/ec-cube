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
use Eccube\Event\EccubeEvents;
use Eccube\Service\RefundRequestService;
use Eccube\Tests\EccubeTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Symfony\Component\Mime\Email;

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

    public function testCreateRefundRequestWithFiles(): void
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
        $RefundRequest->setReason('ファイル添付テスト');

        $tmpFile = tempnam(sys_get_temp_dir(), 'refund_test_');
        file_put_contents($tmpFile, str_repeat("\x00", 100));
        $uploadedFile = new UploadedFile($tmpFile, 'test.jpg', 'image/jpeg', null, true);

        $result = $this->refundRequestService->createRefundRequest($RefundRequest, [$uploadedFile]);

        $this->assertNotNull($result->getId());
        $this->assertCount(1, $result->getRefundRequestFiles());

        $file = $result->getRefundRequestFiles()->first();
        $this->assertNotNull($file->getMimeType());
        $this->assertSame(1, $file->getSortNo());
        $this->assertNotNull($file->getFileName());
        $this->assertEmailCount(1);
    }

    public function testCreateRefundRequestWithMultipleFiles(): void
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
        $RefundRequest->setReason('複数ファイル添付テスト');

        $files = [];
        for ($i = 0; $i < 3; $i++) {
            $tmpFile = tempnam(sys_get_temp_dir(), 'refund_test_');
            file_put_contents($tmpFile, str_repeat("\x00", 100));
            $files[] = new UploadedFile($tmpFile, "test_{$i}.png", 'image/png', null, true);
        }

        $result = $this->refundRequestService->createRefundRequest($RefundRequest, $files);

        $this->assertCount(3, $result->getRefundRequestFiles());

        $sortNos = [];
        foreach ($result->getRefundRequestFiles() as $file) {
            $sortNos[] = $file->getSortNo();
        }
        $this->assertSame([1, 2, 3], $sortNos);
    }

    public function testMailBodyContainsRefundRequestInfo(): void
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
        $RefundRequest->setQuantity('3');
        $RefundRequest->setReason('メール本文検証用の理由テキスト');

        $this->refundRequestService->createRefundRequest($RefundRequest);

        $this->assertEmailCount(1);

        /** @var Email $email */
        $email = $this->getMailerMessage();
        $body = $email->getTextBody();

        $this->assertStringContainsString($Order->getOrderNo(), (string) $body);
        $this->assertStringContainsString('メール本文検証用の理由テキスト', (string) $body);
    }

    public function testChangeStatusDispatchesEvent(): void
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
        $RefundRequest->setReason('イベント検証');

        $this->refundRequestService->createRefundRequest($RefundRequest);

        $dispatched = false;
        $dispatcher = static::getContainer()->get(TraceableEventDispatcher::class);
        $dispatcher->addListener(EccubeEvents::REFUND_REQUEST_STATUS_CHANGE, function () use (&$dispatched): void {
            $dispatched = true;
        });

        $this->refundRequestService->changeStatus($RefundRequest, 'start_processing');

        $this->assertTrue($dispatched);
    }

    private function setOrderStatus(Order $Order, int $statusId): void
    {
        $Status = $this->entityManager->find(OrderStatus::class, $statusId);
        $this->assertInstanceOf(OrderStatus::class, $Status);
        $Order->setOrderStatus($Status);
    }
}
