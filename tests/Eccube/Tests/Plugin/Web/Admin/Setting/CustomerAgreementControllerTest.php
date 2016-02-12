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


namespace Eccube\Tests\Plugin\Web\Admin\Setting;

use Eccube\Event\EccubeEvents;
use Eccube\Tests\Plugin\Web\Admin\AbstractAdminWebTestCase;

class CustomerAgreementControllerTest extends AbstractAdminWebTestCase
{
    public function testRoutingAdminSettingCustomerAgreement()
    {
        $this->client->request('GET', $this->app['url_generator']->generate('admin_setting_shop_customer_agreement'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $hookpoins = array(
            EccubeEvents::ADMIN_CSV_INDEX_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
    }
}
