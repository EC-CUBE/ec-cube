<?php

namespace Eccube\Tests\Web;

use Eccube\Common\Constant;
use Eccube\Form\Type\Front\ForgotType;
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
        $this->markTestIncomplete("reset_key can not found");
        $Customer = $this->createCustomer();
        $BaseInfo = $this->baseInfoRepository->get();

        // パスワード再発行リクエスト
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('forgot'),
            array(
                'login_email' => $Customer->getEmail(),
                Constant::TOKEN_NAME => $this->getCsrfToken(ForgotType::class)
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
        $cleanContent = quoted_printable_decode($Message->getBody());
        $this->assertEquals(1, preg_match('|http://localhost(.*)|', $cleanContent, $urls));
        $forgot_path = trim($urls[1]);

        // メール URL クリック
        $crawler = $this->client->request(
            'GET',
            $forgot_path
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = 'パスワード変更(完了ページ)';
        $this->actual = $crawler->filter('div.ec-pageHeader > h1')->text();
        $this->verify();

        // 再発行メール受信確認
        $Messages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $Messages[0];
        $this->expected = '[' . $BaseInfo->getShopName() . '] パスワード変更のお知らせ';
        $this->actual = $Message->getSubject();
        $cleanContent = quoted_printable_decode($Message->getBody());
        $this->verify();

        $this->assertRegexp('/新しいパスワード：[a-zA-Z0-9]/u', $cleanContent);
    }

    public function testComplete()
    {
        $client = $this->client;
        $crawler = $client->request('GET', $this->generateUrl('forgot_complete'));

        $this->expected = 'パスワード発行メールの送信 完了';
        $this->actual = $crawler->filter('div.ec-pageHeader > h1')->text();
        $this->verify();

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testResetWithDenied()
    {
        // Todo: Fail assert if debug = true
        // $isDebug = $this->container->getParameter('kernel.debug');

        // debugはONの時に403ページ表示しない例外になり`ます。
        // if($isDebug == true){
        //    $this->expectException(\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException::class);
        // }

        $client = $this->client;
        $client->request(
            'GET',
            '/forgot/reset/a___aaa'
        );

        // debugはOFFの時に403ページが表示します。
        // if($isDebug == false){
        $this->expected = 403;
        $this->actual = $client->getResponse()->getStatusCode();
        $this->verify();
        // }
    }

    public function testResetWithNotFound()
    {
        // $this->markTestIncomplete('Todo: Fail to catch exception by PHPUNIT');
        // $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $client = $this->client;

        $client->request(
           'GET',
           '/forgot/reset/aaaa'
        );

        $this->expected = 404;
        $this->actual = $client->getResponse()->getStatusCode();
        $this->verify();
    }
}
