<?php

namespace Eccube\Tests\Plugin\Web;

use Eccube\Event\EccubeEvents;
use Symfony\Component\HttpKernel\Exception as HttpException;

class ForgotControllerTest extends AbstractWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->initializeMailCatcher();
    }

    public function tearDown()
    {
        $this->cleanUpMailCatcherMessages();
        parent::tearDown();
    }

    public function testIndex()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', $this->app->url('forgot'));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $hookpoins = array(
            EccubeEvents::FRONT_FORGOT_INDEX_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
    }

    public function testIndexWithPostAndVerify()
    {
        $Customer = $this->createCustomer();
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $client = $this->createClient();

        // パスワード再発行リクエスト
        $crawler = $client->request(
            'POST',
            $this->app->url('forgot'),
            array(
                'login_email' => $Customer->getEmail(),
                '_token' => 'dummy'
            )
        );
        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('forgot_complete')));

        $customer_id = $Customer->getId();
        $OrigCustomer = $this->app['eccube.repository.customer']->find($customer_id);
        $key = $OrigCustomer->getResetKey();

        // メール URL クリック
        $crawler = $client->request(
            'GET',
            'http://localhost/forgot/reset/' . $key
        );
        $this->assertTrue($client->getResponse()->isSuccessful());

        $hookpoins = array(
            EccubeEvents::FRONT_FORGOT_INDEX_INITIALIZE,
            EccubeEvents::FRONT_FORGOT_INDEX_COMPLETE,
            EccubeEvents::MAIL_PASSWORD_RESET,
            EccubeEvents::FRONT_FORGOT_RESET_COMPLETE,
            EccubeEvents::MAIL_PASSWORD_RESET_COMPLETE,
        );
        $this->verifyOutputString($hookpoins);
    }
}
