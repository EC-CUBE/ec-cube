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

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\RefundRequestStatus;
use Eccube\Entity\Order;
use Eccube\Entity\RefundRequest;
use Eccube\Tests\Web\AbstractWebTestCase;
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

    public function testIndexPostConfirm(): void
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
                'mode' => 'confirm',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndexPostComplete(): void
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
                'mode' => 'complete',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());
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

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
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

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
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

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
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

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testAccessDeniedForRefundNotAllowed(): void
    {
        $OrderItem = $this->Order->getProductOrderItems()[0];
        $Product = $OrderItem->getProduct();
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

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testValidationQuantityZero(): void
    {
        $this->loginTo($this->Customer);
        $OrderItem = $this->Order->getProductOrderItems()[0];

        $crawler = $this->client->request(
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
                'mode' => 'confirm',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testValidationReasonEmpty(): void
    {
        $this->loginTo($this->Customer);
        $OrderItem = $this->Order->getProductOrderItems()[0];

        $crawler = $this->client->request(
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
                'mode' => 'confirm',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    private function setOrderStatus(Order $Order, int $statusId): void
    {
        $Status = $this->entityManager->find(OrderStatus::class, $statusId);
        $Order->setOrderStatus($Status);
    }

    private function createRefundRequest(Order $Order, mixed $OrderItem, Customer $Customer): RefundRequest
    {
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
}
