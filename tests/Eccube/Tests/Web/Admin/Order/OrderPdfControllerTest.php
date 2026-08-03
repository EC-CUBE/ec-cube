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

use Eccube\Common\Constant;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\OrderPdf;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\OrderPdfRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Faker\Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class OrderPdfControllerTest.
 */
final class OrderPdfControllerTest extends AbstractAdminWebTestCase
{
    protected ?OrderStatusRepository $orderStatusRepo = null;

    protected ?OrderRepository $orderRepo = null;

    protected ?OrderPdfRepository $orderPdfRepository = null;

    protected ?EccubeConfig $config = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderStatusRepo = $this->entityManager->getRepository(OrderStatus::class);
        $this->orderRepo = $this->entityManager->getRepository(Order::class);
        $this->orderPdfRepository = $this->entityManager->getRepository(OrderPdf::class);
        $this->config = $this->eccubeConfig;
    }

    /**
     * testRoutingOrderExportPdf test
     */
    public function testRoutingOrderExportPdf()
    {
        $Order = $this->createOrderForSearch();

        $this->client->request(Request::METHOD_POST,
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
            Request::METHOD_GET,
            $this->generateUrl('admin_order')
        );

        $this->assertStringContainsString((string) $shippingId, $crawler->filter('#search_result')->html());

        $expectedText = '納品書出力';
        $actualNode = $crawler->filter('.btn-bulk-wrapper')->html();
        $this->assertStringContainsString($expectedText, $actualNode);
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
        $crawler = $this->client->request(Request::METHOD_POST,
            $this->generateUrl('admin_order_export_pdf'),
            [
                '_token' => 'dummy',
                'ids' => [$shippingId],
            ]
        );
        $html = $crawler->filter('#order_pdf_form')->html();
        $this->assertStringContainsString((string) $shippingId, $html);
        $this->assertStringContainsString('お買上げ明細書(納品書)', $html);
        $this->assertStringContainsString('このたびはお買上げいただきありがとうございます。', $html);
        $this->assertStringContainsString('下記の内容にて納品させていただきます。', $html);
        $this->assertStringContainsString('ご確認くださいますよう、お願いいたします。', $html);
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
        $crawler = $this->client->request(Request::METHOD_POST,
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

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_order_export_pdf'),
            [
                '_token' => 'dummy',
                'ids' => [$shippingId],
            ]);
        $html = $crawler->filter('#order_pdf_form')->html();

        $this->assertStringContainsString((string) $shippingId, $html);

        $this->assertStringContainsString($form['order_pdf[title]']->getValue(), $html);
        $this->assertStringContainsString($form['order_pdf[message1]']->getValue(), $html);
        $this->assertStringContainsString($form['order_pdf[message2]']->getValue(), $html);
        $this->assertStringContainsString($form['order_pdf[message3]']->getValue(), $html);
        $this->assertStringContainsString($form['order_pdf[note1]']->getValue(), $html);
        $this->assertStringContainsString($form['order_pdf[note2]']->getValue(), $html);
        $this->assertStringContainsString($form['order_pdf[note3]']->getValue(), $html);
    }

    /**
     * Order pdf download.
     */
    public function testDownloadIdInvalid()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_order_export_pdf'));
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order')));
        /**
         * @var Crawler
         */
        $crawler = $this->client->followRedirect();

        $html = $crawler->filter('.alert')->html();
        $this->assertStringContainsString('出荷IDが指定されていません', $html);
    }

    /**
     * Order pdf download.
     *
     * @param string $field
     * @param string $message
     */
    #[DataProvider(methodName: 'dataDownloadMaxLengthProvider')]
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
        $crawler = $client->request(Request::METHOD_POST, $this->generateUrl('admin_order_export_pdf'),
            [
                '_token' => 'dummy',
                'ids' => [$shippingId],
            ]
        );
        $html = $crawler->filter('#order_pdf_form')->html();
        $this->assertStringContainsString((string) $shippingId, $html);
        $this->assertStringContainsString('お買上げ明細書(納品書)', $html);
        $this->assertStringContainsString('このたびはお買上げいただきありがとうございます。', $html);
        $this->assertStringContainsString('下記の内容にて納品させていただきます。', $html);
        $this->assertStringContainsString('ご確認くださいますよう、お願いいたします。', $html);

        $form = $this->getForm($crawler);
        /**
         * @var Generator
         */
        $faker = $this->getFaker();
        $form["$field"] = $faker->text(1000);
        $crawler = $client->submit($form);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $html = $crawler->filter('#order_pdf_form')->html();
        $this->assertStringContainsString($message, (string) $html);
    }

    /**
     * Data provider for max length test.
     */
    public static function dataDownloadMaxLengthProvider(): \Iterator
    {
        yield ['order_pdf[title]', '長すぎます。この値は255文字以下で入力してください。'];
        yield ['order_pdf[message1]', '長すぎます。この値は30文字以下で入力してください。'];
        yield ['order_pdf[message2]', '長すぎます。この値は30文字以下で入力してください。'];
        yield ['order_pdf[message3]', '長すぎます。この値は30文字以下で入力してください。'];
        yield ['order_pdf[note1]', '長すぎます。この値は255文字以下で入力してください。'];
        yield ['order_pdf[note2]', '長すぎます。この値は255文字以下で入力してください。'];
        yield ['order_pdf[note3]', '長すぎます。この値は255文字以下で入力してください。'];
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
        $crawler = $client->request(Request::METHOD_POST, $this->generateUrl('admin_order_export_pdf'),
            [
                '_token' => 'dummy',
                'ids' => [$shippingId],
            ]);
        $html = $crawler->filter('#order_pdf_form')->html();
        $this->assertStringContainsString((string) $shippingId, $html);
        $this->assertStringContainsString('お買上げ明細書(納品書)', $html);
        $this->assertStringContainsString('このたびはお買上げいただきありがとうございます。', $html);
        $this->assertStringContainsString('下記の内容にて納品させていただきます。', $html);
        $this->assertStringContainsString('ご確認くださいますよう、お願いいたします。', $html);

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
            ->setVisible((bool) Constant::DISABLED)
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime());

        $this->entityManager->persist($OrderPdf);
        $this->entityManager->flush($OrderPdf);

        $crawler = $client->request(Request::METHOD_POST, $this->generateUrl('admin_order_export_pdf'),
            [
                '_token' => 'dummy',
                'ids' => [$shippingId],
            ]
        );
        $html = $crawler->filter('#order_pdf_form')->html();

        $this->assertStringContainsString((string) $shippingId, (string) $html);
        $this->assertStringContainsString($OrderPdf->getTitle(), (string) $html);
        $this->assertStringContainsString($OrderPdf->getMessage1(), (string) $html);
        $this->assertStringContainsString($OrderPdf->getMessage2(), (string) $html);
        $this->assertStringContainsString($OrderPdf->getMessage3(), (string) $html);
        $this->assertStringContainsString($OrderPdf->getNote1(), (string) $html);
        $this->assertStringContainsString($OrderPdf->getNote2(), (string) $html);
        $this->assertStringContainsString($OrderPdf->getNote3(), (string) $html);

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

        $crawler = $this->client->request(Request::METHOD_POST, $this->generateUrl('admin_order_export_pdf'),
            [
                '_token' => 'dummy',
                'ids' => [$shippingId],
            ]
        );

        /**
         * @var Form
         */
        $form = $this->getForm($crawler);
        // fields set to empty.
        $form->setValues([
            'order_pdf[title]' => 'title',
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
        $token = static::getContainer()->get(TokenStorageInterface::class)->getToken();
        $adminTest = $token->getUser();
        $this->assertEquals($adminTest->getId(), $OrderPdf->getMemberId(), '管理ユーザーのIDと一致するはず');

        $this->assertSame('title', $OrderPdf->getTitle());
        $this->assertNull($OrderPdf->getMessage1());
        $this->assertNull($OrderPdf->getMessage2());
        $this->assertNull($OrderPdf->getMessage3());
        $this->assertNull($OrderPdf->getNote1());
        $this->assertNull($OrderPdf->getNote2());
        $this->assertNull($OrderPdf->getNote3());
    }

    /**
     * 納品書PDFの出力項目トグルを全て ON にした状態でも PDF が生成できること (#6197).
     * 行数が最大になるため, 店舗情報欄が合計金額欄を侵食しないことの回帰防止.
     */
    public function testDownloadSuccessWithAllOrderPdfItemsVisible()
    {
        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->find(1);
        $this->assertInstanceOf(BaseInfo::class, $BaseInfo);

        // EccubeTestCase はトランザクションを張らないため、書き換えた値は後続テストへ残る。
        // 変更前の値を退避し、アサーション失敗時も finally で必ず戻す。
        $original = [
            'shopName' => $BaseInfo->isOrderPdfVisibleShopName(),
            'shopNameEng' => $BaseInfo->isOrderPdfVisibleShopNameEng(),
            'address' => $BaseInfo->isOrderPdfVisibleAddress(),
            'companyNameVisible' => $BaseInfo->isOrderPdfVisibleCompanyName(),
            'phoneNumber' => $BaseInfo->isOrderPdfVisiblePhoneNumber(),
            'businessHourVisible' => $BaseInfo->isOrderPdfVisibleBusinessHour(),
            'email' => $BaseInfo->isOrderPdfVisibleEmail(),
            'invoiceNumber' => $BaseInfo->isOrderPdfVisibleInvoiceNumber(),
            'companyName' => $BaseInfo->getCompanyName(),
            'businessHour' => $BaseInfo->getBusinessHour(),
        ];

        try {
            $BaseInfo->setOrderPdfVisibleShopName(true)
                ->setOrderPdfVisibleShopNameEng(true)
                ->setOrderPdfVisibleAddress(true)
                ->setOrderPdfVisibleCompanyName(true)
                ->setOrderPdfVisiblePhoneNumber(true)
                ->setOrderPdfVisibleBusinessHour(true)
                ->setOrderPdfVisibleEmail(true)
                ->setOrderPdfVisibleInvoiceNumber(true)
                ->setCompanyName('テスト株式会社')
                ->setBusinessHour('10:00-19:00');
            $this->entityManager->flush();

            $Order = $this->createOrderForSearch();
            $Shippings = $Order->getShippings();
            $shippingId = $Shippings[0]->getId();

            $client = $this->client;
            $crawler = $client->request(Request::METHOD_POST, $this->generateUrl('admin_order_export_pdf'),
                [
                    '_token' => 'dummy',
                    'ids' => [$shippingId],
                ]);

            $form = $this->getForm($crawler);
            $client->submit($form);

            $this->actual = $client->getResponse()->headers->get('Content-Type');
            $this->expected = 'application/pdf';
            $this->verify();
        } finally {
            $BaseInfo->setOrderPdfVisibleShopName($original['shopName'])
                ->setOrderPdfVisibleShopNameEng($original['shopNameEng'])
                ->setOrderPdfVisibleAddress($original['address'])
                ->setOrderPdfVisibleCompanyName($original['companyNameVisible'])
                ->setOrderPdfVisiblePhoneNumber($original['phoneNumber'])
                ->setOrderPdfVisibleBusinessHour($original['businessHourVisible'])
                ->setOrderPdfVisibleEmail($original['email'])
                ->setOrderPdfVisibleInvoiceNumber($original['invoiceNumber'])
                ->setCompanyName($original['companyName'])
                ->setBusinessHour($original['businessHour']);
            $this->entityManager->flush();
        }
    }

    private function getForm(Crawler $crawler): Form
    {
        $form = $crawler->selectButton('作成')->form();
        $form['order_pdf[_token]'] = 'dummy';

        return $form;
    }

    /**
     * Create order data for search function.
     */
    private function createOrderForSearch(): Order
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Status = $this->orderStatusRepo->find(OrderStatus::DELIVERED);
        // Update order_date to show on search
        $this->orderRepo->changeStatus($Order->getId(), $Status);

        return $Order;
    }
}
