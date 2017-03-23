<?php

namespace Eccube\Tests\Service;

use Eccube\Service\MailService;

/**
 * MailService test cases.
 *
 * このテストは MailCatcher を使用します.
 * 事前に MailCatcher をインストールし, 起動させておく必要があります.
 *
 * インストール)
 * $ gem install mailcatcher
 *
 * 起動)
 * $ mailcatcher
 *
 * MailCatcher については下記リンクを参考にしてください
 * @link http://mailcatcher.me/
 * @link http://qiita.com/suzuki86/items/258694fb535071f0110f
 * @link http://kirii.hateblo.jp/entry/2014/07/18/000000
 */
class MailServiceTest extends AbstractServiceTestCase
{

    protected $client;
    protected $Customer;
    protected $BaseInfo;

    public function setUp()
    {
        parent::setUp();
        $this->initializeMailCatcher();
        $paths = array($this->app['config']['template_default_realdir']);
        $this->app['twig.loader']->addLoader(new \Twig_Loader_Filesystem($paths));
        $this->Customer = $this->createCustomer();
        $this->BaseInfo = $this->app['eccube.repository.base_info']->get(1);
    }

    public function tearDown()
    {
        $this->cleanUpMailCatcherMessages();
        parent::tearDown();
    }

    public function testSendCustomerConfirmMail()
    {
        $url = 'http://example.com/confirm';
        $this->app['eccube.service.mail']->sendCustomerConfirmMail($this->Customer, $url);
        $Messages = $this->getMessages();
        $Message = $this->getMessage($Messages[0]->id);

        $this->expected = $url;
        $this->verifyRegExp($Message, 'URLは'.$url.'ではありません');

        $this->expected = '[' . $this->BaseInfo->getShopName() . '] 会員登録のご確認';
        $this->actual = $Message->subject;
        $this->verify();

        $this->expected = '<'.$this->Customer->getEmail().'>';
        $this->actual = $Message->recipients[0];
        $this->verify();

        $this->expected = 'Reply-To: '.$this->BaseInfo->getEmail03();
        $this->verifyRegExp($Message, 'Reply-Toは'.$this->BaseInfo->getEmail03().'ではありません');

        $BccMessage = $this->getMessage($Messages[1]->id);
        $this->expected = 'Bcc: '.$this->BaseInfo->getEmail01();
        $this->verifyRegExp($BccMessage, 'BCC');
    }

    public function testSendCustomerCompleteMail()
    {
        $this->app['eccube.service.mail']->sendCustomerCompleteMail($this->Customer);

        $Messages = $this->getMessages();
        $Message = $this->getMessage($Messages[0]->id);

        $this->expected = '[' . $this->BaseInfo->getShopName() . '] 会員登録が完了しました。';
        $this->actual = $Message->subject;
        $this->verify();

        $this->expected = '<'.$this->Customer->getEmail().'>';
        $this->actual = $Message->recipients[0];
        $this->verify();

        $this->expected = 'Reply-To: '.$this->BaseInfo->getEmail03();
        $this->verifyRegExp($Message, 'Reply-Toは'.$this->BaseInfo->getEmail03().'ではありません');

        $BccMessage = $this->getMessage($Messages[1]->id);
        $this->expected = 'Bcc: '.$this->BaseInfo->getEmail01();
        $this->verifyRegExp($BccMessage, 'BCC');
    }

    public function testSendCustomerWithdrawMail()
    {
        $email = 'draw@example.com';
        $this->app['eccube.service.mail']->sendCustomerWithdrawMail($this->Customer, $email);

        $Messages = $this->getMessages();
        $Message = $this->getMessage($Messages[0]->id);

        $this->expected = '[' . $this->BaseInfo->getShopName() . '] 退会手続きのご完了';
        $this->actual = $Message->subject;
        $this->verify();

        $this->expected = '<'.$email.'>';
        $this->actual = $Message->recipients[0];
        $this->verify();

        $this->expected = 'Reply-To: '.$this->BaseInfo->getEmail03();
        $this->verifyRegExp($Message, 'Reply-Toは'.$this->BaseInfo->getEmail03().'ではありません');

        $BccMessage = $this->getMessage($Messages[1]->id);
        $this->expected = 'Bcc: '.$this->BaseInfo->getEmail01();
        $this->verifyRegExp($BccMessage, 'BCC');
    }

