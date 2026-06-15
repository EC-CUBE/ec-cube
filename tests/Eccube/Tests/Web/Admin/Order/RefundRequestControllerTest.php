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

namespace Eccube\Tests\Web\Admin\Order;

use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\RefundRequestStatus;
use Eccube\Entity\Order;
use Eccube\Entity\RefundRequest;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RefundRequestControllerTest extends AbstractAdminWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testIndex(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_refund_request')
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndexWithSearch(): void
    {
        $RefundRequest = $this->createTestRefundRequest();

        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_refund_request'),
            [
                'admin_search_refund_request' => [
                    'multi' => (string) $RefundRequest->getId(),
                ],
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndexWithStatusSearch(): void
    {
        $this->createTestRefundRequest();

        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_refund_request'),
            [
                'admin_search_refund_request' => [
                    'status' => [(string) RefundRequestStatus::NEW],
                ],
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testEdit(): void
    {
        $RefundRequest = $this->createTestRefundRequest();

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_refund_request_edit', ['id' => $RefundRequest->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testEditPostAdminNote(): void
    {
        $RefundRequest = $this->createTestRefundRequest();

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_refund_request_edit', ['id' => $RefundRequest->getId()]),
            [
                'admin_refund_request_edit' => [
                    '_token' => 'dummy',
                    'admin_note' => '管理者メモテスト',
                    'transition' => '',
                ],
            ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $this->entityManager->refresh($RefundRequest);
        $this->assertSame('管理者メモテスト', $RefundRequest->getAdminNote());
    }

    public function testEditPostWithTransition(): void
    {
        $RefundRequest = $this->createTestRefundRequest();

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_refund_request_edit', ['id' => $RefundRequest->getId()]),
            [
                'admin_refund_request_edit' => [
                    '_token' => 'dummy',
                    'admin_note' => '',
                    'transition' => 'start_processing',
                ],
            ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $this->entityManager->refresh($RefundRequest);
        $this->assertSame(RefundRequestStatus::PROCESSING, $RefundRequest->getRefundRequestStatus()->getId());
    }

    public function testUpdateStatus(): void
    {
        $RefundRequest = $this->createTestRefundRequest();

        $this->client->request(
            Request::METHOD_PUT,
            $this->generateUrl('admin_refund_request_update_status', ['id' => $RefundRequest->getId()]),
            [
                'transition' => 'start_processing',
                '_token' => 'dummy',
            ]
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));

        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);
    }

    public function testUpdateStatusInvalidTransition(): void
    {
        $RefundRequest = $this->createTestRefundRequest();

        $this->client->request(
            Request::METHOD_PUT,
            $this->generateUrl('admin_refund_request_update_status', ['id' => $RefundRequest->getId()]),
            [
                'transition' => 'accept',
                '_token' => 'dummy',
            ]
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
    }

    public function testUpdateStatusNoTransition(): void
    {
        $RefundRequest = $this->createTestRefundRequest();

        $this->client->request(
            Request::METHOD_PUT,
            $this->generateUrl('admin_refund_request_update_status', ['id' => $RefundRequest->getId()]),
            [
                '_token' => 'dummy',
            ]
        );

        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function testExport(): void
    {
        $this->createTestRefundRequest();

        $session = $this->createSession($this->client);
        $session->set('eccube.admin.refund_request.search', []);
        $session->save();

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_refund_request_export')
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('application/octet-stream', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('refund_request_', $response->headers->get('Content-Disposition'));
    }

    public function testEditNotFound(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_refund_request_edit', ['id' => 999999])
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testPagination(): void
    {
        $this->createTestRefundRequest();

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_refund_request_page', ['page_no' => 1])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
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
