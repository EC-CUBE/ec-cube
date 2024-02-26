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

namespace Eccube\Tests\Web\Mypage;

use Eccube\Entity\Customer;
use Eccube\Tests\Web\AbstractWebTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\Mime\Email;

class WithdrawControllerTest extends AbstractWebTestCase
{
    use MailerAssertionsTrait;

    /**
     * @var Customer
     */
    protected $Customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
    }

    protected function tearDown(): void
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
        $this->logInTo($this->Customer);

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('mypage_withdraw'),
            [
                'form' => ['_token' => 'dummy'],
                'mode' => 'complete',
            ]
        );

        $this->assertMatchesRegularExpression('/@dummy.dummy/', $this->Customer->getEmail());

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('mypage_withdraw_complete')));

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $BaseInfo = $this->entityManager->getRepository(\Eccube\Entity\BaseInfo::class)->get();
        $this->expected = '['.$BaseInfo->getShopName().'] 退会手続きのご完了';
        $this->actual = $Message->getSubject();
        $this->verify();
    }

    public function testIndexWithPostCompleteWithSanitize()
    {
        $this->Customer->setName01('<Sanitize&>');
        $this->entityManager->flush();

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

        $this->assertMatchesRegularExpression('/@dummy.dummy/', $this->Customer->getEmail());

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('mypage_withdraw_complete')));

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $BaseInfo = $this->entityManager->getRepository(\Eccube\Entity\BaseInfo::class)->get();
        $this->expected = '['.$BaseInfo->getShopName().'] 退会手続きのご完了';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->assertEmailTextBodyContains($Message, '＜Sanitize&＞', 'テキストメールがサニタイズされている');
        $this->assertEmailHtmlBodyNotContains($Message, '＜Sanitize&＞', 'HTML part は存在しない');
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
