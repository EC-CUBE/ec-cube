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

class TradelawControllerTest extends AbstractAdminWebTestCase
{

    public function testRouting()
    {
        $this->client->request('GET', $this->app['url_generator']->generate('admin_setting_shop_tradelaw'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @param $isSuccess
     * @param $expected
     * @dataProvider dataSubmitProvider
     */
    public function testSubmit($isSuccess, $expected)
    {
        $formData = $this->createFormData();
        if (!$isSuccess) {
            $formData['law_company'] = '';
        }
        $this->client->request('POST',
            $this->app->url('admin_setting_shop_tradelaw'),
            array('tradelaw' => $formData)
        );

        $this->expected = $expected;
        $this->actual = $this->client->getResponse()->isRedirection();
        $this->verify();
    }

    public function createFormData()
    {
        $form = array(
            '_token' => 'dummy',
            'law_company' => '販売業者名',
            'law_manager' => '運営責任者名',
            'law_zip' => array(
                'law_zip01' => '530',
                'law_zip02' => '0001',
            ),
            'law_address' => array(
                'law_pref' => '5',
                'law_addr01' => '北区',
                'law_addr02' => '梅田',
            ),
            'law_tel' => array(
                'law_tel01' => '03',
                'law_tel02' => '1111',
                'law_tel03' => '1111',
            ),
            'law_fax' => array(
                'law_fax01' => '03',
                'law_fax02' => '1111',
                'law_fax03' => '4444',
            ),
            'law_email' => 'eccube@example.com',
            'law_url' => 'http://www.eccube.net',
            'law_term01' => 'law_term01',
            'law_term02' => 'law_term02',
            'law_term03' => 'law_term03',
            'law_term04' => 'law_term04',
            'law_term05' => 'law_term05',
            'law_term06' => 'law_term06',
        );
        return $form;
    }

    public function dataSubmitProvider()
    {
        return array(
            array(false, false),
            array(true, true),
            // To do implement
        );
    }
}
