<?php

namespace Eccube\Tests\Web;
use Eccube\Common\Constant;

/**
 * 複数配送指定のテストケース.
 *
 * @author Kentaro Ohkouchi
 */
class ShoppingControllerWithMultipleTest extends AbstractShoppingControllerTestCase
{

    public function setUp()
    {
        parent::setUp();

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        // 複数配送を有効に
        $BaseInfo->setOptionMultipleShipping(1);
        $this->app['orm.em']->flush($BaseInfo);
    }

    public function tearDown()
    {
        $this->cleanUpMailCatcherMessages();
        parent::tearDown();
    }

    /**
     * カート→購入確認画面→複数配送設定画面→購入確認画面→完了画面
     */
    public function testCompleteWithLogin()
    {
        $faker = $this->getFaker();
        $Customer = $this->logIn();
        $CustomerAddress = $this->createCustomerAddress($Customer);

        $client = $this->client;
        // カート画面
        $this->scenarioCartIn($client);
        $this->scenarioCartIn($client); // 2個カート投入

        // 確認画面
        $crawler = $this->scenarioConfirm($client);
        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        // 複数配送画面
        $crawler = $client->request('GET', $this->app->path('shopping_shipping_multiple'));

        // 配送先1, 配送先2の情報を返す
        $shippings = $crawler->filter('#form_shipping_multiple_0_shipping_0_customer_address > option')->each(
            function ($node, $i) {
                return array(
                    'customer_address' => $node->attr('value'),
                    'quantity' => 1
                );
            }
        );

        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_shipping_multiple'),
            array('form' =>
                  array(
                      'shipping_multiple' =>
                      array(0 =>
                            array(
                                // 配送先1, 配送先2 の 情報を渡す
                                'shipping' => $shippings
                            )
                      ),
                      '_token' => 'dummy'
                  )
            )
        );

        // 確認画面
        $crawler = $this->scenarioConfirm($client);

        // 完了画面
        $crawler = $this->scenarioComplete(
            $client,
            $this->app->path('shopping_confirm'),
            array(
                // 配送先1
                array(
                    'delivery' => 1,
                    'deliveryTime' => 1
                ),
                // 配送先2
                array(
                    'delivery' => 1,
                    'deliveryTime' => 1
                )
            )
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_complete')));

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

        $this->expected = '[' . $BaseInfo->getShopName() . '] ご注文ありがとうございます';
        $this->actual = $Message->subject;
        $this->verify();

        $body = $this->parseMailCatcherSource($Message);
        $this->assertRegexp('/◎お届け先2/', $body, '複数配送のため, お届け先2が存在する');

        // 生成された受注のチェック
        $Order = $this->app['eccube.repository.order']->findOneBy(
            array(
                'Customer' => $Customer
            )
        );

        $OrderNew = $this->app['eccube.repository.order_status']->find($this->app['config']['order_new']);
        $this->expected = $OrderNew;
        $this->actual = $Order->getOrderStatus();
        $this->verify();

        $this->expected = $Customer->getName01();
        $this->actual = $Order->getName01();
        $this->verify();
    }

    /**
     * Test add multi shipping
     */
    public function testShippingShippingMultiAdd()
    {
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);

        $User = $this->logIn();
        $client = $this->client;

        $client->request('POST', '/cart/add', array('product_class_id' => 10, 'quantity' => 2));
        $client->request('POST', '/cart/add', array('product_class_id' => 1, 'quantity' => 1));

        $this->scenarioCartIn($client);

        // 確認画面
        $crawler = $this->scenarioConfirm($client);
        // お届け先指定画面
        $shippingUrl = $crawler->filter('a.btn-shipping')->attr('href');
        $this->scenarioComplete($client, $shippingUrl);

        $CustomerAddress =  $this->createCustomerAddress($User);
        $User->addCustomerAddress($CustomerAddress);

        $arrCustomerAddress = $User->getCustomerAddresses();

        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => $arrCustomerAddress->first()->getId(),
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => $arrCustomerAddress->last()->getId(),
                            'quantity' => 1,
                        ),
                    ),
                ),
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => $arrCustomerAddress->first()->getId(),
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => $arrCustomerAddress->last()->getId(),
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array("form" => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        $Order = $this->app['eccube.repository.order']->findOneBy(array('Customer' => $User));
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);
        $this->expected = count($arrCustomerAddress);
        $this->verify();
    }
}
