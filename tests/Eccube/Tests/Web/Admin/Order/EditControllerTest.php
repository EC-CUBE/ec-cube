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

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\Delivery;
use Eccube\Entity\MailHistory;
use Eccube\Entity\Master\Job;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\Master\Sex;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\Shipping;
use Eccube\Entity\TaxRule;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Service\TaxRuleService;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditControllerTest extends AbstractEditControllerTestCase
{
    protected ?Customer $Customer = null;

    protected ?Order $Order = null;

    protected ?Product $Product = null;

    protected ?OrderRepository $orderRepository = null;

    protected ?CustomerRepository $customerRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->Product = $this->createProduct();
        $this->customerRepository = $this->entityManager->getRepository(Customer::class);
        $this->orderRepository = $this->entityManager->getRepository(Order::class);
        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $this->entityManager->flush($BaseInfo);
    }

    public function testRoutingAdminOrderNew()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_order_new'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminOrderNewPost()
    {
        $formData = $this->createFormData($this->Customer, $this->Product);
        unset($formData['OrderStatus']);
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_new'),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );

        $url = $crawler->filter('a')->text();
        $this->assertTrue($this->client->getResponse()->isRedirect($url));

        // pre_order_id がセットされているか確認
        /** @var Order[] $Orders */
        $Orders = $this->orderRepository->findBy([], ['create_date' => 'DESC']);
        $this->assertNotNull($Orders[0]->getPreOrderId());
    }

    public function testRoutingAdminOrderEdit()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminOrderEditPost()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush($Order);

        $formData = $this->createFormData($Customer, $this->Product);
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());
        $this->expected = $formData['name']['name01'];
        $this->actual = $EditedOrder->getName01();
        $this->verify();

        // TODO
        // 顧客の購入回数と購入金額確認
        // $this->expected =  $EditedOrder->getPaymentTotal();
        // $this->actual = $EditedOrder->getCustomer()->getBuyTotal();
        // $this->verify();
        // $this->expected = 1;
        // $this->actual = $EditedOrder->getCustomer()->getBuyTimes();
        // $this->verify();
    }

    public function testNotUpdateLastBuyDate()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush($Order);

        $formData = $this->createFormData($Customer, $this->Product);
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));
        $EditedCustomer = $this->customerRepository->find($Customer->getId());
        $this->expected = $Customer->getLastBuyDate();
        $this->assertInstanceOf(Customer::class, $EditedCustomer);
        $this->actual = $EditedCustomer->getLastBuyDate();
        $this->verify();
    }

    /**
     * 受注編集画面で入金日を手動で編集できることを確認するテスト.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6528
     */
    public function testEditPaymentDate()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush($Order);

        $formData = $this->createFormData($Customer, $this->Product);
        // ステータス遷移を発生させず入金日の編集のみを検証する.
        $formData['OrderStatus'] = OrderStatus::NEW;
        $formData['payment_date'] = '2021-05-06T07:08:09';

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());
        // タイムゾーン表現に依存せず, 指し示す時刻(instant)が一致することを確認する.
        $this->expected = (new \DateTime('2021-05-06T07:08:09'))->getTimestamp();
        $this->assertInstanceOf(Order::class, $EditedOrder);
        $this->actual = $EditedOrder->getPaymentDate()->getTimestamp();
        $this->verify();
    }

    /**
     * 受注編集画面で出荷日を手動で編集できることを確認するテスト.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6528
     */
    public function testEditShippingDate()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush($Order);

        $formData = $this->createFormData($Customer, $this->Product);
        $formData['OrderStatus'] = OrderStatus::NEW;
        $formData['Shipping']['shipping_date'] = '2021-05-06T07:08:09';

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());
        // タイムゾーン表現に依存せず, 指し示す時刻(instant)が一致することを確認する.
        $this->expected = (new \DateTime('2021-05-06T07:08:09'))->getTimestamp();
        $this->assertInstanceOf(Order::class, $EditedOrder);
        $this->actual = $EditedOrder->getShippings()->first()->getShippingDate()->getTimestamp();
        $this->verify();
    }

    /**
     * 手動で入金日を設定しつつ入金済みへ遷移させても, 手動値が自動セットで上書きされないことを確認するテスト.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6528
     */
    public function testEditPaymentDateNotOverwrittenOnPayTransition()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush($Order);

        $formData = $this->createFormData($Customer, $this->Product);
        // 入金済みへ遷移させつつ, 入金日を手動で設定する.
        $formData['OrderStatus'] = OrderStatus::PAID;
        $formData['payment_date'] = '2021-05-06T07:08:09';

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());
        $this->assertInstanceOf(Order::class, $EditedOrder);
        // 入金済みに遷移していること.
        $this->assertSame(OrderStatus::PAID, $EditedOrder->getOrderStatus()->getId());
        // 手動で設定した入金日が, 遷移時の自動セット(現在日時)で上書きされていないこと.
        $this->expected = (new \DateTime('2021-05-06T07:08:09'))->getTimestamp();
        $this->actual = $EditedOrder->getPaymentDate()->getTimestamp();
        $this->verify();
    }

    /**
     * 危険なXSS htmlインジェクションが削除されたことを確認するテスト
     *
     * 下記のものをチェックします。
     *     ・ ID属性の追加
     *     ・ <script> スクリプトインジェクション
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/5372
     */
    public function testOrderMailXSSAttackPrevention(): void
    {
        // Create a new news item for the homepage with a XSS attack (via <script> AND id attribute injection)
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $MailHistory = (new MailHistory())->setMailHtmlBody(
            "<div id='dangerous-id' class='safe_to_use_class'>
                    <p>メール内容＃１</p>
                    <script>alert('XSS Attack')</script>
                    <a href='https://www.google.com'>safe html</a>
                </div>"
        )
            ->setOrder($Order)
            ->setMailSubject('テスト')
            ->setMailBody('テスト内容')
            ->setSendDate(new \DateTime())
            ->setCreator($this->createMember());
        $this->entityManager->persist($MailHistory);
        $this->entityManager->flush($MailHistory);
        $this->entityManager->refresh($Order);

        // 1つの新着情報を保存した後にホームページにアクセスする。
        // Request Homepage after saving a single news item
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]));
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());

        // <div>タグから危険なid属性が削除されていることを確認する。
        // Find that dangerous id attributes are removed from <div> tags.
        $testNewsArea_notFoundTest = $crawler->filter('#dangerous-id');
        $this->assertCount(0, $testNewsArea_notFoundTest);

        // 安全なclass属性が出力されているかどうかを確認する。
        // Find if classes (which are safe) have been outputted
        $testNewsArea = $crawler->filter('.safe_to_use_class');
        $this->assertCount(1, $testNewsArea);

        // 安全なHTMLが存在するかどうかを確認する
        // Find if the safe HTML exists
        $this->assertStringContainsString('<p>メール内容＃１</p>', $testNewsArea->outerHtml());
        $this->assertStringContainsString('<a href="https://www.google.com">safe html</a>', $testNewsArea->outerHtml());

        // 安全でないスクリプトが存在しないかどうかを確認する
        // Find if the unsafe script does not exist
        $this->assertStringNotContainsString("<script>alert('XSS Attack')</script>", $testNewsArea->outerHtml());
    }

    #[Group(name: 'decimal')]
    public function testOrderCustomerInfo()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush($Order);

        $formData = $this->createFormData($Customer, $this->Product);
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());
        $this->assertInstanceOf(Order::class, $EditedOrder);
        $EditedCustomer = $this->customerRepository->find($EditedOrder->getCustomer()->getId());
        // decimal の値を反映させるために refresh する
        $this->entityManager->refresh($EditedOrder);
        $this->entityManager->refresh($EditedCustomer);
        $this->assertInstanceOf(Order::class, $EditedOrder);

        // 顧客の購入回数と購入金額確認
        $totalPrice = $EditedOrder->getPaymentTotal();

        $this->expected = $totalPrice;
        $this->actual = $EditedOrder->getCustomer()->getBuyTotal();
        $this->verify();
        $this->expected = '1';
        $this->actual = $EditedOrder->getCustomer()->getBuyTimes();
        $this->verify();

        $Order = $this->createOrder($Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush();

        $formData = $this->createFormData($Customer, $this->Product);
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());
        $this->assertInstanceOf(Order::class, $EditedOrder);

        // 顧客の購入回数と購入金額確認
        $this->expected = bcadd($totalPrice, $EditedOrder->getPaymentTotal(), 2);
        // XXX SQLite の場合、小数点以下の '.00' が省略されるため、bcadd() で正規化して比較する
        $this->actual = bcadd((string) $EditedOrder->getCustomer()->getBuyTotal(), '0', 2);
        $this->verify();
        $this->expected = '2';
        $this->actual = $EditedOrder->getCustomer()->getBuyTimes();
        $this->verify();
    }

    public function testSearchCustomerHtml()
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_search_customer_html'),
            [
                'search_word' => $this->Customer->getId(),
            ],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testSearchCustomerById()
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_search_customer_by_id'),
            [
                'id' => $this->Customer->getId(),
            ],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $Result = json_decode($this->client->getResponse()->getContent(), true);

        $this->expected = $this->Customer->getName01();
        $this->actual = $Result['name01'];
        $this->verify();
    }

    public function testSearchProduct()
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_search_product'),
            [
                'id' => $this->Product->getId(),
            ],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * 管理画面から購入処理中(PROCESSING)で受注登録できるテスト.
     *
     * 元々は #1452 の確認としてフロントの購入確認画面までを検証していたが,
     * その部分は廃止された CartService::lock() に依存していたため削除した.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1452
     */
    public function testOrderProcessingRegister()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $formData = $this->createFormData($Customer, $this->Product);
        $formData['OrderStatus'] = OrderStatus::PROCESSING; // 購入処理中で受注を登録する
        // 管理画面から受注登録
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());
        $this->expected = $formData['OrderStatus'];
        $this->assertInstanceOf(Order::class, $EditedOrder);
        $this->actual = $EditedOrder->getOrderStatus()->getId();
        $this->verify();
    }

    /**
     * 受注編集時に、dtb_order.taxの値が正しく保存されているかどうかのテスト
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1606
     */
    #[Group(name: 'decimal')]
    public function testOrderProcessingWithTax()
    {
        $this->markTestSkipped('インボイス対応に伴い Order::tax が非推奨となったためスキップ');
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush($Order);

        $formData = $this->createFormData($Customer, $this->Product);

        // 管理画面から受注登録
        $this->client->request(
            Request::METHOD_POST, $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]), [
                'order' => $formData,
                'mode' => 'register',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());
        $formDataForEdit = $this->createFormDataForEdit($EditedOrder);

        $addingQuantity = 3;
        foreach ($formDataForEdit['OrderItems'] as $index => $orderItem) {
            // 商品数変更3個追加
            $formDataForEdit['OrderItems'][$index]['quantity'] = $orderItem['quantity'] + $addingQuantity;
        }

        // 管理画面で受注編集する
        $this->client->request(
            Request::METHOD_POST, $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]), [
                'order' => $formDataForEdit,
                'mode' => 'register',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));
        $EditedOrderafterEdit = $this->orderRepository->find($Order->getId());

        // 税金計算
        $taxableItem = array_filter($EditedOrder->getOrderItems()->toArray(), fn ($OrderItem) => !is_null($OrderItem->getTaxType()) && $OrderItem->getTaxType()->getId() === TaxType::TAXATION);
        $totalTaxByTaxRate = [];
        $totalByTaxRate = [];
        foreach ($taxableItem as $OrderItem) {
            $totalPrice = $OrderItem->getPriceIncTax() * ($OrderItem->getQuantity() + $addingQuantity);
            $taxRate = $OrderItem->getTaxRate();
            $totalByTaxRate[$taxRate] = isset($totalByTaxRate[$taxRate])
                ? $totalByTaxRate[$taxRate] + $totalPrice
                : $totalPrice;
        }
        foreach ($totalByTaxRate as $rate => $price) {
            $taxValue = bcdiv(bcmul((string) $price, (string) $rate, 4), bcadd('100', (string) $rate, 0), 4);
            $tax = static::getContainer()->get(TaxRuleService::class)
                ->roundByRoundingType($taxValue, RoundingType::ROUND);
            $totalTaxByTaxRate[$rate] = $tax;
        }
        $totalTax = array_reduce($totalTaxByTaxRate, fn ($sum, $tax) => bcadd($sum, (string) $tax, 2), '0');

        // 確認する「トータル税金」
        $this->expected = $totalTax;
        // XXX SQLite の場合、小数点以下の '.00' が省略されるため、bcadd() で正規化して比較する
        $this->actual = bcadd((string) $EditedOrderafterEdit->getTax(), '0', 2);
        $this->verify();
    }

    /**
     * 受注登録時に会員情報が正しく保存されているかどうかのテスト
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1682
     */
    public function testOrderProcessingWithCustomer()
    {
        $formData = $this->createFormData($this->Customer, $this->Product);
        unset($formData['OrderStatus']);
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_new'),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );

        $url = $crawler->filter('a')->text();
        $this->assertTrue($this->client->getResponse()->isRedirect($url));

        $savedOderId = preg_replace('/.*\/admin\/order\/(\d+)\/edit/', '$1', $url);
        $SavedOrder = $this->orderRepository->find($savedOderId);

        $this->assertInstanceOf(Order::class, $SavedOrder);
        $this->expected = $this->Customer->getSex();
        $this->actual = $SavedOrder->getSex();
        $this->verify('会員の性別が保存されている');

        $this->expected = $this->Customer->getJob();
        $this->actual = $SavedOrder->getJob();
        $this->verify('会員の職業が保存されている');

        $this->expected = $this->Customer->getBirth();
        $this->actual = $SavedOrder->getBirth();
        $this->verify('会員の誕生日が保存されている');
    }

    public function testMailNoRFC()
    {
        $formData = $this->createFormData($this->Customer, $this->Product);
        // RFCに準拠していないメールアドレスを設定
        $formData['email'] = 'aa..@example.com';

        unset($formData['OrderStatus']);
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_new'),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );

        $url = $crawler->filter('a')->text();
        $this->assertTrue($this->client->getResponse()->isRedirect($url));

        $savedOderId = preg_replace('/.*\/admin\/order\/(\d+)\/edit/', '$1', $url);
        $SavedOrder = $this->orderRepository->find($savedOderId);

        $this->assertInstanceOf(Order::class, $SavedOrder);
        $this->expected = $SavedOrder->getEmail();
        $this->actual = $formData['email'];
        $this->verify();
    }

    /**
     * お届け時間の指定を「指定なし」に変更できるかのテスト
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/4143
     */
    public function testUpdateShippingDeliveryTimeToNoneSpecified()
    {
        $this->createCustomer();
        $Order = $this->createOrder($this->Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush($Order);

        $formData = $this->createFormData($this->Customer, $this->Product);
        // まずお届け時間に何か指定する(便宜上、最初に取得できたものを利用)
        $Delivery = $this->entityManager->getRepository(Delivery::class)->find($formData['Shipping']['Delivery']);
        $this->assertInstanceOf(Delivery::class, $Delivery);
        $DeliveryTime = $Delivery->getDeliveryTimes()[0];
        $delivery_time_id = $DeliveryTime->getId();
        $delivery_time = $DeliveryTime->getDeliveryTime();
        $formData['Shipping']['DeliveryTime'] = $delivery_time_id;

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());
        $this->assertInstanceOf(Order::class, $EditedOrder);
        $EditedShipping = $EditedOrder->getShippings()[0];

        $this->expected = $delivery_time_id;
        $this->actual = $EditedShipping->getTimeId();
        $this->verify();
        $this->expected = $delivery_time;
        $this->actual = $EditedShipping->getShippingDeliveryTime();
        $this->verify();

        $formDataForEdit = $this->createFormDataForEdit($EditedOrder);
        // 「指定なし」に変更
        $formDataForEdit['Shipping']['DeliveryTime'] = null;

        // 管理画面で受注編集する
        $this->client->request(
            Request::METHOD_POST, $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]), [
                'order' => $formDataForEdit,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrderafterEdit = $this->orderRepository->find($Order->getId());
        $this->assertInstanceOf(Order::class, $EditedOrderafterEdit);
        $EditedShippingafterEdit = $EditedOrderafterEdit->getShippings()[0];

        $this->expected = null;
        $this->actual = $EditedShippingafterEdit->getTimeId();
        $this->verify();
        $this->expected = null;
        $this->actual = $EditedShippingafterEdit->getShippingDeliveryTime();
        $this->verify();
    }

    /**
     * 受注管理で税率を変更できる
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/4269
     */
    #[Group(name: 'decimal')]
    public function testChangeOrderItemTaxRate()
    {
        /** @var RoundingType $RoundingType */
        $RoundingType = $this->entityManager->find(RoundingType::class, RoundingType::ROUND);
        /** @var Product $Product */
        $Product = $this->createProduct(null, 1);
        $this->entityManager->persist($Product);

        /** @var ProductClass $ProductClass */
        $ProductClass = $this->Product->getProductClasses()[0];
        $ProductClass->setPrice02('1000');
        $this->entityManager->persist($ProductClass);

        $TaxRule = new TaxRule();
        $TaxRule->setTaxRate('8')
            ->setTaxAdjust('0')
            ->setRoundingType($RoundingType)
            ->setProduct($Product)
            ->setProductClass($ProductClass)
            ->setApplyDate(new \DateTime('yesterday'))
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime());
        $this->entityManager->persist($TaxRule);

        $this->entityManager->flush();

        $formData = $this->createFormData($this->Customer, $this->Product);
        unset($formData['OrderStatus']);

        // 商品の税率を10%に変更
        $formData['OrderItems'][0]['tax_rate'] = '10';

        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_new'),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );

        $url = $crawler->filter('a')->text();
        $this->assertTrue($this->client->getResponse()->isRedirect($url));

        // 税率が10%で登録されている
        /** @var Order $Order */
        $Order = $this->orderRepository->findBy([], ['create_date' => 'DESC'])[0];
        $this->assertSame('10', $Order->getProductOrderItems()[0]->getTaxRate());
        $this->assertSame('100.00', $Order->getProductOrderItems()[0]->getTax());
    }

    public function testRoutingAdminOrderEditPostWithCustomerInfo()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $Order->setSex();
        $Order->setJob();
        $Order->setBirth();

        $this->entityManager->flush($Order);

        $Customer->setSex($this->entityManager->find(Sex::class, 1));
        $Customer->setJob($this->entityManager->find(Job::class, 1));
        $Customer->setBirth(new \DateTime());
        $this->entityManager->flush($Customer);

        $formData = $this->createFormData($Customer, $this->Product);
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());
        $this->assertInstanceOf(Order::class, $EditedOrder);
        $this->assertNotInstanceOf(Sex::class, $EditedOrder->getSex());
        $this->assertNotInstanceOf(Job::class, $EditedOrder->getJob());
        $this->assertNotInstanceOf(\DateTime::class, $EditedOrder->getBirth());
    }

    /**
     * 受注登録時にその他明細(初期の価格0円)をゼロ除算なしに正しく追加できるかどうかのテスト
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/5533
     */
    public function testAddOrderItemOrderWithoutZeroDivision()
    {
        $Product = null;
        $charge = 0;
        $formData = $this->createFormData($this->Customer, $Product, $charge);
        unset($formData['OrderStatus']);
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_new'),
            [
                'order' => $formData,
                'mode' => '',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * 明細の最終行を削除し保存せずに商品を追加したとき, 削除した明細(送料等)の
     * 税表示区分が商品明細に引き継がれないことのテスト.
     *
     * 未保存の削除により DB から再読込された既存 OrderItem のスロットが再利用され,
     * 新しい商品明細のデータがバインドされる状況を再現する.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6444
     */
    public function testOrderItemTaxDisplayTypeNotCarriedOverWhenFeeSlotReusedByProduct()
    {
        $Order = $this->createOrder($this->Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush();
        $orderId = $Order->getId();

        $ProductClass = $this->Product->getProductClasses()[0];
        $productData = [
            'ProductClass' => $ProductClass->getId(),
            'price' => $ProductClass->getPrice02(),
            'quantity' => 1,
            'product_name' => $this->Product->getName(),
            'order_item_type' => OrderItemType::PRODUCT,
            'tax_type' => TaxType::TAXATION,
            'tax_rate' => '10',
        ];

        $EditOrder = $this->orderRepository->find($orderId);
        $formData = $this->createFormDataForEdit($EditOrder);
        $this->fillTaxType($formData, $EditOrder);

        // 送料明細(税込)のスロットを商品明細のデータで上書きする.
        $replaced = $this->replaceOrderItemSlot($formData, OrderItemType::DELIVERY_FEE, $productData);
        $this->assertGreaterThan(0, $replaced, '送料明細のスロットが見つからなかった');

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_edit', ['id' => $orderId]),
            ['order' => $formData, 'mode' => 'register']
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $orderId])));

        $EditedOrder = $this->orderRepository->find($orderId);
        $ProductOrderItems = $EditedOrder->getProductOrderItems();
        $this->assertNotEmpty($ProductOrderItems);
        $productCodes = [];
        foreach ($ProductOrderItems as $ProductItem) {
            // 商品明細は必ず税抜(EXCLUDED). 旧送料明細の税込(INCLUDED)が引き継がれていないこと.
            $this->assertSame(
                TaxDisplayType::EXCLUDED,
                $ProductItem->getTaxDisplayType()->getId(),
                '商品明細の税表示区分に旧明細の値が引き継がれている'
            );
            $productCodes[] = $ProductItem->getProductCode();
        }
        // 送料スロットに追加した商品には, 自身の ProductClass の商品コードが設定されていること.
        $this->assertContains($ProductClass->getCode(), $productCodes);
    }

    /**
     * 明細の最終行(商品)を削除し保存せずにその他明細を追加したとき, 削除した商品明細の
     * 規格情報(商品コード・規格名)がその他明細に引き継がれないことのテスト.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6444
     */
    public function testOrderItemProductSpecNotCarriedOverWhenProductSlotReusedByFee()
    {
        // 商品を1つ残すため, 2つの規格で受注を作成する.
        $ProductClasses = $this->Product->getProductClasses();
        $Order = $this->createOrderWithProductClasses($this->Customer, [$ProductClasses[0], $ProductClasses[1]]);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush();
        $orderId = $Order->getId();

        $chargeData = [
            'ProductClass' => null,
            'price' => 100,
            'quantity' => 1,
            'product_name' => '手数料',
            'order_item_type' => OrderItemType::CHARGE,
            'tax_type' => TaxType::TAXATION,
            'tax_rate' => '10',
        ];

        $EditOrder = $this->orderRepository->find($orderId);
        $formData = $this->createFormDataForEdit($EditOrder);
        $this->fillTaxType($formData, $EditOrder);

        // 商品明細のスロット1つを手数料明細のデータで上書きする(商品は1つ残る).
        $replaced = $this->replaceOrderItemSlot($formData, OrderItemType::PRODUCT, $chargeData, 1);
        $this->assertSame(1, $replaced, '商品明細のスロットが見つからなかった');

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_edit', ['id' => $orderId]),
            ['order' => $formData, 'mode' => 'register']
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $orderId])));

        $EditedOrder = $this->orderRepository->find($orderId);
        foreach ($EditedOrder->getOrderItems() as $OrderItem) {
            if ($OrderItem->getOrderItemType()->getId() === OrderItemType::PRODUCT) {
                continue;
            }
            // 非商品明細に旧商品明細の規格情報が引き継がれていないこと.
            $this->assertNull($OrderItem->getProductCode(), '非商品明細に商品コードが引き継がれている');
            $this->assertNull($OrderItem->getClassName1(), '非商品明細に規格名が引き継がれている');
            $this->assertNotInstanceOf(Product::class, $OrderItem->getProduct(), '非商品明細に商品が引き継がれている');

            // 送料・手数料は必ず税込(INCLUDED). 旧商品明細の税抜(EXCLUDED)が引き継がれていないこと.
            if (in_array($OrderItem->getOrderItemType()->getId(), [OrderItemType::DELIVERY_FEE, OrderItemType::CHARGE], true)) {
                $this->assertSame(
                    TaxDisplayType::INCLUDED,
                    $OrderItem->getTaxDisplayType()->getId(),
                    '送料・手数料の税表示区分に旧商品明細の税抜が引き継がれている'
                );
            }
        }
    }

    /**
     * 明細の最終行(値引き)を削除し保存せずに商品を追加したとき, 追加した商品明細が
     * Shipping に紐づく(shipping_id が NULL にならない)ことのテスト.
     *
     * 値引き・手数料明細は shipping_id が NULL のため, これらのスロットが商品明細に
     * 再利用されると, associateOrderAndShipping が id 有りの明細をスキップしていた旧実装では
     * 商品が Shipping に紐づかず, 納品書・出荷完了メール・出荷登録画面・マイページ履歴から
     * 商品が消えてしまっていた. 既定の受注構成でも最終行は値引きのため発生する.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6444
     */
    public function testOrderItemShippingNotOrphanedWhenDiscountSlotReusedByProduct()
    {
        $Order = $this->createOrder($this->Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush();
        $orderId = $Order->getId();

        $ProductClass = $this->Product->getProductClasses()[0];
        $productData = [
            'ProductClass' => $ProductClass->getId(),
            'price' => $ProductClass->getPrice02(),
            'quantity' => 1,
            'product_name' => $this->Product->getName(),
            'order_item_type' => OrderItemType::PRODUCT,
            'tax_type' => TaxType::TAXATION,
            'tax_rate' => '10',
        ];

        $EditOrder = $this->orderRepository->find($orderId);
        $formData = $this->createFormDataForEdit($EditOrder);
        $this->fillTaxType($formData, $EditOrder);

        // 値引き明細(shipping_id=NULL)のスロットを商品明細のデータで上書きする.
        $replaced = $this->replaceOrderItemSlot($formData, OrderItemType::DISCOUNT, $productData);
        $this->assertGreaterThan(0, $replaced, '値引き明細のスロットが見つからなかった');

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_edit', ['id' => $orderId]),
            ['order' => $formData, 'mode' => 'register']
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $orderId])));

        $EditedOrder = $this->orderRepository->find($orderId);
        $ProductOrderItems = $EditedOrder->getProductOrderItems();
        $this->assertNotEmpty($ProductOrderItems);
        foreach ($ProductOrderItems as $ProductItem) {
            // 全ての商品明細が Shipping に紐づいていること(値引きスロット再利用でも孤児化しない).
            $this->assertInstanceOf(
                Shipping::class,
                $ProductItem->getShipping(),
                '商品明細が Shipping に紐づいていない(孤児化している)'
            );
        }
    }

    /**
     * createFormDataForEdit は tax_type を含めないため, 実フォーム(hidden で tax_type を送信)に合わせて
     * 各明細に tax_type を補完する. これをしないと再バインド時に tax_type が null 化する.
     *
     * @param array<string, mixed> $formData
     */
    private function fillTaxType(array &$formData, Order $Order): void
    {
        $items = array_values($Order->getOrderItems()->toArray());
        foreach ($formData['OrderItems'] as $i => &$fd) {
            if (isset($items[$i]) && null !== $items[$i]->getTaxType()) {
                $fd['tax_type'] = $items[$i]->getTaxType()->getId();
            }
        }
        unset($fd);

        if (isset($formData['Shipping']['OrderItems'])) {
            $shippingItems = array_values($Order->getShippings()[0]->getOrderItems()->toArray());
            foreach ($formData['Shipping']['OrderItems'] as $i => &$fd) {
                if (isset($shippingItems[$i]) && null !== $shippingItems[$i]->getTaxType()) {
                    $fd['tax_type'] = $shippingItems[$i]->getTaxType()->getId();
                }
            }
            unset($fd);
        }
    }

    /**
     * フォームデータ内(order[OrderItems] と Shipping[OrderItems] の双方)の指定種別の明細スロットを
     * 別の明細データで上書きする. 上書きした件数を返す.
     *
     * @param array<string, mixed> $formData
     * @param array<string, mixed> $newItem
     */
    private function replaceOrderItemSlot(array &$formData, int $orderItemType, array $newItem, ?int $limit = null): int
    {
        $replaced = 0;
        foreach ($formData['OrderItems'] as $i => $item) {
            if (null !== $limit && $replaced >= $limit) {
                break;
            }
            if ((int) $item['order_item_type'] === $orderItemType) {
                $formData['OrderItems'][$i] = $newItem;
                ++$replaced;
            }
        }
        if (isset($formData['Shipping']['OrderItems'])) {
            $shippingReplaced = 0;
            foreach ($formData['Shipping']['OrderItems'] as $i => $item) {
                if (null !== $limit && $shippingReplaced >= $limit) {
                    break;
                }
                if ((int) $item['order_item_type'] === $orderItemType) {
                    $formData['Shipping']['OrderItems'][$i] = $newItem;
                    ++$shippingReplaced;
                }
            }
        }

        return $replaced;
    }

    /**
     * 二重送信・多重編集による受注明細の破損を防止するテスト.
     *
     * フォーム描画時点の更新日時と現在の更新日時が異なる場合は, 既に別の操作で
     * 更新済みと判断し, 登録処理を中断する.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6671
     */
    public function testOrderEditRejectedWhenUpdateDateIsStale()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush();

        $url = $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]);
        $formData = $this->createFormData($Customer, $this->Product);

        // 描画時点の更新日時が一致していれば登録できる.
        $EditedOrder = $this->orderRepository->find($Order->getId());
        $this->assertInstanceOf(Order::class, $EditedOrder);
        $formData['form_update_date'] = $EditedOrder->getUpdateDate()->format('Y-m-d H:i:s');
        $this->client->request(
            Request::METHOD_POST, $url, ['order' => $formData, 'mode' => 'register']
        );
        $this->assertTrue(
            $this->client->getResponse()->isRedirect($url),
            '更新日時が一致する場合は登録できる'
        );

        // 描画時点の更新日時が古い(＝別の操作で更新済み)場合は登録を中断する.
        $formData['form_update_date'] = '2000-01-01 00:00:00';
        $this->client->request(
            Request::METHOD_POST, $url, ['order' => $formData, 'mode' => 'register']
        );
        $this->assertFalse(
            $this->client->getResponse()->isRedirect(),
            '更新日時が古い場合は登録されない'
        );
        $this->assertStringContainsString(
            '別の操作で更新',
            (string) $this->client->getResponse()->getContent()
        );
    }
}
