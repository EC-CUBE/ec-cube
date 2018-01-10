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


namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * Class TradelawControllerTest
 * @package Eccube\Tests\Web\Admin\Setting\Shop
 */
class TradelawControllerTest extends AbstractAdminWebTestCase
{
    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
    }

    /**
     * Roting
     */
    public function testRouting()
    {
        $this->client->request('GET', $this->app->url('admin_setting_shop_tradelaw'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @param bool $isSuccess
     * @param bool $expected
     * @dataProvider dataSubmitProvider
     */
    public function testSubmit($isSuccess, $expected)
    {
        $formData = $this->createFormData();
        if (!$isSuccess) {
            $formData['company'] = '';
        }
        $this->client->request(
            'POST',
            $this->app->url('admin_setting_shop_tradelaw'),
            array('tradelaw' => $formData)
        );

        $this->expected = $expected;
        $this->actual = $this->client->getResponse()->isRedirection();
        $this->verify();
    }

    /**
     * @return array
     */
    public function createFormData()
    {
        $faker = $this->getFaker();
        $tel = explode('-', $faker->phoneNumber);

        $form = array(
            '_token' => 'dummy',
            'company' => $faker->word,
            'manager' => $faker->word,
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
            'email' => $faker->email,
            'url' => $faker->url,
            'term01' => $faker->word,
            'term02' => $faker->word,
            'term03' => $faker->word,
            'term04' => $faker->word,
            'term05' => $faker->word,
            'term06' => $faker->word,
        );

        return $form;
    }

    /**
     * @return array
     */
    public function dataSubmitProvider()
    {
        return array(
            array(false, false),
            array(true, true),
            // To do implement
        );
    }
}
