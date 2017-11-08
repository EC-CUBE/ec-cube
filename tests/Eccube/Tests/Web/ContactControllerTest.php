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

class ContactControllerTest extends AbstractWebTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->initializeMailCatcher();
    }

    public function tearDown()
    {
        $this->cleanUpMailCatcherMessages();
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
            'contents' => $faker->text(),
            '_token' => 'dummy'
        );
        return $form;
    }

    public function testRoutingIndex()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app->path('contact'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testConfirm()
    {
        $client = $this->createClient();

        $crawler = $client->request(
            'POST',
            $this->app->path('contact'),
            array('contact' => $this->createFormData(),
                  'mode' => 'confirm')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->expected = 'お問い合わせ(確認ページ)';
        $this->actual = $crawler->filter('title')->text();
        $this->assertRegexp('/'.preg_quote($this->expected).'$/', $this->actual);
    }

    public function testComplete()
    {
        $client = $this->createClient();

        $crawler = $client->request(
            'POST',
            $this->app->path('contact'),
            array('contact' => $this->createFormData(),
                  'mode' => 'complete')
        );
        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('contact_complete')));

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

        $this->expected = '[' . $BaseInfo->getShopName() . '] お問い合わせを受け付けました。';
        $this->actual = $Message->subject;
        $this->verify();

    }

    /**
     * 必須項目のみのテストケース
     * @link https://github.com/EC-CUBE/ec-cube/issues/1314
     */
    public function testCompleteWithRequired()
    {
        $client = $this->createClient();

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

        $crawler = $client->request(
            'POST',
            $this->app->path('contact'),
            array('contact' => $formData,
                  'mode' => 'complete')
        );
        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('contact_complete')));

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

        $this->expected = '[' . $BaseInfo->getShopName() . '] お問い合わせを受け付けました。';
        $this->actual = $Message->subject;
        $this->verify();
    }

    public function testCompleteWithLogin()
    {
        $client = $this->createClient();
        $this->logIn();
        $crawler = $client->request(
            'POST',
            $this->app->path('contact'),
            array('contact' => $this->createFormData(),
                  'mode' => 'complete')
        );
        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('contact_complete')));

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

        $this->expected = '[' . $BaseInfo->getShopName() . '] お問い合わせを受け付けました。';
        $this->actual = $Message->subject;
        $this->verify();

    }

    public function testRoutingComplete()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app->path('contact_complete'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
