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

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\RefundRequestStatus;
use Eccube\Entity\Order;
use Eccube\Entity\RefundRequest;
use Eccube\Entity\RefundRequestFile;
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

        $this->client->request(
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

        $this->client->request(
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

    /**
     * 件数セレクタの page_count パラメータがセッションに保存されることを検証する.
     */
    public function testIndexPageCountPersistsInSession(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_refund_request_page', ['page_no' => 1, 'page_count' => 20])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertEquals(20, $this->client->getRequest()->getSession()->get('eccube.admin.refund_request.search.page_count'));
    }

    /**
     * 一覧で初期表示時にゼロ件にならないこと（status=空コレクション → IN (NULL) 回帰防止）.
     */
    public function testIndexInitialShowsAllRefundRequests(): void
    {
        $this->createTestRefundRequest();

        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_refund_request')
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        // 「検索結果に合致するデータが見つかりませんでした」が出ていないこと
        $this->assertStringNotContainsString(
            'admin.common.search_no_result',
            (string) $this->client->getResponse()->getContent()
        );
        // テーブル行が 1 行以上存在すること
        $this->assertGreaterThanOrEqual(1, $crawler->filter('table tbody tr')->count());
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
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), (string) $response->getContent());

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

        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
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
        $this->assertStringContainsString('refund_request_', (string) $response->headers->get('Content-Disposition'));
    }

    public function testEditNotFound(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_refund_request_edit', ['id' => 999999])
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
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

    public function testExportCsvHeaderColumns(): void
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

        $content = $this->client->getInternalResponse()->getContent();

        $bom = "\xEF\xBB\xBF";
        if (str_starts_with($content, $bom)) {
            $content = substr($content, 3);
        }

        $lines = array_filter(explode("\n", $content), fn ($line) => $line !== '');
        $this->assertGreaterThanOrEqual(1, count($lines));

        $header = str_getcsv($lines[0]);
        $this->assertCount(9, $header);
    }

    public function testDownloadFile(): void
    {
        $RefundRequest = $this->createTestRefundRequest();
        $file = $this->createTestFile($RefundRequest);

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_refund_request_file', [
                'id' => $RefundRequest->getId(),
                'file_id' => $file->getId(),
            ])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testDownloadFileNotFound(): void
    {
        $RefundRequest = $this->createTestRefundRequest();

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_refund_request_file', [
                'id' => $RefundRequest->getId(),
                'file_id' => 999999,
            ])
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testDownloadFilePathTraversal(): void
    {
        $RefundRequest = $this->createTestRefundRequest();

        $file = new RefundRequestFile();
        $file->setFileName('../../etc/passwd');
        $file->setMimeType('text/plain');
        $file->setFileSize(100);
        $file->setSortNo(1);
        $RefundRequest->addRefundRequestFile($file);
        $this->entityManager->persist($file);
        $this->entityManager->flush();

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_refund_request_file', [
                'id' => $RefundRequest->getId(),
                'file_id' => $file->getId(),
            ])
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    private function createTestRefundRequest(): RefundRequest
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $this->setOrderStatus($Order, OrderStatus::DELIVERED);
        $this->entityManager->flush();

        $OrderItem = $Order->getProductOrderItems()[0];
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

    private function setOrderStatus(Order $Order, int $statusId): void
    {
        $Status = $this->entityManager->find(OrderStatus::class, $statusId);
        $this->assertInstanceOf(OrderStatus::class, $Status);
        $Order->setOrderStatus($Status);
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
