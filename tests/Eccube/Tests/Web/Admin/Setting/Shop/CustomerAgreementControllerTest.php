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

class CustomerAgreementControllerTest extends AbstractAdminWebTestCase
{
    public function testRoutingAdminSettingCustomerAgreement()
    {
        $this->client->request('GET', $this->app['url_generator']->generate('admin_setting_shop_customer_agreement'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @param $content
     * @param $expected
     * @dataProvider dataSubmitProvider
     */
    public function testSubmit($content, $expected)
    {
        $this->client->request('POST',
            $this->app->url('admin_setting_shop_customer_agreement'),
            array('customer_agreement' => $this->createFormData($content))
        );
        $this->expected = $expected;
        $this->actual = $this->client->getResponse()->isRedirection();
        $this->verify();
    }

    public function createFormData($content = '')
    {
        $form = array(
            '_token' => 'dummy',
            'customer_agreement' => $content,
        );

        return $form;
    }

    public function dataSubmitProvider()
    {
        $faker = $this->getFaker();

        return array(
            array('', false),
            array($faker->paragraph, true),
            // To do implement
        );
    }
}
