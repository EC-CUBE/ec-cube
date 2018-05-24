<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Entity\Payment;
use Eccube\Repository\PaymentRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class PaymentControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->paymentRepository = $this->container->get(PaymentRepository::class);
    }

    public function testRouting()
    {
        $this->client->request('GET', $this->generateUrl('admin_setting_shop_payment'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingNew()
    {
        $this->client->request('GET', $this->generateUrl('admin_setting_shop_payment_new'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @param $isSuccess
     * @param $expected
     * @dataProvider dataSubmitProvider
     */
    public function testNew($isSuccess, $expected)
    {
        $formData = $this->createFormData();
        if (!$isSuccess) {
            $formData['method'] = '';
        }

        $crawler = $this->client->request('POST',
            $this->generateUrl('admin_setting_shop_payment_new'),
            [
                'payment_register' => $formData,
            ]
        );

        $this->expected = $expected;
        $this->actual = $this->client->getResponse()->isRedirection();
        $this->verify();
    }

    public function testRoutingEdit()
    {
        $Payment = $this->paymentRepository->find(1);
        $this->client->request('GET', $this->generateUrl('admin_setting_shop_payment_edit', ['id' => $Payment->getId()]));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @param $isSuccess
     * @param $expected
     * @dataProvider dataSubmitProvider
     */
    public function testEdit($isSuccess, $expected)
    {
        $formData = $this->createFormData();
        if (!$isSuccess) {
            $formData['method'] = '';
        }

        $Payment = $this->paymentRepository->find(1);

        $this->client->request('POST',
            $this->generateUrl('admin_setting_shop_payment_edit', ['id' => $Payment->getId()]),
            [
                'payment_register' => $formData,
            ]
        );
        $this->expected = $expected;
        $this->actual = $this->client->getResponse()->isRedirection();
        $this->verify();
    }

    public function testDeleteSuccess()
    {
        $Member = $this->createMember();
        $Payment = new Payment();
        $Payment->setMethod('testDeleteSuccess')
            ->setCharge(0)
            ->setRuleMin(0)
            ->setRuleMax(9999)
            ->setCreator($Member)
            ->setVisible(true);

        $this->entityManager->persist($Payment);
        $this->entityManager->flush();

        $pid = $Payment->getId();
        $this->client->request('DELETE',
            $this->generateUrl('admin_setting_shop_payment_delete', ['id' => $pid])
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $Payment = $this->paymentRepository->find($pid);
        $this->assertNull($Payment);
    }

    public function testDeleteFail_NotFound()
    {
        $pid = 9999;
        $this->client->request(
            'DELETE',
            $this->generateUrl('admin_setting_shop_payment_delete', ['id' => $pid])
        );
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testUp()
    {
        $pid = 4;
        $Payment = $this->paymentRepository->find($pid);
        $before = $Payment->getSortNo();
        $this->client->request('PUT',
            $this->generateUrl('admin_setting_shop_payment_up', ['id' => $pid])
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $after = $Payment->getSortNo();
        $this->actual = $after;
        $this->expected = $before + 1;
        $this->verify();
    }

    public function testDown()
    {
        $pid = 1;
        $Payment = $this->paymentRepository->find($pid);
        $before = $Payment->getSortNo();
        $this->client->request('PUT',
            $this->generateUrl('admin_setting_shop_payment_down', ['id' => $pid])
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $after = $Payment->getSortNo();
        $this->actual = $after;
        $this->expected = $before - 1;
        $this->verify();
    }

    public function testAddImage()
    {
        $formData = $this->createFormData();

        $this->client->request('POST',
            $this->generateUrl('admin_payment_image_add'),
            [
                'payment_register' => $formData,
            ],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testAddImage_NotAjax()
    {
        $formData = $this->createFormData();

        $this->client->request('POST',
            $this->generateUrl('admin_payment_image_add'),
            [
                'payment_register' => $formData,
            ],
            []
        );
        $this->assertSame(400, $this->client->getResponse()->getStatusCode());
    }

    //    public function testAddImage_MineNotSupported()
    //    {
    //        $formData = $this->createFormData();
    //
    //        $formData['payment_image'] = 'abc.avi';
    //        $formData['payment_image_file'] = 'abc.avi';
    //
    //        $this->client->request('POST',
    //            $this->app->url('admin_payment_image_add'),
    //            array(
    //                'payment_register' => $formData
    //            ),
    //            array(),
    //            array(
    //                'HTTP_X-Requested-With' => 'XMLHttpRequest',
    //            )
    //        );
    //    }

    public function testMoveSortNo()
    {
        /** @var Payment[] $Payments */
        $Payments = $this->paymentRepository->findBy([], ['sort_no' => 'DESC']);

        $this->expected = [];
        foreach ($Payments as $Payment) {
            $this->expected[$Payment->getId()] = $Payment->getSortNo();
        }

        // swap sort_no
        reset($this->expected);
        $firstKey = key($this->expected);
        end($this->expected);
        $lastKey = key($this->expected);

        $tmp = $this->expected[$firstKey];
        $this->expected[$firstKey] = $this->expected[$lastKey];
        $this->expected[$lastKey] = $tmp;

        $this->client->request('POST',
            $this->generateUrl('admin_setting_shop_payment_sort_no_move'),
            $this->expected,
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $Payments = $this->paymentRepository->findBy([], ['sort_no' => 'DESC']);
        $this->actual = [];
        foreach ($Payments as $Payment) {
            $this->actual[$Payment->getId()] = $Payment->getSortNo();
        }
        sort($this->expected);
        sort($this->actual);

        $this->verify();
    }

    public function createFormData()
    {
        $charge = 10000;
        if (mt_rand(0, 1)) {
            $charge = number_format($charge);
        }

        $rule_max = 10000;
        if (mt_rand(0, 1)) {
            $rule_max = number_format($rule_max);
        }

        $form = [
            '_token' => 'dummy',
            'method' => 'Test',
            'charge' => $charge,
            'rule_min' => '100',
            'rule_max' => $rule_max,
            'payment_image' => 'abc.png',
            'payment_image_file' => 'abc.png',
            'fixed' => true,
        ];

        return $form;
    }

    public function dataSubmitProvider()
    {
        return [
            [false, false],
            [true, true],
            // To do implement
        ];
    }

    //    TO DO : implement
}
