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

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\RefundRequestStatus;
use Eccube\Entity\Order;
use Eccube\Entity\RefundRequest;
use Eccube\Event\EccubeEvents;
use Eccube\Service\RefundRequestService;
use Eccube\Tests\EccubeTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\Email;

final class RefundRequestServiceTest extends EccubeTestCase
{
    use MailerAssertionsTrait;

    private const SESSION_ID = 'phpunit-session';

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

        $result = $this->refundRequestService->createRefundRequest($RefundRequest, [], self::SESSION_ID);

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

        $result = $this->refundRequestService->createRefundRequest($RefundRequest, [], self::SESSION_ID);

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

        $this->refundRequestService->createRefundRequest($RefundRequest, [], self::SESSION_ID);

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

        $this->refundRequestService->createRefundRequest($RefundRequest, [], self::SESSION_ID);

        $this->expectException(\InvalidArgumentException::class);
        $this->refundRequestService->changeStatus($RefundRequest, 'accept');
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

        $this->refundRequestService->createRefundRequest($RefundRequest, [], self::SESSION_ID);

        $transitions = $this->refundRequestService->getAvailableTransitions($RefundRequest);
        $this->assertArrayHasKey('start_processing', $transitions);
        $this->assertCount(1, $transitions);
    }

    public function testSaveTempFile(): void
    {
        $uploadedFile = $this->createUploadedFile('test.jpg', 'image/jpeg');
        $info = $this->refundRequestService->saveTempFile($uploadedFile, self::SESSION_ID);

        $this->assertArrayHasKey('key', $info);
        $this->assertSame('test.jpg', $info['client_name']);
        $this->assertSame('image/jpeg', $info['mime_type']);
        $this->assertGreaterThan(0, $info['size']);
        $this->assertNotNull($this->refundRequestService->getTempFilePath(self::SESSION_ID, $info['key']));
    }

    public function testCreateRefundRequestWithTempFiles(): void
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $this->setOrderStatus($Order, OrderStatus::DELIVERED);
        $this->entityManager->flush();

        $OrderItem = $Order->getProductOrderItems()[0];

        // 事前に一時保存
        $uploadedFile = $this->createUploadedFile('test.jpg', 'image/jpeg');
        $info = $this->refundRequestService->saveTempFile($uploadedFile, self::SESSION_ID);

        $RefundRequest = new RefundRequest();
        $RefundRequest->setOrder($Order);
        $RefundRequest->setOrderItem($OrderItem);
        $RefundRequest->setCustomer($Customer);
        $RefundRequest->setQuantity('1');
        $RefundRequest->setReason('ファイル添付テスト');

        $result = $this->refundRequestService->createRefundRequest($RefundRequest, [$info], self::SESSION_ID);

        $this->assertNotNull($result->getId());
        $this->assertCount(1, $result->getRefundRequestFiles());

        $file = $result->getRefundRequestFiles()->first();
        $this->assertSame('image/jpeg', $file->getMimeType());
        $this->assertSame(1, $file->getSortNo());
        $this->assertSame($info['key'], $file->getFileName());
        $this->assertEmailCount(1);

        // 一時ファイルが本領域に移動して掃除されていること
        $finalDir = static::getContainer()->get(EccubeConfig::class)['eccube_save_refund_request_file_dir'];
        $this->assertFileExists($finalDir.'/'.$info['key']);
        $this->assertNull($this->refundRequestService->getTempFilePath(self::SESSION_ID, $info['key']));
    }

    public function testCreateRefundRequestWithMultipleTempFiles(): void
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

        $infos = [];
        for ($i = 0; $i < 3; $i++) {
            $up = $this->createUploadedFile("test_{$i}.png", 'image/png');
            $infos[] = $this->refundRequestService->saveTempFile($up, self::SESSION_ID);
        }

        $result = $this->refundRequestService->createRefundRequest($RefundRequest, $infos, self::SESSION_ID);

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

        $this->refundRequestService->createRefundRequest($RefundRequest, [], self::SESSION_ID);

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

        $this->refundRequestService->createRefundRequest($RefundRequest, [], self::SESSION_ID);

        $dispatched = false;
        $dispatcher = static::getContainer()->get(EventDispatcherInterface::class);
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

    private function createUploadedFile(string $name, string $mime): UploadedFile
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'refund_test_');
        // MIME 推定 (Symfony MimeTypeGuesser/file コマンド) が通るように本物のマジックバイトを入れる
        $content = match ($mime) {
            'image/jpeg' => "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x00\x00\x01\x00\x01\x00\x00\xFF\xD9",
            'image/png' => base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='),
            default => str_repeat("\x00", 100),
        };
        file_put_contents($tmpFile, $content);

        return new UploadedFile($tmpFile, $name, $mime, null, true);
    }
}
