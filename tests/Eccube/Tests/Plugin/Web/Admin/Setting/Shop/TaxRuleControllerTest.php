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


namespace Eccube\Tests\Plugin\Web\Admin\Setting\Shop;

use Eccube\Event\EccubeEvents;
use Eccube\Tests\Plugin\Web\Admin\AbstractAdminWebTestCase;

class TaxRuleControllerTest extends AbstractAdminWebTestCase
{

    public function test_routeing_AdminBasisTax_index()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_setting_shop_tax')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $hookpoins = array(
            EccubeEvents::ADMIN_SETTING_SHOP_TAX_RULE_INDEX_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
    }

    public function test_routeing_AdminBasisTax_edit()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_setting_shop_tax_edit', array('id' => 1))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $hookpoins = array(
            EccubeEvents::ADMIN_SETTING_SHOP_TAX_RULE_INDEX_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
    }

    public function test_routeing_AdminProductTax_edit()
    {
        $this->client->request(
            'POST',
            $this->app->url('admin_setting_shop_tax_edit_param', array('id' => 1))
        );
        $redirectUrl = $this->app->url('admin_setting_shop_tax');
        $this->assertSame(true, $this->client->getResponse()->isRedirect($redirectUrl));

        $hookpoins = array(
            EccubeEvents::ADMIN_SETTING_SHOP_TAX_RULE_EDIT_PARAMETER_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
    }

    public function test_routeing_AdminBasisTax_delete()
    {
        $redirectUrl = $this->app->url('admin_setting_shop_tax');

        $this->client->request(
            'DELETE',
            $this->app->url('admin_setting_shop_tax_delete', array('id' => 1))
        );

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertSame(true, $actual);
    }
}
