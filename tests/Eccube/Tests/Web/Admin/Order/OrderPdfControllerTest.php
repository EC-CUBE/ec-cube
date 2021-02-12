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

namespace Eccube\Tests\Web\Admin\Order;

use Eccube\Common\Constant;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\OrderPdf;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\OrderPdfRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Faker\Generator;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Client;

/**
 * Class OrderPdfControllerTest.
 */
class OrderPdfControllerTest extends AbstractAdminWebTestCase
{
    /** @var OrderStatusRepository */
    protected $orderStatusRepo;

    /** @var OrderRepository */
    protected $orderRepo;

    /** @var OrderPdfRepository */
    protected $orderPdfRepository;

    public function setUp()
    {
        parent::setUp();
        $this->orderStatusRepo = $this->entityManager->getRepository(\Eccube\Entity\Master\OrderStatus::class);
        $this->orderRepo = $this->entityManager->getRepository(\Eccube\Entity\Order::class);
        $this->orderPdfRepository = $this->entityManager->getRepository(\Eccube\Entity\OrderPdf::class);
    }

    /**
     * testRoutingOrderExportPdf test
     */
    public function testRoutingOrderExportPdf()
    {
        $Order = $this->createOrderForSearch();

        $this->client->request('POST',
            $this->generateUrl('admin_order_export_pdf'),
            [
                '_token' => 'dummy',
                'ids' => [$Order->getId()],
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Render test.
     */
    public function testRender()
    {
        $Order = $this->createOrderForSearch();
        $Shippings = $Order->getShippings();
        $shippingId = $Shippings[0]->getId();
        /**
         * @var Crawler
         */
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_order')
        );

        $this->assertContains((string) $shippingId, $crawler->filter('#search_result')->html());

        $expectedText = '納品書出力';
        $actualNode = $crawler->filter('.btn-bulk-wrapper')->html();
        $this->assertContains($expectedText, $actualNode);
    }

    /**
     * Render order pdf download.
     */
    public function testRenderDownloadWithDefault()
    {
        $Order = $this->createOrderForSearch();
        $Shippings = $Order->getShippings();
        $shippingId = $Shippings[0]->getId();

        /**
         * @var Crawler
         */
        $crawler = $this->client->request('POST',
            $this->generateUrl('admin_order_export_pdf'),
            [
                '_token' => 'dummy',
                'ids' => [$shippingId],
            ]
        );
        $html = $crawler->filter('#order_pdf_form')->html();
        $this->assertContains((string) $shippingId, $html);
        $this->assertContains('お買上げ明細書(納品書)', $html);
        $this->assertContains('このたびはお買上げいただきありがとうございます。', $html);
        $this->assertContains('下記の内容にて納品させていただきます。', $html);
        $this->assertContains('ご確認くださいますよう、お願いいたします。', $html);
    }

    /**
     * Render order pdf download.
     */
    public function testRenderDownloadWithPreviousInput()
    {
        $Order = $this->createOrderForSearch();
        $Shippings = $Order->getShippings();
        $shippingId = $Shippings[0]->getId();

        /**
         * @var Crawler
         */
        $crawler = $this->client->request('POST',
            $this->generateUrl('admin_order_export_pdf'),
            [
                '_token' => 'dummy',
                'ids' => [$shippingId],
            ]);

        $form = $this->getForm($crawler);

        /**
         * @var Generator
         */
        $faker = $this->getFaker();
        $form['order_pdf[title]'] = $faker->text(50);
        $form['order_pdf[message1]'] = $faker->text(30);
        $form['order_pdf[message2]'] = $faker->text(30);
        $form['order_pdf[message3]'] = $faker->text(30);
        $form['order_pdf[note1]'] = $faker->text(50);
        $form['order_pdf[note2]'] = $faker->text(50);
        $form['order_pdf[note3]'] = $faker->text(50);
        $form['order_pdf[default]'] = 1;
        $this->client->submit($form);
        $this->actual = $this->client->getResponse()->headers->get('Content-Type');
        $this->expected = 'application/pdf';
        $this->verify();

        $crawler = $this->client->request('GET', $this->generateUrl('admin_order_export_pdf'),
            [
                '_token' => 'dummy',
                'ids' => [$shippingId],
            ]);
        $html = $crawler->filter('#order_pdf_form')->html();

        $this->assertContains((string) $shippingId, $html);

        $this->assertContains($form['order_pdf[title]']->getValue(), $html);
        $this->assertContains($form['order_pdf[message1]']->getValue(), $html);
        $this->assertContains($form['order_pdf[message2]']->getValue(), $html);
        $this->assertContains($form['order_pdf[message3]']->getValue(), $html);
        $this->assertContains($form['order_pdf[note1]']->getValue(), $html);
        $this->assertContains($form['order_pdf[note2]']->getValue(), $html);
        $this->assertContains($form['order_pdf[note3]']->getValue(), $html);
    }

    /**
     * Order pdf download.
     */
    public function testDownloadIdInvalid()
    {
        $this->client->request('GET', $this->generateUrl('admin_order_export_pdf'));
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order')));
        /**
         * @var Crawler
         */
        $crawler = $this->client->followRedirect();

        $html = $crawler->filter('.alert')->html();
        $this->assertContains('出荷IDが指定されていません', $html);
    }

    /**
     * Order pdf download.
     *
     * @param string $field
     * @param string $message
     *
     * @dataProvider dataDownloadMaxLengthProvider
     */
    public function testDownloadMaxLength($field, $message)
    {
        $Order = $this->createOrderForSearch();
        $Shippings = $Order->getShippings();
        $shippingId = $Shippings[0]->getId();
        /**
         * @var Client
         */
        $client = $this->client;

        /**
         * @var Crawler
         */
        $crawler = $client->request('POST', $this->generateUrl('admin_order_export_pdf'),
            [
                '_token' => 'dummy',
                'ids' => [$shippingId],
            ]
        );
        $html = $crawler->filter('#order_pdf_form')->html();
        $this->assertContains((string) $shippingId, $html);
        $this->assertContains('お買上げ明細書(納品書)', $html);
        $this->assertContains('このたびはお買上げいただきありがとうございます。', $html);
        $this->assertContains('下記の内容にて納品させていただきます。', $html);
        $this->assertContains('ご確認くださいますよう、お願いいたします。', $html);

        $form = $this->getForm($crawler);
        /**
         * @var Generator
         */
        $faker = $this->getFaker();
        $form["$field"] = $faker->text(1000);
        $crawler = $client->submit($form);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $html = $crawler->filter('#order_pdf_form')->html();
        $this->assertContains($message, $html);
    }

    /**
     * Data provider for max length test.
     *
     * @return array
     */
    public function dataDownloadMaxLengthProvider()
    {
        return [
            ['order_pdf[title]', '値が長すぎます。255文字以内でなければなりません。'],
            ['order_pdf[message1]', '値が長すぎます。30文字以内でなければなりません。'],
            ['order_pdf[message2]', '値が長すぎます。30文字以内でなければなりません。'],
            ['order_pdf[message3]', '値が長すぎます。30文字以内でなければなりません。'],
            ['order_pdf[note1]', '値が長すぎます。255文字以内でなければなりません。'],
            ['order_pdf[note2]', '値が長すぎます。255文字以内でなければなりません。'],
            ['order_pdf[note3]', '値が長すぎます。255文字以内でなければなりません。'],
        ];
    }

    /**
     * Order pdf download.
     */
    public function testDownloadSuccess()
    {
        $Order = $this->createOrderForSearch();
        $Shippings = $Order->getShippings();
        $shippingId = $Shippings[0]->getId();

        /**
         * @var Client
         */
        $client = $this->client;

        /**
         * @var Crawler
         */
        $crawler = $client->request('POST', $this->generateUrl('admin_order_export_pdf'),
            [
                '_token' => 'dummy',
                'ids' => [$shippingId],
            ]);
        $html = $crawler->filter('#order_pdf_form')->html();
        $this->assertContains((string) $shippingId, $html);
        $this->assertContains('お買上げ明細書(納品書)', $html);
        $this->assertContains('このたびはお買上げいただきありがとうございます。', $html);
        $this->assertContains('下記の内容にて納品させていただきます。', $html);
        $this->assertContains('ご確認くださいますよう、お願いいたします。', $html);

        $form = $this->getForm($crawler);
        $client->submit($form);

        $this->actual = $client->getResponse()->headers->get('Content-Type');
        $this->expected = 'application/pdf';
        $this->verify();
    }

    /**
     * Render order pdf download.
     */
    public function testDownloadWithPreviousInputSuccess()
    {
        $Order = $this->createOrderForSearch();
        $Shippings = $Order->getShippings();
        $shippingId = $Shippings[0]->getId();

        /**
         * @var Generator
         */
        $faker = $this->getFaker();
        $adminTest = $this->createMember();

        /**
         * @var Client
         */
        $client = $this->loginTo($adminTest);
        $OrderPdf = new OrderPdf();

        $OrderPdf->setMemberId($adminTest->getId())
            ->setTitle($faker->text(50))
            ->setMessage1($faker->text(30))
            ->setMessage2($faker->text(30))
            ->setMessage3($faker->text(30))
            ->setNote1($faker->text(50))
            ->setNote2($faker->text(50))
            ->setNote3($faker->text(50))
            ->setVisible(Constant::DISABLED);

        $this->entityManager->persist($OrderPdf);
        $this->entityManager->flush($OrderPdf);

        $crawler = $client->request('POST', $this->generateUrl('admin_order_export_pdf'),
            [
                '_token' => 'dummy',
                'ids' => [$shippingId],
            ]
        );
        $html = $crawler->filter('#order_pdf_form')->html();

        $this->assertContains((string) $shippingId, $html);
        $this->assertContains($OrderPdf->getTitle(), $html);
        $this->assertContains($OrderPdf->getMessage1(), $html);
        $this->assertContains($OrderPdf->getMessage2(), $html);
        $this->assertContains($OrderPdf->getMessage3(), $html);
        $this->assertContains($OrderPdf->getNote1(), $html);
        $this->assertContains($OrderPdf->getNote2(), $html);
        $this->assertContains($OrderPdf->getNote3(), $html);

        $form = $this->getForm($crawler);
        $client->submit($form);

        $this->actual = $client->getResponse()->headers->get('Content-Type');
        $this->expected = 'application/pdf';
        $this->verify();
    }

    /**
     * Render order pdf download.
     */
    public function testDownloadWithPreviousInputSuccessWithWeb()
    {
        $Order = $this->createOrderForSearch();
        $Shippings = $Order->getShippings();
        $shippingId = $Shippings[0]->getId();

        $crawler = $this->client->request('POST', $this->generateUrl('admin_order_export_pdf'),
            [
                '_token' => 'dummy',
                'ids' => [$shippingId],
            ]
        );

        /**
         * @var \Symfony\Component\DomCrawler\Form
         */
        $form = $this->getForm($crawler);
        // fields set to empty.
        $form->setValues([
            'order_pdf[title]' => '',
            'order_pdf[message1]' => '',
            'order_pdf[message2]' => '',
            'order_pdf[message3]' => '',
            'order_pdf[note1]' => '',
            'order_pdf[note2]' => '',
            'order_pdf[note3]' => '',
            'order_pdf[default]' => '1',
        ]);

        $this->client->submit($form);

        $this->actual = $this->client->getResponse()->headers->get('Content-Type');
        $this->expected = 'application/pdf';
        $this->verify();

        $OrderPdfs = $this->orderPdfRepository->findAll();
        $this->assertCount(1, $OrderPdfs, '1件保存されているはず');

        $OrderPdf = current($OrderPdfs);
        $token = self::$container->get('security.token_storage')->getToken();
        $adminTest = $token->getUser();
        $this->assertEquals($adminTest->getId(), $OrderPdf->getMemberId(), '管理ユーザーのIDと一致するはず');

        $this->assertNull($OrderPdf->getTitle());
        $this->assertNull($OrderPdf->getMessage1());
        $this->assertNull($OrderPdf->getMessage2());
        $this->assertNull($OrderPdf->getMessage3());
        $this->assertNull($OrderPdf->getNote1());
        $this->assertNull($OrderPdf->getNote2());
        $this->assertNull($OrderPdf->getNote3());
    }

    /**
     * @param Crawler $crawler
     *
     * @return \Symfony\Component\DomCrawler\Form
     */
    private function getForm(Crawler $crawler)
    {
        $form = $crawler->selectButton('作成')->form();
        $form['order_pdf[_token]'] = 'dummy';

        return $form;
    }

    /**
     * Create order data for search function.
     *
     * @return Order
     */
    private function createOrderForSearch()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Status = $this->orderStatusRepo->find(OrderStatus::DELIVERED);
        // Update order_date to show on search
        $this->orderRepo->changeStatus($Order->getId(), $Status);

        return $Order;
    }
}
