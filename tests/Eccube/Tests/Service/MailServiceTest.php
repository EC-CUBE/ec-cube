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

namespace Eccube\Tests\Service;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\Shipping;
use Eccube\Repository\MailHistoryRepository;
use Eccube\Service\MailService;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * MailService test cases.
 */
class MailServiceTest extends AbstractServiceTestCase
{
    use MailerAssertionsTrait;

    /**
     * @var Customer
     */
    protected $Customer;

    /**
     * @var Order
     */
    protected $Order;
    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $this->BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $this->mailService = static::getContainer()->get(MailService::class);

        $request = Request::createFromGlobals();
        static::getContainer()->get('request_stack')->push($request);
        $twig = static::getContainer()->get('twig');
        $twig->addGlobal('BaseInfo', $this->BaseInfo);
    }

    public function testSendCustomerConfirmMail()
    {
        $url = 'http://example.com/confirm';
        $this->mailService->sendCustomerConfirmMail($this->Customer, $url);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->assertEmailTextBodyContains($Message, $url, 'URLは'.$url.'ではありません');

        $this->expected = '['.$this->BaseInfo->getShopName().'] 会員登録のご確認';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $this->Customer->getEmail();
        $this->actual = $Message->getTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = $Message->getReplyTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = $Message->getBcc()[0]->getAddress();
        $this->verify();

        $this->assertEmailHtmlBodyContains($Message, $url, 'URLは'.$url.'ではありません');
    }

    public function testSendCustomerCompleteMail()
    {
        $this->mailService->sendCustomerCompleteMail($this->Customer);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->expected = '['.$this->BaseInfo->getShopName().'] 会員登録が完了しました。';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $this->Customer->getEmail();
        $this->actual = $Message->getTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = $Message->getReplyTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = $Message->getBcc()[0]->getAddress();
        $this->verify();

        $this->assertEmailTextBodyContains($Message, '本会員登録が完了いたしました');
        $this->assertEmailHtmlBodyContains($Message, '本会員登録が完了いたしました');
    }

    public function testSendCustomerWithdrawMail()
    {
        $email = 'draw@example.com';
        $this->mailService->sendCustomerWithdrawMail($this->Customer, $email);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->expected = '['.$this->BaseInfo->getShopName().'] 退会手続きのご完了';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $email;
        $this->actual = $Message->getTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = $Message->getReplyTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = $Message->getBcc()[0]->getAddress();
        $this->verify();

        $this->assertEmailTextBodyContains($Message, '退会手続きが完了いたしました');
        $this->assertEmailHtmlBodyNotContains($Message, '退会手続きが完了いたしました', 'HTML part は存在しない');
    }

    public function testSendContactMail()
    {
        $faker = $this->getFaker();
        $name01 = $faker->lastName;
        $name02 = $faker->firstName;
        $kana01 = $faker->lastName;
        $kana02 = $faker->firstName;
        $email = $faker->email;
        $postCode = $faker->postCode;
        $Pref = $this->entityManager->find(\Eccube\Entity\Master\Pref::class, 1);
        $addr01 = $faker->city;
        $addr02 = $faker->streetAddress;

        $formData = [
            'name01' => $name01,
            'name02' => $name02,
            'kana01' => $kana01,
            'kana02' => $kana02,
            'postal_code' => $postCode,
            'pref' => $Pref,
            'addr01' => $addr01,
            'addr02' => $addr02,
            'phone_number' => $faker->phoneNumber,
            'email' => $email,
            'contents' => 'お問い合わせ内容',
        ];

        $this->mailService->sendContactMail($formData);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->expected = '['.$this->BaseInfo->getShopName().'] お問い合わせを受け付けました。';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $email;
        $this->actual = $Message->getTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = $Message->getReplyTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = $Message->getBcc()[0]->getAddress();

        $this->assertEmailTextBodyContains($Message, 'お問い合わせ内容');
        $this->assertEmailHtmlBodyContains($Message, 'お問い合わせ内容');
    }

    public function testSendOrderMail()
    {
        $Order = $this->Order;
        $this->mailService->sendOrderMail($Order);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->expected = '['.$this->BaseInfo->getShopName().'] ご注文ありがとうございます';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $Order->getEmail();
        $this->actual = $Message->getTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = $Message->getReplyTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = $Message->getBcc()[0]->getAddress();
        $this->verify();

        $this->assertEmailTextBodyContains($Message, 'この度はご注文いただき誠にありがとうございます。');
        $this->assertEmailHtmlBodyContains($Message, 'この度はご注文いただき誠にありがとうございます。');
    }

    public function testSendAdminCustomerConfirmMail()
    {
        $url = 'http://example.com/confirm';
        $this->mailService->sendAdminCustomerConfirmMail($this->Customer, $url);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->assertEmailTextBodyContains($Message, $url, 'URLは'.$url.'ではありません');
        $this->assertEmailHtmlBodyContains($Message, $url, 'URLは'.$url.'ではありません');
        $this->expected = '['.$this->BaseInfo->getShopName().'] 会員登録のご確認';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $this->Customer->getEmail();
        $this->actual = $Message->getTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = $Message->getReplyTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = $Message->getBcc()[0]->getAddress();
        $this->verify();
    }

    public function testSendAdminOrderMail()
    {
        $Order = $this->Order;
        $faker = $this->getFaker();
        $subject = $faker->sentence;
        $formData = [
            'mail_subject' => $subject,
            'tpl_data' => $faker->text,
        ];
        $this->mailService->sendAdminOrderMail($Order, $formData);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->expected = '['.$this->BaseInfo->getShopName().'] '.$subject;
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $Order->getEmail();
        $this->actual = $Message->getTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = $Message->getReplyTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = $Message->getBcc()[0]->getAddress();
        $this->verify();
    }

    public function testSendPasswordResetNotificationMail()
    {
        $url = 'http://example.com/reset';
        $this->mailService->sendPasswordResetNotificationMail($this->Customer, $url);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->assertEmailTextBodyContains($Message, $url, 'URLは'.$url.'ではありません');
        $this->assertEmailHtmlBodyNotContains($Message, $url, 'HTML part は存在しない');

        $this->expected = '['.$this->BaseInfo->getShopName().'] パスワード変更のご確認';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $this->Customer->getEmail();
        $this->actual = $Message->getTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = $Message->getReplyTo()[0]->getAddress();
        $this->verify();
    }

    public function testSendPasswordResetCompleteMail()
    {
        $faker = $this->getFaker();
        $password = $faker->password;
        $this->mailService->sendPasswordResetCompleteMail($this->Customer, $password);
        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->expected = '['.$this->BaseInfo->getShopName().'] パスワード変更のお知らせ';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = $Message->getReplyTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = $Message->getBcc()[0]->getAddress();
        $this->verify();

        $this->assertEmailTextBodyContains($Message, 'パスワードを変更いたしました。');
        $this->assertEmailHtmlBodyNotContains($Message, 'パスワードを変更いたしました。', 'HTML part は存在しない');
    }

    public function testConvertRFCViolatingEmail()
    {
        $this->expected = new Address('".aa"@example.com');
        $this->actual = $this->mailService->convertRFCViolatingEmail('.aa@example.com');
        $this->verify();

        $this->expected = new Address('"aa."@example.com');
        $this->actual = $this->mailService->convertRFCViolatingEmail('aa.@example.com');
        $this->verify();

        $this->expected = new Address('"a..a"@example.com');
        $this->actual = $this->mailService->convertRFCViolatingEmail('a..a@example.com');
        $this->verify();
    }

    public function testCustomerChangeNotifyMailByEmailAddressChange()
    {
        $userData = [
            'userAgent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36',
            'ipAddress' => '192.168.0.100',
            'preEmail' => 'abc@example.com',
        ];
        $eventName = '会員情報編集';

        $this->mailService->sendCustomerChangeNotifyMail($this->Customer, $userData, $eventName);
        // 変更前と変更後の両方送られる関係で2
        $this->assertEmailCount(2);

        // 変更前と変更後のメールをそれぞれチェック
        for ($i = 0; $i < 2; $i++) {
            /** @var Email $Message */
            $Message = $this->getMailerMessage($i);

            $this->expected = '['.$this->BaseInfo->getShopName().'] 会員情報変更のお知らせ';
            $this->actual = $Message->getSubject();
            $this->verify();

            // 0が変更後。1が変更前
            if ($i == 0) {
                $this->expected = $this->Customer->getEmail();
            } else {
                $this->expected = $userData['preEmail'];
            }
            $this->actual = $Message->getTo()[0]->getAddress();
            $this->verify();

            $this->expected = $this->BaseInfo->getEmail03();
            $this->actual = $Message->getReplyTo()[0]->getAddress();
            $this->verify();

            $this->expected = $this->BaseInfo->getEmail01();
            $this->actual = $Message->getBcc()[0]->getAddress();
            $this->verify();

            $this->assertEmailTextBodyContains($Message, '会員情報編集 がありましたのでお知らせいたします。');
            $this->assertEmailHtmlBodyContains($Message, '会員情報編集 がありましたのでお知らせいたします。');

            $this->assertEmailTextBodyContains($Message, $userData['userAgent']);
            $this->assertEmailHtmlBodyContains($Message, $userData['userAgent']);

            $this->assertEmailTextBodyContains($Message, $userData['ipAddress']);
            $this->assertEmailHtmlBodyContains($Message, $userData['ipAddress']);
        }
    }


    public function testCustomerChangeNotifyMailByDeliveryAddressChange()
    {
        $userData = [
            'userAgent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36',
            'ipAddress' => '192.168.0.100'
        ];
        $eventName = 'お届け先情報編集';

        $this->mailService->sendCustomerChangeNotifyMail($this->Customer, $userData, $eventName);
        // 変更前と変更後のアドレスは変わらないので1
        $this->assertEmailCount(1);

        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->expected = '['.$this->BaseInfo->getShopName().'] 会員情報変更のお知らせ';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $this->Customer->getEmail();
        $this->actual = $Message->getTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = $Message->getReplyTo()[0]->getAddress();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = $Message->getBcc()[0]->getAddress();
        $this->verify();

        $this->assertEmailTextBodyContains($Message, 'お届け先情報編集 がありましたのでお知らせいたします。');
        $this->assertEmailHtmlBodyContains($Message, 'お届け先情報編集 がありましたのでお知らせいたします。');

        $this->assertEmailTextBodyContains($Message, $userData['userAgent']);
        $this->assertEmailHtmlBodyContains($Message, $userData['userAgent']);

        $this->assertEmailTextBodyContains($Message, $userData['ipAddress']);
        $this->assertEmailHtmlBodyContains($Message, $userData['ipAddress']);

    }
}
