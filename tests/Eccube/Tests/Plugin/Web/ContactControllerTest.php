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


namespace Eccube\Tests\Plugin\Web;

use Eccube\Event\EccubeEvents;

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

        $hookpoins = array(
            EccubeEvents::FRONT_CONTACT_INDEX_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
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

        $hookpoins = array(
            EccubeEvents::FRONT_CONTACT_INDEX_INITIALIZE,
            EccubeEvents::FRONT_CONTACT_INDEX_COMPLETE,
            EccubeEvents::MAIL_CONTACT,
        );
        $this->verifyOutputString($hookpoins);
    }
}
