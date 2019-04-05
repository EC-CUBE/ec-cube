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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeliveryControllerTest extends AbstractWebTestCase
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
            $this->app->path('mypage_delivery')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testNew()
    {
        $this->logIn($this->Customer);
        $client = $this->client;

        $client->request(
            'GET',
            $this->app->path('mypage_delivery_new')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testNewWithPost()
    {
        $this->logIn($this->Customer);
        $client = $this->client;

        $form = $this->createFormData();
        $crawler = $client->request(
            'POST',
            $this->app->path('mypage_delivery_new'),
            array('customer_address' => $form)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('mypage_delivery')));
    }

    public function testEdit()
    {
        $this->logIn($this->Customer);
        $client = $this->client;

        $CustomerAddress = $this->app['eccube.repository.customer_address']->findOneBy(
            array('Customer' => $this->Customer)
        );

        $crawler = $client->request(
            'GET',
            $this->app->path('mypage_delivery_edit', array('id' => $CustomerAddress->getId()))
        );

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testEditWithPost()
    {
        $this->logIn($this->Customer);
        $client = $this->client;

        $CustomerAddress = $this->app['eccube.repository.customer_address']->findOneBy(
            array('Customer' => $this->Customer)
        );

        $form = $this->createFormData();
        $crawler = $client->request(
            'POST',
            $this->app->path('mypage_delivery_edit', array('id' => $CustomerAddress->getId())),
            array('customer_address' => $form)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('mypage_delivery')));

        $this->expected = $form['name']['name01'];
        $this->actual = $CustomerAddress->getName01();
        $this->verify();
    }

    public function testDelete()
    {
        $this->logIn($this->Customer);
        $client = $this->client;

        $CustomerAddress = $this->app['eccube.repository.customer_address']->findOneBy(
            array('Customer' => $this->Customer)
        );
        $id = $CustomerAddress->getId();

        $form = $this->createFormData();
        $crawler = $client->request(
            'DELETE',
            $this->app->path('mypage_delivery_delete', array('id' => $id))
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('mypage_delivery')));

        $CustomerAddress = $this->app['eccube.repository.customer_address']->find($id);
        $this->assertNull($CustomerAddress);

        $this->expected = array('mypage.address.delete.complete');
        $this->actual = $this->app['session']->getFlashBag()->get('eccube.front.success');
        $this->verify();
    }

    public function testDeleteWithFailure()
    {
        $this->logIn($this->Customer);
        $client = $this->client;

        $crawler = $client->request(
            'DELETE',
            $this->app->path('mypage_delivery_delete', array('id' => 999999999))
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('mypage_delivery')));

        $this->expected = array('mypage.address.delete.failed');
        $this->actual = $this->app['session']->getFlashBag()->get('eccube.front.error');
        $this->verify();
    }
}
