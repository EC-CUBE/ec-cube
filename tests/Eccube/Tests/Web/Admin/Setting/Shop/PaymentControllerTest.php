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

use Eccube\Entity\Payment;
use Eccube\Repository\PaymentRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PaymentControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @var string
     */
    protected $imageDir;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->paymentRepository = $this->entityManager->getRepository(\Eccube\Entity\Payment::class);
        $this->imageDir = sys_get_temp_dir().'/'.sha1(mt_rand());
        $fs = new Filesystem();
        $fs->mkdir($this->imageDir);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->imageDir);
        parent::tearDown();
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

    public function testDeleteFailNotFound()
    {
        $pid = 9999;
        $this->client->request(
            'DELETE',
            $this->generateUrl('admin_setting_shop_payment_delete', ['id' => $pid])
        );
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testAddImage()
    {
        $formData = $this->createFormData();

        copy(
            __DIR__.'/../../../../../../../html/upload/save_image/sand-1.png',
            $this->imageDir.'/sand-1.png'
        );
        $image = new UploadedFile(
            $this->imageDir.'/sand-1.png',
            'sand-1.png',
            'image/png',
            null, null, true
        );
        $this->client->request('POST',
            $this->generateUrl('admin_payment_image_add'),
            [
                'payment_register' => $formData,
            ],
            [
                'payment_register' => ['payment_image_file' => $image],
            ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testAddImageWithUppercaseSuffix()
    {
        $formData = $this->createFormData();
        copy(
            __DIR__.'/../../../../../../../html/upload/save_image/sand-1.png',
            $this->imageDir.'/sand-1.PNG'
        );
        $image = new UploadedFile(
            $this->imageDir.'/sand-1.PNG',
            'sand-1.PNG',
            'image/png',
            null, null, true
        );

        $this->client->request('POST',
            $this->generateUrl('admin_payment_image_add'),
            [
                'payment_register' => $formData,
            ],
            [
                'payment_register' => ['payment_image_file' => $image],
            ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testAddImageNotAjax()
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

    public function testAddImageMineNotSupported()
    {
        $formData = $this->createFormData();
        copy(
            __DIR__.'/../../../../../../Fixtures/categories.csv',
            $this->imageDir.'/categories.png'
        );
        $image = new UploadedFile(
            $this->imageDir.'/categories.png',
            'categories.png',
            'image/png',
            null, null, true
        );

        $crawler = $this->client->request('POST',
           $this->generateUrl('admin_payment_image_add'),
           [
               'payment_register' => $formData,
           ],
           [
               'payment_register' => ['payment_image_file' => $image],
           ],
           [
               'HTTP_X-Requested-With' => 'XMLHttpRequest',
           ]
        );
        $this->assertFalse($this->client->getResponse()->isSuccessful());
    }

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
            'visible' => true,
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
