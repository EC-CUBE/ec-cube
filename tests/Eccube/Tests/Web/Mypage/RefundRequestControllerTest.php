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

namespace Eccube\Tests\Web\Mypage;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\RefundRequestStatus;
use Eccube\Entity\Order;
use Eccube\Entity\Product;
use Eccube\Entity\RefundRequest;
use Eccube\Entity\RefundRequestFile;
use Eccube\Tests\Web\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RefundRequestControllerTest extends AbstractWebTestCase
{
    private ?Customer $Customer = null;
    private ?Order $Order = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $this->setOrderStatus($this->Order, OrderStatus::DELIVERED);
        $this->entityManager->flush();
    }

    public function testIndex(): void
    {
        $this->loginTo($this->Customer);
        $OrderItem = $this->Order->getProductOrderItems()[0];

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_refund_request', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndexPostRedirectsToConfirm(): void
    {
        $this->loginTo($this->Customer);
        $OrderItem = $this->Order->getProductOrderItems()[0];

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('mypage_refund_request', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ]),
            [
                'refund_request' => [
                    '_token' => 'dummy',
                    'quantity' => '1',
                    'reason' => 'テスト理由です',
                ],
            ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertStringContainsString('/confirm', (string) $this->client->getResponse()->headers->get('Location'));
    }

    public function testConfirmDisplay(): void
    {
        $this->loginTo($this->Customer);
        $OrderItem = $this->Order->getProductOrderItems()[0];

        // 入力画面の POST でセッションに格納
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('mypage_refund_request', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ]),
            [
                'refund_request' => [
                    '_token' => 'dummy',
                    'quantity' => '1',
                    'reason' => 'テスト理由です',
                ],
            ]
        );

        // 確認画面の GET 表示
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_refund_request_confirm', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testConfirmWithoutSessionRedirectsToIndex(): void
    {
        $this->loginTo($this->Customer);
        $OrderItem = $this->Order->getProductOrderItems()[0];

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_refund_request_confirm', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ])
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());
    }

    public function testConfirmPostCreatesRefundRequest(): void
    {
        $this->loginTo($this->Customer);
        $OrderItem = $this->Order->getProductOrderItems()[0];

        // 入力画面の POST でセッションに格納
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('mypage_refund_request', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ]),
            [
                'refund_request' => [
                    '_token' => 'dummy',
                    'quantity' => '1',
                    'reason' => 'テスト理由です',
                ],
            ]
        );

        // 確認画面の POST で確定
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('mypage_refund_request_confirm', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ]),
            ['_token' => 'dummy']
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $count = (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(rr)')
            ->from(RefundRequest::class, 'rr')
            ->where('rr.Customer = :c')
            ->setParameter('c', $this->Customer)
            ->getQuery()->getSingleScalarResult();
        $this->assertSame(1, $count);
    }

    public function testConfirmPostWithFilePersistsFile(): void
    {
        $this->loginTo($this->Customer);
        $OrderItem = $this->Order->getProductOrderItems()[0];

        // テスト用ダミーアップロードファイルを作成
        $tmpFile = tempnam(sys_get_temp_dir(), 'rr_test_');
        // 1x1 透明 PNG
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
        file_put_contents($tmpFile, $png);

        $uploaded = new UploadedFile($tmpFile, 'evidence.png', 'image/png', null, true);

        // 入力画面の POST でセッションに格納（一時保存される）
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('mypage_refund_request', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ]),
            [
                'refund_request' => [
                    '_token' => 'dummy',
                    'quantity' => '1',
                    'reason' => 'テスト理由です',
                ],
            ],
            [
                'refund_request' => ['files' => [$uploaded]],
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirection(), 'index POST should redirect to confirm');

        // 確認画面の POST で確定
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('mypage_refund_request_confirm', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ]),
            ['_token' => 'dummy']
        );

        $this->assertTrue($this->client->getResponse()->isRedirection(), 'confirm POST should redirect to complete');

        $RefundRequest = $this->entityManager->createQueryBuilder()
            ->select('rr')
            ->from(RefundRequest::class, 'rr')
            ->where('rr.Customer = :c')
            ->setParameter('c', $this->Customer)
            ->getQuery()->getOneOrNullResult();
        $this->assertInstanceOf(RefundRequest::class, $RefundRequest);
        $this->assertCount(1, $RefundRequest->getRefundRequestFiles(), 'エビデンスファイルが永続化されること');
    }

    public function testComplete(): void
    {
        $this->loginTo($this->Customer);
        $OrderItem = $this->Order->getProductOrderItems()[0];

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_refund_request_complete', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testItemHistory(): void
    {
        $this->loginTo($this->Customer);
        $OrderItem = $this->Order->getProductOrderItems()[0];

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_refund_request_item_history', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testItemHistoryWithRefundRequests(): void
    {
        $OrderItem = $this->Order->getProductOrderItems()[0];
        $this->createRefundRequest($this->Order, $OrderItem, $this->Customer);

        $this->loginTo($this->Customer);

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_refund_request_item_history', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testAccessDeniedForNonDeliveredOrder(): void
    {
        $this->setOrderStatus($this->Order, OrderStatus::NEW);
        $this->entityManager->flush();

        $this->loginTo($this->Customer);
        $OrderItem = $this->Order->getProductOrderItems()[0];

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_refund_request', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ])
        );

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testNotFoundForOtherCustomerOrder(): void
    {
        $OtherCustomer = $this->createCustomer();
        $this->loginTo($OtherCustomer);

        $OrderItem = $this->Order->getProductOrderItems()[0];

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_refund_request', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ])
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testNotFoundForInvalidOrderItemId(): void
    {
        $this->loginTo($this->Customer);

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_refund_request', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => 999999,
            ])
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testNotFoundForOtherCustomerHistory(): void
    {
        $OtherCustomer = $this->createCustomer();
        $this->loginTo($OtherCustomer);

        $OrderItem = $this->Order->getProductOrderItems()[0];

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_refund_request_item_history', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ])
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testAccessDeniedForRefundNotAllowed(): void
    {
        $OrderItem = $this->Order->getProductOrderItems()[0];
        $Product = $OrderItem->getProduct();
        $this->assertInstanceOf(Product::class, $Product);
        $Product->setRefundAllowed(false);
        $this->entityManager->flush();

        $this->loginTo($this->Customer);

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_refund_request', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ])
        );

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testValidationQuantityZero(): void
    {
        $this->loginTo($this->Customer);
        $OrderItem = $this->Order->getProductOrderItems()[0];

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('mypage_refund_request', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ]),
            [
                'refund_request' => [
                    '_token' => 'dummy',
                    'quantity' => '0',
                    'reason' => 'テスト理由です',
                ],
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testValidationReasonEmpty(): void
    {
        $this->loginTo($this->Customer);
        $OrderItem = $this->Order->getProductOrderItems()[0];

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('mypage_refund_request', [
                'order_no' => $this->Order->getOrderNo(),
                'order_item_id' => $OrderItem->getId(),
            ]),
            [
                'refund_request' => [
                    '_token' => 'dummy',
                    'quantity' => '1',
                    'reason' => '',
                ],
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testDownloadFile(): void
    {
        $OrderItem = $this->Order->getProductOrderItems()[0];
        $RefundRequest = $this->createRefundRequest($this->Order, $OrderItem, $this->Customer);
        $file = $this->createTestFile($RefundRequest);

        $this->loginTo($this->Customer);

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_refund_request_file', [
                'refund_request_id' => $RefundRequest->getId(),
                'file_id' => $file->getId(),
            ])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testDownloadFileOtherCustomer(): void
    {
        $OrderItem = $this->Order->getProductOrderItems()[0];
        $RefundRequest = $this->createRefundRequest($this->Order, $OrderItem, $this->Customer);
        $file = $this->createTestFile($RefundRequest);

        $OtherCustomer = $this->createCustomer();
        $this->loginTo($OtherCustomer);

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_refund_request_file', [
                'refund_request_id' => $RefundRequest->getId(),
                'file_id' => $file->getId(),
            ])
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testDownloadFileNotFound(): void
    {
        $OrderItem = $this->Order->getProductOrderItems()[0];
        $RefundRequest = $this->createRefundRequest($this->Order, $OrderItem, $this->Customer);

        $this->loginTo($this->Customer);

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_refund_request_file', [
                'refund_request_id' => $RefundRequest->getId(),
                'file_id' => 999999,
            ])
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testDownloadFilePathTraversal(): void
    {
        $OrderItem = $this->Order->getProductOrderItems()[0];
        $RefundRequest = $this->createRefundRequest($this->Order, $OrderItem, $this->Customer);

        $file = new RefundRequestFile();
        $file->setFileName('../../etc/passwd');
        $file->setMimeType('text/plain');
        $file->setFileSize(100);
        $file->setSortNo(1);
        $RefundRequest->addRefundRequestFile($file);
        $this->entityManager->persist($file);
        $this->entityManager->flush();

        $this->loginTo($this->Customer);

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('mypage_refund_request_file', [
                'refund_request_id' => $RefundRequest->getId(),
                'file_id' => $file->getId(),
            ])
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    private function setOrderStatus(Order $Order, int $statusId): void
    {
        $Status = $this->entityManager->find(OrderStatus::class, $statusId);
        $this->assertInstanceOf(OrderStatus::class, $Status);
        $Order->setOrderStatus($Status);
    }

    private function createRefundRequest(Order $Order, mixed $OrderItem, Customer $Customer): RefundRequest
    {
        $NewStatus = $this->entityManager->find(RefundRequestStatus::class, RefundRequestStatus::NEW);
        $this->assertInstanceOf(RefundRequestStatus::class, $NewStatus);

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

    private function createTestFile(RefundRequest $RefundRequest): RefundRequestFile
    {
        $dir = static::getContainer()->get(EccubeConfig::class)['eccube_save_refund_request_file_dir'];
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileName = bin2hex(random_bytes(16)).'.jpg';
        file_put_contents($dir.'/'.$fileName, str_repeat("\x00", 100));

        $file = new RefundRequestFile();
        $file->setFileName($fileName);
        $file->setMimeType('image/jpeg');
        $file->setFileSize(100);
        $file->setSortNo(1);
        $RefundRequest->addRefundRequestFile($file);

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $file;
    }
}
