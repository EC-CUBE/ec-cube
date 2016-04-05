<?php

namespace Eccube\Tests\Plugin\Web\Mypage;

use Eccube\Event\EccubeEvents;
use Eccube\Tests\Plugin\Web\AbstractWebTestCase;

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

        $hookpoins = array(
            EccubeEvents::FRONT_MYPAGE_WITHDRAW_INDEX_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
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

        $hookpoins = array(
            EccubeEvents::FRONT_MYPAGE_WITHDRAW_INDEX_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
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

        $hookpoins = array(
            EccubeEvents::FRONT_MYPAGE_WITHDRAW_INDEX_INITIALIZE,
            EccubeEvents::FRONT_MYPAGE_WITHDRAW_INDEX_COMPLETE,
            EccubeEvents::MAIL_CUSTOMER_WITHDRAW,
        );
        $this->verifyOutputString($hookpoins);
    }
}
