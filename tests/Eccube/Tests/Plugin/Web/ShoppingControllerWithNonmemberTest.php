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


namespace Eccube\Tests\Plugin\Web;

use Eccube\Event\EccubeEvents;

class ShoppingControllerWithNonmemberTest extends AbstractWebTestCase
{

    protected $Product;

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

    /**
     * 非会員情報入力→購入確認画面
     */
    public function testConfirmWithNonmember()
    {
        $client = $this->createClient();
        $this->scenarioCartIn($client);

        $hookpoins = array(
            EccubeEvents::FRONT_CART_ADD_INITIALIZE,
            EccubeEvents::FRONT_CART_ADD_COMPLETE,
        );

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $hookpoins = array_merge($hookpoins,
            array(
                EccubeEvents::FRONT_SHOPPING_NONMEMBER_INITIALIZE,
                EccubeEvents::FRONT_SHOPPING_NONMEMBER_COMPLETE,
            )
        );

        $crawler = $client->request('GET', $this->app->path('shopping'));
        $this->assertTrue($client->getResponse()->isSuccessful());

        $hookpoins = array_merge($hookpoins,
            array(
                EccubeEvents::FRONT_SHOPPING_INDEX_INITIALIZE,
            )
        );

        $this->verifyOutputString($hookpoins);
    }

    /**
     * 非会員情報入力→購入確認画面→完了画面
     */
    public function testCompleteWithNonmember()
    {
        $faker = $this->getFaker();
        $client = $this->createClient();
        $this->scenarioCartIn($client);

        $hookpoins = array(
            EccubeEvents::FRONT_CART_ADD_INITIALIZE,
            EccubeEvents::FRONT_CART_ADD_COMPLETE,
        );

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $hookpoins = array_merge($hookpoins,
            array(
                EccubeEvents::FRONT_SHOPPING_NONMEMBER_INITIALIZE,
                EccubeEvents::FRONT_SHOPPING_NONMEMBER_COMPLETE,
            )
        );

        $crawler = $this->scenarioConfirm($client);

        $hookpoins = array_merge($hookpoins,
            array(
                EccubeEvents::FRONT_SHOPPING_INDEX_INITIALIZE,
            )
        );

        $this->scenarioComplete($client, $this->app->path('shopping_confirm'));

        $hookpoins = array_merge($hookpoins,
            array(
                EccubeEvents::FRONT_SHOPPING_CONFIRM_INITIALIZE,
                EccubeEvents::SERVICE_SHOPPING_ORDER_STATUS,
                EccubeEvents::FRONT_SHOPPING_CONFIRM_PROCESSING,
                EccubeEvents::MAIL_ORDER,
                EccubeEvents::FRONT_SHOPPING_CONFIRM_COMPLETE,
            )
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_complete')));

        $this->verifyOutputString($hookpoins);
    }

    public function createNonmemberFormData()
    {
        $faker = $this->getFaker();
        $tel = explode('-', $faker->phoneNumber);

        $email = $faker->safeEmail;

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
            'email' => array(
                'first' => $email,
                'second' => $email,
            ),
            '_token' => 'dummy'
        );
        return $form;
    }

    protected function scenarioCartIn($client)
    {
        $crawler = $client->request('POST', '/cart/add', array('product_class_id' => 1));
        $this->app['eccube.service.cart']->lock();
        return $crawler;
    }

    protected function scenarioInput($client, $formData)
    {
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_nonmember'),
            array('nonmember' => $formData)
        );
        $this->app['eccube.service.cart']->lock();
        return $crawler;
    }

    protected function scenarioConfirm($client)
    {
        $crawler = $client->request('GET', $this->app->path('shopping'));
        return $crawler;
    }

    protected function scenarioComplete($client, $confirm_url)
    {
        $faker = $this->getFaker();
        $crawler = $client->request(
            'POST',
            $confirm_url,
            array('shopping' =>
                  array(
                      'shippings' =>
                      array(0 =>
                            array(
                                'delivery' => 1,
                                'deliveryTime' => 1
                            ),
                      ),
                      'payment' => 1,
                      'message' => $faker->text(),
                      '_token' => 'dummy'
                  )
            )
        );
        return $crawler;
    }
}
