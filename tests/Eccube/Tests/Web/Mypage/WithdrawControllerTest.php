<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Web\Mypage;

use Eccube\Entity\Customer;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Tests\Web\AbstractWebTestCase;

class WithdrawControllerTest extends AbstractWebTestCase
{
    /**
     * @var Customer
     */
    protected $Customer;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testIndex()
    {
        $this->logInTo($this->Customer);

        $this->client->request(
            'GET',
            $this->generateUrl('mypage_withdraw')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndexWithPostConfirm()
    {
        $this->logInTo($this->Customer);

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('mypage_withdraw'),
            [
                'form' => ['_token' => 'dummy'],
                'mode' => 'confirm',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = '退会手続きを実行してもよろしいでしょうか？';
        $this->actual = $crawler->filter('p.ec-withdrawConfirmRole__title')->text();
        $this->verify();
    }

    public function testIndexWithPostComplete()
    {
        $this->client->enableProfiler();
        $this->logInTo($this->Customer);

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('mypage_withdraw'),
            [
                'form' => ['_token' => 'dummy'],
                'mode' => 'complete',
            ]
        );

        $this->assertRegExp('/@dummy.dummy/', $this->Customer->getEmail());

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('mypage_withdraw_complete')));

        $Messages = $this->getMailCollector(false)->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $Messages[0];

        $BaseInfo = $this->container->get(BaseInfoRepository::class)->get();
        $this->expected = '['.$BaseInfo->getShopName().'] 退会手続きのご完了';
        $this->actual = $Message->getSubject();
        $this->verify();
    }

    public function testComplete()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('mypage_withdraw_complete')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
