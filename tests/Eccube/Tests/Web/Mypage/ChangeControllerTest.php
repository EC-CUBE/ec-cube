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


namespace Eccube\Tests\Web\Mypage;

use Eccube\Tests\Web\AbstractWebTestCase;

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

        $this->expected = $form['email']['first'];
        $this->actual = $this->Customer->getEmail();
        $this->verify();
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
    }
}
