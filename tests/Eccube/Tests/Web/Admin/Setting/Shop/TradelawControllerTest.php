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


namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * Class TradelawControllerTest
 * @package Eccube\Tests\Web\Admin\Setting\Shop
 */
class TradelawControllerTest extends AbstractAdminWebTestCase
{
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
            $formData['law_company'] = '';
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
            'law_company' => $faker->word,
            'law_manager' => $faker->word,
            'law_zip' => array(
                'law_zip01' => $faker->postcode1(),
                'law_zip02' => $faker->postcode2(),
            ),
            'law_address' => array(
                'law_pref' => '5',
                'law_addr01' => $faker->city,
                'law_addr02' => $faker->streetAddress,
            ),
            'law_tel' => array(
                'law_tel01' => $tel[0],
                'law_tel02' => $tel[1],
                'law_tel03' => $tel[2],
            ),
            'law_fax' => array(
                'law_fax01' => $tel[0],
                'law_fax02' => $tel[1],
                'law_fax03' => $tel[2],
            ),
            'law_email' => $faker->email,
            'law_url' => $faker->url,
            'law_term01' => $faker->word,
            'law_term02' => $faker->word,
            'law_term03' => $faker->word,
            'law_term04' => $faker->word,
            'law_term05' => $faker->word,
            'law_term06' => $faker->word,
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
