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

/**
 * Class MasterdataControllerTest
 * @package Eccube\Tests\Web\Admin\Setting\System
 */
class MasterdataControllerTest extends AbstractAdminWebTestCase
{
    protected $entityTest = 'Eccube-Entity-Master-Sex';

    /**
     * routing test
     */
    public function testRouting()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_setting_system_masterdata')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Change test
     */
    public function testSelect()
    {
        $formData = $this->createFormData($this->entityTest);

        $this->client->request(
            'POST',
            $this->app->url('admin_setting_system_masterdata'),
            array(
                'admin_system_masterdata' => $formData,
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_setting_system_masterdata_view', array('entity' => $formData['masterdata']))));

        $crawler = $this->client->request(
            'GET',
            $this->app->url('admin_setting_system_masterdata_view', array('entity' => $formData['masterdata']))
        );
        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $this->actual = $crawler->filter('div#result_list__body_inner')->html();
        $this->expected = $this->app['orm.em']->getRepository($entityName)->find(1)->getName();
        $this->assertContains($this->expected, $this->actual);
    }

    /**
     * Edit test
     */
    public function testRoutingEdit()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_setting_system_masterdata_edit')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Edit test
     */
    public function testEdit()
    {
        $formData = $this->createFormData($this->entityTest);
        $editForm = $this->createFormDataEdit($this->entityTest);

        $this->client->request(
            'POST',
            $this->app->url('admin_setting_system_masterdata_edit'),
            array(
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm,
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_setting_system_masterdata_view', array('entity' => $formData['masterdata']))));

        $data = end($editForm['data']);
        $this->expected = $data['name'];

        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $this->actual = $this->app['orm.em']->getRepository($entityName)->find($data['id'])->getName();
        $this->verify();

        // message check
        $outPut = $this->app['session']->getFlashBag()->get('eccube.admin.success');
        $this->actual = array_shift($outPut);
        $this->expected = 'admin.register.complete';
        $this->verify();
    }

    /**
     * Edit remove test
     */
    public function testEditRemove()
    {
        $formData = $this->createFormData($this->entityTest);
        $editForm = $this->createFormDataEdit($this->entityTest);
        $editForm['data'][1]['id'] = null;

        $this->client->request(
            'POST',
            $this->app->url('admin_setting_system_masterdata_edit'),
            array(
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm,
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_setting_system_masterdata_view', array('entity' => $formData['masterdata']))));

        $data = end($editForm['data']);
        $this->expected = $data['name'];

        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $this->actual = $this->app['orm.em']->getRepository($entityName)->find($data['id'])->getName();
        $this->verify();

        // message check
        $outPut = $this->app['session']->getFlashBag()->get('eccube.admin.success');
        $this->actual = array_shift($outPut);
        $this->expected = 'admin.register.complete';
        $this->verify();
    }

    /**
     * @param string $entity
     * @return array
     */
    public function createFormData($entity = 'Eccube-Entity-Master-Sex')
    {
        $formData = array(
            '_token' => 'dummy',
            'masterdata' => $entity,
        );

        return $formData;
    }

    /**
     * @param string $entity
     * @return array
     */
    public function createFormDataEdit($entity = 'Eccube-Entity-Master-Sex')
    {
        $entityName = str_replace('-', '\\', $entity);
        $masterData = $this->app['orm.em']->getRepository($entityName)->findBy(array(), array('rank' => 'ASC'));
        $data = array();
        $rank = 1;
        $id = 1;
        foreach ($masterData as $value) {
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
