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


namespace Eccube\Tests\Plugin\Web\Mypage;

use Eccube\Event\EccubeEvents;
use Eccube\Tests\Plugin\Web\AbstractWebTestCase;

class ChangeControllerTest extends AbstractWebTestCase
{

    protected $Customer;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
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

    public function testIndex()
    {
        $this->logIn($this->Customer);
        $client = $this->client;

        $client->request(
            'GET',
            $this->app->path('mypage_change')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());

        $hookpoins = array(
            EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
    }

    public function testIndexWithPost()
    {
        $this->logIn($this->Customer);
        $client = $this->client;

        $form = $this->createFormData();
        $crawler = $client->request(
            'POST',
            $this->app->path('mypage_change'),
            array('entry' => $form)
        );
        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('mypage_change_complete')));

        $hookpoins = array(
            EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_INITIALIZE,
            EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_COMPLETE,
        );
        $this->verifyOutputString($hookpoins);

    }

    public function testIndexWithPostDefaultPassword()
    {
        $this->logIn($this->Customer);
        $client = $this->client;

        $form = $this->createFormData();
        $form['password'] = array(
            'first' => $this->app['config']['default_password'],
            'second' => $this->app['config']['default_password']
        );
        $crawler = $client->request(
            'POST',
            $this->app->path('mypage_change'),
            array('entry' => $form)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('mypage_change_complete')));

        $hookpoins = array(
            EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_INITIALIZE,
            EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_COMPLETE,
        );
        $this->verifyOutputString($hookpoins);
    }

    public function testIndexWithPostInvalid()
    {
        $this->logIn($this->Customer);
        $client = $this->client;

        $client->request(
            'POST',
            $this->app->path('mypage_change'),
            array()
        );
        $this->assertTrue($client->getResponse()->isSuccessful());

        $hookpoins = array(
            EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
    }

    public function testComplete()
    {
        $this->logIn($this->Customer);
        $client = $this->client;

        $client->request(
            'GET',
            $this->app->path('mypage_change_complete')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());

        $hookpoins = array(
            // Nothing
        );
        $this->verifyOutputString($hookpoins);
    }
}
