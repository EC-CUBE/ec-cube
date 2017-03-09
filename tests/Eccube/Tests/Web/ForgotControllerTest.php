<?php

namespace Eccube\Tests\Web;

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

        $this->expected = 'パスワードの再発行';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $this->assertTrue($client->getResponse()->isSuccessful());
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

        // メール受信確認
        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);
        $this->expected = '[' . $BaseInfo->getShopName() . '] パスワード変更のご確認';
        $this->actual = $Message->subject;
        $this->verify();
        $this->cleanUpMailCatcherMessages();

        $OrigCustomer = $this->app['eccube.repository.customer']->find($customer_id);
        $key = $OrigCustomer->getResetKey();

        $this->assertEquals(1, preg_match('|http://localhost(.*)|', $this->parseMailCatcherSource($Message), $urls));
        $forgot_path = trim($urls[1]);

        // メール URL クリック
        $crawler = $client->request(
            'GET',
            $forgot_path
        );
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->expected = 'パスワード変更(完了ページ)';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        // 再発行メール受信確認
        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);
        $this->expected = '[' . $BaseInfo->getShopName() . '] パスワード変更のお知らせ';
        $this->actual = $Message->subject;
        $this->verify();

        $this->assertRegexp('/新しいパスワード：[a-zA-Z0-9]/u', $this->parseMailCatcherSource($Message));
    }

    public function testComplete()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', $this->app->url('forgot_complete'));

        $this->expected = 'パスワード発行メールの送信 完了';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testResetWithDenied()
    {
        // debugはONの時に403ページ表示しない例外になります。
        if($this->app['debug'] == true){
            $this->setExpectedException('\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException');
        }
        $client = $this->createClient();
            $crawler = $client->request(
                'GET',
                '/forgot/reset/a___aaa'
            );
           
        // debugはOFFの時に403ページが表示します。
        if($this->app['debug'] == false){
            $this->expected = 403;
            $this->actual = $client->getResponse()->getStatusCode();
            $this->verify();
        }
    }

    public function testResetWithNotFound()
    {
        // debugはONの時に404ページ表示しない例外になります。
        if($this->app['debug'] == true){
            $this->setExpectedException('\Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        }
        $client = $this->createClient();
        
        $crawler = $client->request(
           'GET',
           '/forgot/reset/aaaa'
        );
        // debugはOFFの時に404ページが表示します。
        if($this->app['debug'] == false){
            $this->expected = 404;
            $this->actual = $client->getResponse()->getStatusCode();
            $this->verify();
        }
    }
}