    /**
     * @link https://github.com/EC-CUBE/ec-cube/issues/1315
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function testSendrContactMail()
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
        $Pref = $this->app['eccube.repository.master.pref']->find(1);
        $addr01 = $faker->city;
        $addr02 = $faker->streetAddress;
        $tel = explode('-', $faker->phoneNumber);
        $tel01 = $tel[0];
        $tel02 = $tel[1];
        $tel03 = $tel[2];

        $formData = array(
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
            'contents' => 'お問い合わせ内容'
        );

        $this->app['eccube.service.mail']->sendrContactMail($formData);

        $Messages = $this->getMessages();
        $Message = $this->getMessage($Messages[0]->id);

        $this->expected = '[' . $this->BaseInfo->getShopName() . '] お問い合わせを受け付けました。';
        $this->actual = $Message->subject;
        $this->verify();

        $this->expected = '<'.$email.'>';
        $this->actual = $Message->recipients[0];
        $this->verify();

        $this->expected = 'Reply-To: '.$this->BaseInfo->getEmail03();
        $this->verifyRegExp($Message, 'Reply-Toは'.$this->BaseInfo->getEmail03().'ではありません');

        $this->expected = 'お問い合わせ内容';
        $this->verifyRegExp($Message, 'お問い合わせ内容');

        $BccMessage = $this->getMessage($Messages[1]->id);
        $this->expected = 'Bcc: '.$this->BaseInfo->getEmail01();
        $this->verifyRegExp($BccMessage, 'BCC');
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
        $Pref = $this->app['eccube.repository.master.pref']->find(1);
        $addr01 = $faker->city;
        $addr02 = $faker->streetAddress;
        $tel = explode('-', $faker->phoneNumber);
        $tel01 = $tel[0];
        $tel02 = $tel[1];
        $tel03 = $tel[2];

        $formData = array(
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
            'contents' => 'お問い合わせ内容'
        );

        $this->app['eccube.service.mail']->sendContactMail($formData);

        $Messages = $this->getMessages();
        $Message = $this->getMessage($Messages[0]->id);

        $this->expected = '[' . $this->BaseInfo->getShopName() . '] お問い合わせを受け付けました。';
        $this->actual = $Message->subject;
        $this->verify();

        $this->expected = '<'.$email.'>';
        $this->actual = $Message->recipients[0];
        $this->verify();

        $this->expected = 'Reply-To: '.$this->BaseInfo->getEmail03();
        $this->verifyRegExp($Message, 'Reply-Toは'.$this->BaseInfo->getEmail03().'ではありません');

        $this->expected = 'お問い合わせ内容';
        $this->verifyRegExp($Message, 'お問い合わせ内容');

        $BccMessage = $this->getMessage($Messages[1]->id);
        $this->expected = 'Bcc: '.$this->BaseInfo->getEmail01();
        $this->verifyRegExp($BccMessage, 'BCC');
    }

    public function testSendOrderMail()
    {
        $Order = $this->createOrder($this->Customer);
        $this->app['eccube.service.mail']->sendOrderMail($Order);

        $Messages = $this->getMessages();
        $Message = $this->getMessage($Messages[0]->id);

        $this->expected = '[' . $this->BaseInfo->getShopName() . '] ご注文ありがとうございます';
        $this->actual = $Message->subject;
        $this->verify();

        $this->expected = '<'.$this->Customer->getEmail().'>';
        $this->actual = $Message->recipients[0];
        $this->verify();

        $this->expected = 'Reply-To: '.$this->BaseInfo->getEmail03();
        $this->verifyRegExp($Message, 'Reply-Toは'.$this->BaseInfo->getEmail03().'ではありません');

        $BccMessage = $this->getMessage($Messages[1]->id);
        $this->expected = 'Bcc: '.$this->BaseInfo->getEmail01();
        $this->verifyRegExp($BccMessage, 'BCC');
    }

    public function testSendAdminCustomerConfirmMail()
    {
        $this->app['twig']->addGlobal('BaseInfo', $this->BaseInfo);
        $url = 'http://example.com/confirm';
        $this->app['eccube.service.mail']->sendAdminCustomerConfirmMail($this->Customer, $url);
        $Messages = $this->getMessages();
        $Message = $this->getMessage($Messages[0]->id);

        $this->expected = $url;
        $this->verifyRegExp($Message, 'URLは'.$url.'ではありません');

        $this->expected = '[' . $this->BaseInfo->getShopName() . '] 会員登録のご確認';
        $this->actual = $Message->subject;
        $this->verify();

        $this->expected = '<'.$this->Customer->getEmail().'>';
        $this->actual = $Message->recipients[0];
        $this->verify();

        $this->expected = 'Reply-To: '.$this->BaseInfo->getEmail03();
        $this->verifyRegExp($Message, 'Reply-Toは'.$this->BaseInfo->getEmail03().'ではありません');

        $BccMessage = $this->getMessage($Messages[1]->id);
        $this->expected = 'Bcc: '.$this->BaseInfo->getEmail01();
        $this->verifyRegExp($BccMessage, 'BCC');
    }

    public function testSendAdminOrderMail()
    {
        $Order = $this->createOrder($this->Customer);
        $faker = $this->getFaker();
        $header = $faker->paragraph;
        $footer = $faker->paragraph;
        $subject = $faker->sentence;
        $formData = array(
            'header' => $header,
            'footer' => $footer,
            'subject' => $subject
        );
        $this->app['eccube.service.mail']->sendAdminOrderMail($Order, $formData);

        $Messages = $this->getMessages();
        $Message = $this->getMessage($Messages[0]->id);

        $this->expected = '[' . $this->BaseInfo->getShopName() . '] '.$subject;
        $this->actual = $Message->subject;
        $this->verify();

        $this->expected = '<'.$this->Customer->getEmail().'>';
        $this->actual = $Message->recipients[0];
        $this->verify();

        $this->expected = 'Reply-To: '.$this->BaseInfo->getEmail03();
        $this->verifyRegExp($Message, 'Reply-Toは'.$this->BaseInfo->getEmail03().'ではありません');

        $BccMessage = $this->getMessage($Messages[1]->id);
        $this->expected = 'Bcc: '.$this->BaseInfo->getEmail01();
        $this->verifyRegExp($BccMessage, 'BCC');
    }

    public function testSendPasswordResetNotificationMail()
    {
        $this->app['twig']->addGlobal('BaseInfo', $this->BaseInfo);
        $url = 'http://example.com/reset';
        $this->app['eccube.service.mail']->sendPasswordResetNotificationMail($this->Customer, $url);
        $Messages = $this->getMessages();
        $Message = $this->getMessage($Messages[0]->id);

        $this->expected = $url;
        $this->verifyRegExp($Message, 'URLは'.$url.'ではありません');

        $this->expected = '[' . $this->BaseInfo->getShopName() . '] パスワード変更のご確認';
        $this->actual = $Message->subject;
        $this->verify();

        $this->expected = '<'.$this->Customer->getEmail().'>';
        $this->actual = $Message->recipients[0];
        $this->verify();

        $this->expected = 'Reply-To: '.$this->BaseInfo->getEmail03();
        $this->verifyRegExp($Message, 'Reply-Toは'.$this->BaseInfo->getEmail03().'ではありません');

        $this->assertLessThanOrEqual(1, count($Messages), 'Bccメールは送信しない');
    }

    public function testSendPasswordResetCompleteMail()
    {
        $this->app['twig']->addGlobal('BaseInfo', $this->BaseInfo);
        $faker = $this->getFaker();
        $password = $faker->password;
        $this->app['eccube.service.mail']->sendPasswordResetCompleteMail($this->Customer, $password);
        $Messages = $this->getMessages();
        $Message = $this->getMessage($Messages[0]->id);

        $this->expected = '[' . $this->BaseInfo->getShopName() . '] パスワード変更のお知らせ';
        $this->actual = $Message->subject;
        $this->verify();

        $this->expected = '<'.$this->Customer->getEmail().'>';
        $this->actual = $Message->recipients[0];
        $this->verify();

        $this->expected = 'Reply-To: '.$this->BaseInfo->getEmail03();
        $this->verifyRegExp($Message, 'Reply-Toは'.$this->BaseInfo->getEmail03().'ではありません');

        $this->assertLessThanOrEqual(1, count($Messages), 'Bccメールは送信しない');
    }

    protected function getMessages()
    {
        return $this->getMailCatcherMessages();
    }

    protected function getMessage($id)
    {
        return $this->getMailCatcherMessage($id);
    }

    protected function verifyRegExp($Message, $errorMessage = null)
    {
        $Source = $this->parseMailCatcherSource($Message);
        $this->assertRegExp('/'.preg_quote($this->expected, '/').'/', $Source, $errorMessage);
    }
}
