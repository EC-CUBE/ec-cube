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

use Eccube\Entity\BaseInfo;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\Mime\Email;

class ContactControllerTest extends AbstractWebTestCase
{
    use MailerAssertionsTrait;

    protected function createFormData()
    {
        $faker = $this->getFaker();
        $email = $faker->safeEmail;
        $password = $faker->lexify('????????');

        $form = [
            'name' => [
                'name01' => $faker->lastName,
                'name02' => $faker->firstName,
            ],
            'kana' => [
                'kana01' => $faker->lastKanaName,
                'kana02' => $faker->firstKanaName,
            ],
            'postal_code' => $faker->postcode,
            'address' => [
                'pref' => '5',
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ],
            'phone_number' => $faker->phoneNumber,
            'email' => $email,
            'contents' => $faker->realText(),
            '_token' => 'dummy',
        ];

        return $form;
    }

    public function testRoutingIndex()
    {
        $this->client->request('GET', $this->generateUrl('contact'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testConfirm()
    {
        $this->markTestIncomplete('FIXME title');
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('contact'),
            ['contact' => $this->createFormData(),
                  'mode' => 'confirm', ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = 'お問い合わせ(確認ページ)';
        $this->actual = $crawler->filter('title')->text();

        $this->assertMatchesRegularExpression('/'.preg_quote($this->expected).'$/', $this->actual);
    }

    public function testComplete()
    {
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('contact'),
            ['contact' => $this->createFormData(),
                  'mode' => 'complete', ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('contact_complete')));

        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->expected = '['.$BaseInfo->getShopName().'] お問い合わせを受け付けました。';
        $this->actual = $Message->getSubject();
        $this->verify();
    }

    public function testCompleteWithSanitize()
    {
        $form = $this->createFormData();
        $form['name']['name01'] .= '<Sanitize&>';
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('contact'),
            ['contact' => $form,
                  'mode' => 'complete', ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('contact_complete')));

        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->expected = '['.$BaseInfo->getShopName().'] お問い合わせを受け付けました。';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->assertStringContainsString('＜Sanitize＆＞', $Message->getTextBody(), 'テキストメールがサニタイズされている');
        $this->assertStringContainsString('＜Sanitize＆＞', $Message->getHtmlBody(), 'HTMLメールがサニタイズされている');
    }

    /**
     * 必須項目のみのテストケース
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1314
     */
    public function testCompleteWithRequired()
    {
        $formData = $this->createFormData();
        $formData['kana']['kana01'] = null;
        $formData['kana']['kana02'] = null;
        $formData['postal_code'] = null;
        $formData['address']['pref'] = null;
        $formData['address']['addr01'] = null;
        $formData['address']['addr02'] = null;
        $formData['phone_number'] = null;

        $this->client->enableProfiler();
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('contact'),
            ['contact' => $formData,
                  'mode' => 'complete', ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('contact_complete')));

        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->expected = '['.$BaseInfo->getShopName().'] お問い合わせを受け付けました。';
        $this->actual = $Message->getSubject();
        $this->verify();
    }

    public function testCompleteWithLogin()
    {
        // Always generate a form before login
        $formData = $this->createFormData();
        $this->logInTo($this->createCustomer());

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('contact'),
            ['contact' => $formData,
                  'mode' => 'complete', ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('contact_complete')));

        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);

        $this->expected = '['.$BaseInfo->getShopName().'] お問い合わせを受け付けました。';
        $this->actual = $Message->getSubject();
        $this->verify();
    }

    public function testRoutingComplete()
    {
        $this->client->request('GET', $this->generateUrl('contact_complete'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testMailNoRFC()
    {
        $formData = $this->createFormData();
        // RFCに準拠していないメールアドレスを設定
        $formData['email'] = 'aa..@example.com';

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('contact'),
            ['contact' => $formData,
                'mode' => 'complete', ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('contact_complete')));
    }
}
