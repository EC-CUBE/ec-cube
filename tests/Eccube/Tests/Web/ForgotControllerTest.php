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

namespace Eccube\Tests\Web;

use Eccube\Common\Constant;
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
        $this->baseInfoRepository = $this->entityManager->getRepository(\Eccube\Entity\BaseInfo::class);
        $this->customerRepository = $this->entityManager->getRepository(\Eccube\Entity\Customer::class);
        $this->client->disableReboot();
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
        $this->markTestIncomplete('expected and actual is diff');
        $Customer = $this->createCustomer();
        $BaseInfo = $this->baseInfoRepository->get();

        // パスワード再発行リクエスト
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('forgot'),
            [
                'login_email' => $Customer->getEmail(),
                Constant::TOKEN_NAME => 'dummy',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('forgot_complete')));

        $mailCollector = $this->getMailCollector(false);

        // メール受信確認
        $Messages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $Messages[0];
        $this->expected = '['.$BaseInfo->getShopName().'] パスワード変更のご確認';
        $this->actual = $Message->getSubject();
        $this->verify();

        $cleanContent = quoted_printable_decode($Message->getBody());
        $this->assertEquals(1, preg_match('|http://localhost(.*)|', $cleanContent, $urls));
        $forgot_path = trim($urls[1]);

        // メール URL クリック
        $crawler = $this->client->request(
            'GET',
            $forgot_path
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = 'パスワード再発行(再設定ページ)';
        $this->actual = $crawler->filter('div.ec-pageHeader > h1')->text();
        $this->verify();

        // パスワード再設定リクエスト
        $password = 'password_Changed';
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('forgot_reset'),
            [
                'login_email' => $Customer->getEmail(),
                'password[first]' => $password,
                'password[second]' => $password,
                Constant::TOKEN_NAME => 'dummy',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testResetWithInvalid()
    {
        $client = $this->client;
        $client->request(
            'GET',
            '/forgot/reset/a___aaa'
        );

        $this->expected = 404;
        $this->actual = $client->getResponse()->getStatusCode();
        $this->verify();
    }

    public function testResetWithNotFound()
    {
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
