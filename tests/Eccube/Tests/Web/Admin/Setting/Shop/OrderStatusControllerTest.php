<?php

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

namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Symfony\Component\HttpFoundation\Request;
use Eccube\Entity\Master\CustomerOrderStatus;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\OrderStatusColor;
use Eccube\Repository\Master\CustomerOrderStatusRepository;
use Eccube\Repository\Master\OrderStatusColorRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class OrderStatusControllerTest extends AbstractAdminWebTestCase
{
    private ?OrderStatusRepository $orderStatusRepository = null;

    private ?OrderStatusColorRepository $orderStatusColorRepository = null;

    private ?CustomerOrderStatusRepository $customerOrderStatusRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderStatusRepository = $this->entityManager->getRepository(OrderStatus::class);
        $this->orderStatusColorRepository = $this->entityManager->getRepository(OrderStatusColor::class);
        $this->customerOrderStatusRepository = $this->entityManager->getRepository(CustomerOrderStatus::class);
    }

    public function testRouting()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_setting_shop_order_status'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testSubmit()
    {
        $formData = $this->createFormData();
        $formData['OrderStatuses'][0]['name'] = 'テスト名称(受注管理)';
        $formData['OrderStatuses'][0]['customer_order_status_name'] = 'テスト名称(マイページ)';
        $formData['OrderStatuses'][0]['color'] = 'テスト色';

        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_setting_shop_order_status'));
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop_order_status'),
            ['form' => $formData]
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $OrderStatus = $this->orderStatusRepository->findOneBy([], ['sort_no' => 'ASC']);
        $CustomerOrderStatus = $this->customerOrderStatusRepository->findOneBy([], ['sort_no' => 'ASC']);
        $OrderStatusColor = $this->orderStatusColorRepository->findOneBy([], ['sort_no' => 'ASC']);

        $this->assertSame('テスト名称(受注管理)', $OrderStatus->getName());
        $this->assertSame('テスト名称(マイページ)', $CustomerOrderStatus->getName());
        $this->assertSame('テスト色', $OrderStatusColor->getName());
    }

    public function testSubmitWithError()
    {
        $formData = $this->createFormData();
        $formData['OrderStatuses'][0]['name'] = '';
        $formData['OrderStatuses'][0]['customer_order_status_name'] = '';
        $formData['OrderStatuses'][0]['color'] = '';

        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_setting_shop_order_status'));
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop_order_status'),
            ['form' => $formData]
        );

        $this->assertFalse($this->client->getResponse()->isRedirection());
        $this->assertStringContainsString('入力されていません。', $crawler->text());
    }

    private function createFormData()
    {
        $form = [
            '_token' => 'dummy',
            'OrderStatuses' => [],
        ];

        $OrderStatuses = $this->orderStatusRepository->findBy([], ['sort_no' => 'ASC']);

        foreach ($OrderStatuses as $OrderStatus) {
            $form['OrderStatuses'][] = [
                'name' => $OrderStatus->getName(),
                'customer_order_status_name' => $this->customerOrderStatusRepository
                    ->find($OrderStatus->getId())
                    ->getName(),
                'color' => $this->orderStatusColorRepository
                    ->find($OrderStatus->getId())
                    ->getName(),
                'display_order_count' => $OrderStatus->isDisplayOrderCount() ? '1' : '',
            ];
        }

        return $form;
    }
}
