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


namespace Eccube\Tests\Web\Admin\Setting\System;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class MasterdataControllerTest extends AbstractAdminWebTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testRouting()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_setting_system_masterdata')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testSelect()
    {
        $entity = 'Eccube\Entity\Master\Sex';
        $formData = $this->createFormData($entity);

        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_setting_system_masterdata'),
            array(
                'admin_system_masterdata' => $formData
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->actual = $crawler->filter('div#result_list__body_inner')->html();
        $this->expected = $this->app['orm.em']->getRepository($formData['masterdata'])->find(1)->getName();
        $this->assertContains($this->expected, $this->actual);
    }

    public function testRoutingEdit()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_setting_system_masterdata_edit')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testEdit()
    {
        $entity = 'Eccube\Entity\Master\Sex';
        $formData = $this->createFormData($entity);

        $editForm = $this->createFormDataEdit($entity);

        $this->client->request(
            'POST',
            $this->app->url('admin_setting_system_masterdata_edit'),
            array(
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm
            )
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_setting_system_masterdata')));
        $data = end($editForm['data']);
        $this->expected = $data['name'];
        $this->actual = $this->app['orm.em']->getRepository($formData['masterdata'])->find($data['id'])->getName();
        $this->verify();
    }

    public function testEditRemove()
    {
        $entity = 'Eccube\Entity\Master\Sex';
        $formData = $this->createFormData($entity);

        $editForm = $this->createFormDataEdit($entity);
        $editForm['data'][1]['id'] = null;

        $this->client->request(
            'POST',
            $this->app->url('admin_setting_system_masterdata_edit'),
            array(
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm
            )
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_setting_system_masterdata')));
        $data = end($editForm['data']);
        $this->expected = $data['name'];
        $this->actual = $this->app['orm.em']->getRepository($formData['masterdata'])->find($data['id'])->getName();
        $this->verify();
    }

    public function createFormData($entity = 'Eccube\Entity\Master\Sex')
    {
        $formData = array(
            '_token' => 'dummy',
            'masterdata' => $entity
        );

        return $formData;
    }

    public function createFormDataEdit($entity = 'Eccube\Entity\Master\Sex')
    {
        $MasterData = $this->app['orm.em']->getRepository($entity)->findBy(array(), array('rank' => 'ASC'));
        $data = array();
        $rank = 1;
        $id = 1;
        foreach ($MasterData as $value) {
            $data[$value['rank']]['id'] = $value['id'];
            $data[$value['rank']]['name'] = $value['name'];
            $rank = $value['rank'] + 1;
            $id = $value['id'];
        }

        $data[$rank]['id'] = $id + 1;
        $data[$rank]['name'] = 'TestName';

        $editForm = array(
            '_token' => 'dummy',
            'data' => $data,
            'masterdata_name' => $entity,
        );

        return $editForm;
    }


}
