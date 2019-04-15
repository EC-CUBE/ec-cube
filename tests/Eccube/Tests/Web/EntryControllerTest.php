<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

use Eccube\Entity\Master\CustomerStatus;
use Symfony\Component\HttpKernel\Exception as HttpException;

class EntryControllerTest extends AbstractWebTestCase
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
        $birth = $faker->dateTimeBetween;

        $form = array(
            'name' => array(
                'name01' => $faker->lastName,
                'name02' => $faker->firstName,
            ),
            'kana' => array(
                'kana01' => $faker->lastKanaName ,
                'kana02' => $faker->firstKanaName,
            ),
            'company_name' => $faker->company,
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
            'fax' => array(
                'fax01' => $tel[0],
                'fax02' => $tel[1],
                'fax03' => $tel[2],
            ),
            'email' => array(
                'first' => $email,
                'second' => $email,
            ),
            'password' => array(
                'first' => $password,
                'second' => $password,
            ),
            'birth' => array(
                'year' => $birth->format('Y'),
                'month' => $birth->format('n'),
                'day' => $birth->format('j'),
            ),
            'sex' => 1,
            'job' => 1,
            '_token' => 'dummy'
        );
        return $form;
    }

    public function testRoutingIndex()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', $this->app['url_generator']->generate('entry'));

        $this->expected = '新規会員登録';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testConfirm()
    {
        $client = $this->createClient();

        $crawler = $client->request('POST',
            $this->app['url_generator']->generate('entry'),
            array(
                'entry' => $this->createFormData(),
                'mode' => 'confirm',
            )
        );

        $this->expected = '新規会員登録確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testConfirmWithError()
    {
        $client = $this->createClient();

        $crawler = $client->request('POST',
            $this->app['url_generator']->generate('entry'),
            array(
                'entry' => array(),
                'mode' => 'confirm'
            )
        );

        $this->expected = '新規会員登録';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testConfirmWithModeNotFound()
    {
        $client = $this->createClient();

        $crawler = $client->request('POST',
            $this->app['url_generator']->generate('entry'),
            array(
                'entry' => $this->createFormData(),
                'mode' => 'aaaaa'
            )
        );

        $this->expected = '新規会員登録';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testCompleteWithActivate()
    {
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionCustomerActivate(1);
        $this->app['orm.em']->flush();

        $client = $this->createClient();
        $crawler = $client->request('POST',
            $this->app['url_generator']->generate('entry'),
            array(
                'entry' => $this->createFormData(),
                'mode' => 'complete'
            )
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('entry_complete')));

        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);
        $this->expected = '[' . $BaseInfo->getShopName() . '] 会員登録のご確認';
        $this->actual = $Message->subject;
        $this->verify();
    }

    public function testRoutingComplete()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']->generate('entry_complete'));

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testActivate()
    {
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $Customer = $this->createCustomer();
        $secret_key = $Customer->getSecretKey();
        $Status = $this->app['orm.em']->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::NONACTIVE);
        $Customer->setStatus($Status);
        $this->app['orm.em']->flush();

        $client = $this->createClient();
        $crawler = $client->request('GET', $this->app['url_generator']->generate('entry_activate', array('secret_key' => $secret_key)));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);
        $this->expected = '[' . $BaseInfo->getShopName() . '] 会員登録が完了しました。';
        $this->actual = $Message->subject;
        $this->verify();
    }

    public function testActivateWithNotFound()
    {
        // debugはONの時に404ページ表示しない例外になります。
        if($this->app['debug'] == true){
            $this->setExpectedException('\Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        }
        $client = $this->createClient();
        $crawler = $client->request('GET', $this->app['url_generator']->generate('entry_activate', array('secret_key' => 'aaaaa')));
        // debugはOFFの時に404ページが表示します。
        if($this->app['debug'] == false){
            $this->expected = 404;
            $this->actual = $client->getResponse()->getStatusCode();
            $this->verify();
        }
    }

    public function testActivateWithAbort()
    {
        // debugはONの時に403ページ表示しない例外になります。
        if($this->app['debug'] == true){
            $this->setExpectedException('\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException');
        }
        $client = $this->createClient();
        $crawler = $client->request('GET', $this->app['url_generator']->generate('entry_activate', array('secret_key' => '+++++++')));
        // debugはOFFの時に403ページが表示します。
        if($this->app['debug'] == false){
            $this->expected = 403;
            $this->actual = $client->getResponse()->getStatusCode();
            $this->verify();
        }
    }
}
