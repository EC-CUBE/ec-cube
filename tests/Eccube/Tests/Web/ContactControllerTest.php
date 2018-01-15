<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Tests\Web;

use Eccube\Entity\BaseInfo;

class ContactControllerTest extends AbstractWebTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    protected function createFormData()
    {
        $faker = $this->getFaker();
        $tel = explode('-', $faker->phoneNumber);

        $email = $faker->safeEmail;
        $password = $faker->lexify('????????');

        $form = array(
            'name' => array(
                'name01' => $faker->lastName,
                'name02' => $faker->firstName,
            ),
            'kana' => array(
                'kana01' => $faker->lastKanaName ,
                'kana02' => $faker->firstKanaName,
            ),
            'zip' => array(
                'zip01' => $faker->postcode1(),
                'zip02' => $faker->postcode2(),
            ),
            'address' => array(
                'pref' => '5',
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ),
            'tel' => array(
                'tel01' => $tel[0],
                'tel02' => $tel[1],
                'tel03' => $tel[2],
            ),
            'email' => $email,
            'contents' => $faker->realText(),
            '_token' => $this->getCsrfToken('contact')
        );

        return $form;
    }

    public function testRoutingIndex()
    {
        $this->client->request('GET', $this->generateUrl('contact'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testConfirm()
    {
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('contact'),
            array('contact' => $this->createFormData(),
                  'mode' => 'confirm')
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
            array('contact' => $this->createFormData(),
                  'mode' => 'complete')
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('contact_complete')));

        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);

        $mailCollector = $this->getMailCollector(false);
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $this->expected = '[' . $BaseInfo->getShopName() . '] お問い合わせを受け付けました。';
        $this->actual = $Message->getSubject();
        $this->verify();

    }

    /**
     * 必須項目のみのテストケース
     * @link https://github.com/EC-CUBE/ec-cube/issues/1314
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
            array('contact' => $formData,
                  'mode' => 'complete')
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('contact_complete')));

        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);

        $mailCollector = $this->getMailCollector(false);
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $this->expected = '[' . $BaseInfo->getShopName() . '] お問い合わせを受け付けました。';
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
            array('contact' => $formData,
                  'mode' => 'complete')
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('contact_complete')));

        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $mailCollector = $this->getMailCollector(false);

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $this->expected = '[' . $BaseInfo->getShopName() . '] お問い合わせを受け付けました。';
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
