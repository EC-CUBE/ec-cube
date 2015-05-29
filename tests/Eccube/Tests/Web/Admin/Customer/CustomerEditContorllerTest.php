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


namespace Eccube\Tests\Web\Admin\Customer;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class CustomerEditControllerTest extends AbstractAdminWebTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testRoutingAdminCustomerCustomerNew()
    {
        $this->client->request('GET',
            $this->app->url('admin_customer_new')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminCustomerCustomerEdit()
    {
        // before
        $MasterCustomerStatus = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Master\CustomerStatus')
            ->find(1);
        $TestCustomer = $this->newTestCustomer($MasterCustomerStatus);
        $this->app['orm.em']->persist($TestCustomer);
        $this->app['orm.em']->flush();
        $test_customer_id = $this->app['eccube.repository.customer']
            ->findOneBy(array(
                'name01' => $TestCustomer->getName01()
            ))
            ->getId();

        // main
        $this->client->request('GET',
            $this->app->url('admin_customer_edit', array('id' => $test_customer_id))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // after
        $this->app['orm.em']->remove($TestCustomer);
        $this->app['orm.em']->flush();
    }

    private function newTestCustomer($MasterCustomerStatus)
    {
        $TestCustomer = new \Eccube\Entity\Customer();
        $TestCustomer->setName01('高橋')
            ->setName02('慎一')
            ->setKana01('タカハシ')
            ->setKana02('シンイチ')
            ->setAddr01('大阪市')
            ->setAddr02('北区梅田')
            ->setEmail('takahashi@lockon.co.jp')
            ->setSecretKey('abcdefg')
            ->setStatus($MasterCustomerStatus)
            ->setDelFlg(0);

        return $TestCustomer;
    }

}