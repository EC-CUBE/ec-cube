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

namespace Eccube\Tests\Web;

use Eccube\Entity\BaseInfo;

class ContactControllerTest extends AbstractWebTestCase
{
    protected function createFormData()
    {
        $faker = $this->getFaker();
        $tel = explode('-', $faker->phoneNumber);

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
            'zip' => [
                'zip01' => $faker->postcode1(),
                'zip02' => $faker->postcode2(),
            ],
            'address' => [
                'pref' => '5',
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ],
            'tel' => [
                'tel01' => $tel[0],
                'tel02' => $tel[1],
                'tel03' => $tel[2],
            ],
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

        $this->assertRegexp('/'.preg_quote($this->expected).'$/', $this->actual);
    }

    public function testComplete()
    {
        $this->client->enableProfiler();

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('contact'),
            ['contact' => $this->createFormData(),
                  'mode' => 'complete', ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('contact_complete')));

        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);

        $mailCollector = $this->getMailCollector(false);
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $this->expected = '['.$BaseInfo->getShopName().'] お問い合わせを受け付けました。';
        $this->actual = $Message->getSubject();
        $this->verify();
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
        $formData['zip']['zip01'] = null;
        $formData['zip']['zip02'] = null;
        $formData['address']['pref'] = null;
        $formData['address']['addr01'] = null;
        $formData['address']['addr02'] = null;
        $formData['tel']['tel01'] = null;
        $formData['tel']['tel02'] = null;
        $formData['tel']['tel03'] = null;

        $this->client->enableProfiler();
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('contact'),
            ['contact' => $formData,
                  'mode' => 'complete', ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('contact_complete')));

        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);

        $mailCollector = $this->getMailCollector(false);
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $this->expected = '['.$BaseInfo->getShopName().'] お問い合わせを受け付けました。';
        $this->actual = $Message->getSubject();
        $this->verify();
    }

    public function testCompleteWithLogin()
    {
        // require for retrieving mail
        $this->client->enableProfiler();

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
        $mailCollector = $this->getMailCollector(false);

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $this->expected = '['.$BaseInfo->getShopName().'] お問い合わせを受け付けました。';
        $this->actual = $Message->getSubject();
        $this->verify();
    }

    public function testRoutingComplete()
    {
        $this->client = $this->createClient();
        $this->client->request('GET', $this->generateUrl('contact_complete'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
