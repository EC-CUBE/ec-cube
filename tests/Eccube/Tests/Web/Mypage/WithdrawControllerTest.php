<?php

namespace Eccube\Tests\Web\Mypage;

use Eccube\Tests\Web\AbstractWebTestCase;

class WithdrawControllerTest extends AbstractWebTestCase
{

    protected $Customer;

    public function setUp()
    {
        parent::setUp();
        $this->initializeMailCatcher();
        $this->Customer = $this->createCustomer();
    }

    public function tearDown()
    {
        $this->cleanUpMailCatcherMessages();
        parent::tearDown();
    }

    public function testIndex()
    {
        $this->logIn($this->Customer);
        $client = $this->client;

        $client->request(
            'GET',
            $this->app->path('mypage_withdraw')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testIndexWithPostConfirm()
    {
        $this->logIn($this->Customer);
        $client = $this->client;

        $crawler = $client->request(
            'POST',
            $this->app->path('mypage_withdraw'),
            array(
                'form' => array('_token' => 'dummy'),
                'mode' => 'confirm'
            )
        );

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->expected = '退会手続きを実行してもよろしいでしょうか？';
        $this->actual = $crawler->filter('h3')->text();
        $this->verify();
    }

    public function testIndexWithPostComplete()
    {
        $this->logIn($this->Customer);
        $client = $this->client;

        $crawler = $client->request(
            'POST',
            $this->app->path('mypage_withdraw'),
            array(
                'form' => array('_token' => 'dummy'),
                'mode' => 'complete'
            )
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('mypage_withdraw_complete')));

        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $this->expected = '[' . $BaseInfo->getShopName() . '] 退会手続きのご完了';
        $this->actual = $Message->subject;
        $this->verify();
    }

    public function testComplete()
    {
        $client = $this->client;

        $client->request(
            'GET',
            $this->app->path('mypage_withdraw_complete')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
