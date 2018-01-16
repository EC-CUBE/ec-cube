<?php

namespace Eccube\Tests\Web;

use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CustomerRepository;

class ForgotControllerTest extends AbstractWebTestCase
{
    /**
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    public function setUp()
    {
        parent::setUp();
        $this->client->enableProfiler();
        $this->baseInfoRepository = $this->container->get(BaseInfoRepository::class);
        $this->customerRepository = $this->container->get(CustomerRepository::class);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testIndex()
    {
        $crawler = $this->client->request('GET', $this->generateUrl('forgot'));

        $this->expected = 'パスワードの再発行';
        $this->actual = $crawler->filter('div.ec-pageHeader > h1')->text();
        $this->verify();

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndexWithPostAndVerify()
    {
        $Customer = $this->createCustomer();
        $BaseInfo = $this->baseInfoRepository->get();

        // パスワード再発行リクエスト
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('forgot'),
            array(
                'login_email' => $Customer->getEmail(),
                '_token' => $this->getCsrfToken('forgot')
            )
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('forgot_complete')));

        $mailCollector = $this->getMailCollector(false);

        // メール受信確認
        $Messages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $Messages[0];
        $this->expected = '[' . $BaseInfo->getShopName() . '] パスワード変更のご確認';
        $this->actual = $Message->getSubject();
        $this->verify();

        $OrigCustomer = $this->customerRepository->find($Customer->getId());
        $key = $OrigCustomer->getResetKey();

        $this->markTestIncomplete('invalid token value');
        dump([$Message->getSender(), $Message->getFrom()]);
        die('1');
        $cleanContent = quoted_printable_decode($Message->getSender());
        $this->assertEquals(1, preg_match('|http://localhost(.*)|', $this->parseMailCatcherSource($Message), $urls));
        $forgot_path = trim($urls[1]);

        // メール URL クリック
        $crawler = $this->client->request(
            'GET',
            $forgot_path
        );
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->expected = 'パスワード変更(完了ページ)';
        $this->actual = $crawler->filter('div.ec-pageHeader > h1')->text();
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
        $this->actual = $crawler->filter('div.ec-pageHeader > h1')->text();
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

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testResetWithNotFound()
    {
        $client = $this->createClient();

        $crawler = $client->request(
           'GET',
           '/forgot/reset/aaaa'
        );
    }
}
