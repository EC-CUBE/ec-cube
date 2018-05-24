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

namespace Eccube\Tests\Service;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\Shipping;
use Eccube\Repository\MailHistoryRepository;
use Eccube\Service\MailService;

/**
 * MailService test cases.
 */
class MailServiceTest extends AbstractServiceTestCase
{
    /**
     * @var Customer
     */
    protected $Customer;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var \Swift_Message
     */
    protected $Message;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->BaseInfo = $this->container->get(BaseInfo::class);
        $this->mailService = $this->container->get(MailService::class);
    }

    public function testSendCustomerConfirmMail()
    {
        $url = 'http://example.com/confirm';
        $this->mailService->sendCustomerConfirmMail($this->Customer, $url);

        $mailCollector = $this->getMailCollector();
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $this->expected = $url;
        $this->verifyRegExp($Message, 'URLは'.$url.'ではありません');

        $this->expected = '['.$this->BaseInfo->getShopName().'] 会員登録のご確認';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $this->Customer->getEmail();
        $this->actual = key($Message->getTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = key($Message->getReplyTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = key($Message->getBcc());
        $this->verify();
    }

    public function testSendCustomerCompleteMail()
    {
        $this->mailService->sendCustomerCompleteMail($this->Customer);

        $mailCollector = $this->getMailCollector();
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $this->expected = '['.$this->BaseInfo->getShopName().'] 会員登録が完了しました。';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $this->Customer->getEmail();
        $this->actual = key($Message->getTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = key($Message->getReplyTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = key($Message->getBcc());
        $this->verify();
    }

    public function testSendCustomerWithdrawMail()
    {
        $email = 'draw@example.com';
        $this->mailService->sendCustomerWithdrawMail($this->Customer, $email);

        $mailCollector = $this->getMailCollector();
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $this->expected = '['.$this->BaseInfo->getShopName().'] 退会手続きのご完了';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $email;
        $this->actual = key($Message->getTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = key($Message->getReplyTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = key($Message->getBcc());
        $this->verify();
    }

    public function testSendContactMail()
    {
        $faker = $this->getFaker();
        $name01 = $faker->lastName;
        $name02 = $faker->firstName;
        $kana01 = $faker->lastName;
        $kana02 = $faker->firstName;
        $email = $faker->email;
        $zip = $faker->postCode;
        $zip01 = substr($zip, 0, 3);
        $zip02 = substr($zip, 3, 7);
        $Pref = $this->entityManager->find(\Eccube\Entity\Master\Pref::class, 1);
        $addr01 = $faker->city;
        $addr02 = $faker->streetAddress;
        $tel = explode('-', $faker->phoneNumber);
        $tel01 = $tel[0];
        $tel02 = $tel[1];
        $tel03 = $tel[2];

        $formData = [
            'name01' => $name01,
            'name02' => $name02,
            'kana01' => $kana01,
            'kana02' => $kana02,
            'zip01' => $zip01,
            'zip02' => $zip02,
            'pref' => $Pref,
            'addr01' => $addr01,
            'addr02' => $addr02,
            'tel01' => $tel01,
            'tel02' => $tel02,
            'tel03' => $tel03,
            'email' => $email,
            'contents' => 'お問い合わせ内容',
        ];

        $this->mailService->sendContactMail($formData);

        $mailCollector = $this->getMailCollector();
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $this->expected = '['.$this->BaseInfo->getShopName().'] お問い合わせを受け付けました。';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $email;
        $this->actual = key($Message->getTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = key($Message->getReplyTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = key($Message->getBcc());

        $this->expected = 'お問い合わせ内容';
        $this->verifyRegExp($Message, 'お問い合わせ内容');
    }

    public function testSendOrderMail()
    {
        $Order = $this->createOrder($this->Customer);
        $this->mailService->sendOrderMail($Order);

        $mailCollector = $this->getMailCollector();
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $this->expected = '['.$this->BaseInfo->getShopName().'] ご注文ありがとうございます';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $Order->getEmail();
        $this->actual = key($Message->getTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = key($Message->getReplyTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = key($Message->getBcc());
    }

    public function testSendAdminCustomerConfirmMail()
    {
        $url = 'http://example.com/confirm';
        $this->mailService->sendAdminCustomerConfirmMail($this->Customer, $url);

        $mailCollector = $this->getMailCollector();
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $this->expected = $url;
        $this->verifyRegExp($Message, 'URLは'.$url.'ではありません');

        $this->expected = '['.$this->BaseInfo->getShopName().'] 会員登録のご確認';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $this->Customer->getEmail();
        $this->actual = key($Message->getTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = key($Message->getReplyTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = key($Message->getBcc());
        $this->verify();
    }

    public function testSendAdminOrderMail()
    {
        $Order = $this->createOrder($this->Customer);
        $faker = $this->getFaker();
        $header = $faker->paragraph;
        $footer = $faker->paragraph;
        $subject = $faker->sentence;
        $formData = [
            'mail_header' => $header,
            'mail_footer' => $footer,
            'mail_subject' => $subject,
        ];
        $this->mailService->sendAdminOrderMail($Order, $formData);

        $mailCollector = $this->getMailCollector();
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $this->expected = '['.$this->BaseInfo->getShopName().'] '.$subject;
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $this->Customer->getEmail();
        $this->actual = key($Message->getTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = key($Message->getReplyTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = key($Message->getBcc());
        $this->verify();
    }

    public function testSendPasswordResetNotificationMail()
    {
        $url = 'http://example.com/reset';
        $this->mailService->sendPasswordResetNotificationMail($this->Customer, $url);

        $mailCollector = $this->getMailCollector();
        $this->assertLessThanOrEqual(1, $mailCollector->getMessageCount(), 'Bccメールは送信しない');

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $this->expected = $url;
        $this->verifyRegExp($Message, 'URLは'.$url.'ではありません');

        $this->expected = '['.$this->BaseInfo->getShopName().'] パスワード変更のご確認';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $this->Customer->getEmail();
        $this->actual = key($Message->getTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = key($Message->getReplyTo());
        $this->verify();
    }

    public function testSendPasswordResetCompleteMail()
    {
        $faker = $this->getFaker();
        $password = $faker->password;
        $this->mailService->sendPasswordResetCompleteMail($this->Customer, $password);

        $mailCollector = $this->getMailCollector();
        $this->assertLessThanOrEqual(1, $mailCollector->getMessageCount(), 'Bccメールは送信しない');

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $this->expected = '['.$this->BaseInfo->getShopName().'] パスワード変更のお知らせ';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail03();
        $this->actual = key($Message->getReplyTo());
        $this->verify();

        $this->expected = $this->BaseInfo->getEmail01();
        $this->actual = key($Message->getBcc());
        $this->verify();
    }

    public function testConvertMessageISO()
    {
        // TODO  https://github.com/EC-CUBE/ec-cube/issues/2402#issuecomment-362487022
        $this->markTestSkipped('実装確認中のためスキップ');
        $config = $this->app['config'];
        $config['mail']['charset_iso_2022_jp'] = true;
        $this->app['config'] = $config;

        $this->app->initMailer();

        $Order = $this->createOrder($this->Customer);
        // 戻り値はiso-2022-jpのmessage
        $message = $this->app['eccube.service.mail']->sendOrderMail($Order);

        $this->expected = mb_strtolower($message->getCharset());
        $this->actual = 'iso-2022-jp';
        $this->verify();

        $this->expected = $message->getBody();

        // 文字コードがiso-2022-jpからUTF-8に変換されたものと比較
        // MailUtil::convertMessage($this->app, $message);
        // $this->actual = $message->getBody();
        // $this->assertNotEquals($this->expected, $this->actual);

        // 文字コードがUTF-8からiso-2022-jpに変換されたものと比較
        MailUtil::setParameterForCharset($this->app, $message);
        $this->actual = $message->getBody();
        $this->assertEquals($this->expected, $this->actual);

        $config = $this->app['config'];
        $config['mail']['charset_iso_2022_jp'] = false;
        $this->app['config'] = $config;
    }

    public function testConvertMessageUTF()
    {
        // TODO  https://github.com/EC-CUBE/ec-cube/issues/2402#issuecomment-362487022
        $this->markTestSkipped('実装確認中のためスキップ');

        $config = $this->app['config'];
        $config['mail']['charset_iso_2022_jp'] = false;
        $this->app['config'] = $config;

        $this->app->initMailer();

        $Order = $this->createOrder($this->Customer);
        // 戻り値はUTFのmessage
        $message = $this->app['eccube.service.mail']->sendOrderMail($Order);

        $this->expected = mb_strtolower($message->getCharset());
        $this->actual = 'utf-8';
        $this->verify();

        $this->expected = $message->getBody();

        // 変換されない
        MailUtil::convertMessage($this->app, $message);
        $this->actual = $message->getBody();
        $this->verify();

        // 変換されない
        MailUtil::setParameterForCharset($this->app, $message);
        $this->actual = $message->getBody();
        $this->verify();
    }

    /**
     * @throws \Twig_Error
     */
    public function testSendShippingNotifyMail()
    {
        $Order = $this->createOrder($this->Customer);
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();

        $this->mailService->sendShippingNotifyMail($Shipping);
        $this->entityManager->flush();

        $messages = $this->getMailCollector()->getMessages();
        self::assertEquals(1, count($messages));

        /** @var \Swift_Message $message */
        $message = $messages[0];
        self::assertEquals([$Order->getEmail() => 0], $message->getTo(), '受注者にメールが送られているはず');

        /** @var MailHistoryRepository $mailHistoryRepository */
        $mailHistoryRepository = $this->container->get(MailHistoryRepository::class);
        $histories = $mailHistoryRepository->findBy(['Order' => $Order]);
        self::assertEquals(1, count($histories), 'メール履歴が作成されているはず');
    }

    protected function verifyRegExp($Message, $errorMessage = null)
    {
        $this->assertRegExp('/'.preg_quote($this->expected, '/').'/', $Message->getBody(), $errorMessage);
    }
}
