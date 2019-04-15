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
     * Edit name test
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1884
     */
    public function testEditNameIsBlank()
    {
        $formData = $this->createFormData($this->entityTest);
        $editForm = $this->createFormDataEdit($this->entityTest);
        // expected value
        $this->expected = $editForm['data'][1]['name'];
        $editForm['data'][1]['name'] = null;

        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_setting_system_masterdata_edit'),
            array(
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm,
            )
        );
        $html = $crawler->html();
        $this->assertContains('※ 入力されていません。', $html);

        // Cannot save
        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $this->actual = $this->app['orm.em']->getRepository($entityName)->find($editForm['data'][1]['id'])->getName();
        $this->verify();
    }

    /**
     * New id is null
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1884
     */
    public function testNewIdIsNull()
    {
        $formData = $this->createFormData($this->entityTest);
        $editForm = $this->createFormDataEdit($this->entityTest);
        $id = count($editForm['data']) + 1;
        $editForm['data'][$id]['id'] = null;
        $editForm['data'][$id]['name'] = 'test';

        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_setting_system_masterdata_edit'),
            array(
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm,
            )
        );
        $html = $crawler->html();
        $this->assertContains('※ 入力されていません。', $html);

        // Cannot save
        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $actual = $this->app['orm.em']->getRepository($entityName)->find($id);
        $this->assertTrue(empty($actual));
    }

    /**
     * Add new name test
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1884
     */
    public function testNewNameIsBlank()
    {
        $formData = $this->createFormData($this->entityTest);
        $editForm = $this->createFormDataEdit($this->entityTest);
        $id = count($editForm['data']) + 1;
        $editForm['data'][$id]['id'] = $id;
        $editForm['data'][$id]['name'] = '';

        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_setting_system_masterdata_edit'),
            array(
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm,
            )
        );
        $html = $crawler->html();
        $this->assertContains('※ 入力されていません。', $html);

        // Cannot save
        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $actual = $this->app['orm.em']->getRepository($entityName)->find($id);
        $this->assertTrue(empty($actual));
    }

    /**
     * Edit id is null
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1884
     */
    public function testEditIdIsNull()
    {
        $formData = $this->createFormData($this->entityTest);
        $editForm = $this->createFormDataEdit($this->entityTest);
        $id = $editForm['data'][1]['id'];
        $editForm['data'][1]['id'] = null;

        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_setting_system_masterdata_edit'),
            array(
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm,
            )
        );
        $html = $crawler->html();
        $this->assertContains('※ 入力されていません。', $html);

        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $actual = $this->app['orm.em']->getRepository($entityName)->find($id);
        $this->assertFalse(empty($actual));
    }

    /**
     * Edit id is zero
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1884
     */
    public function testEditIdIsZero()
    {
        $formData = $this->createFormData($this->entityTest);
        $editForm = $this->createFormDataEdit($this->entityTest);
        $id = $editForm['data'][1]['id'];
        $editForm['data'][1]['id'] = 0;

        $this->client->request(
            'POST',
            $this->app->url('admin_setting_system_masterdata_edit'),
            array(
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm,
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_setting_system_masterdata_view', array('entity' => $formData['masterdata']))));

        $this->expected = $editForm['data'][1]['name'];

        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $this->actual = $this->app['orm.em']->getRepository($entityName)->find($id)->getName();
        $this->verify();

        // message check
        $outPut = $this->app['session']->getFlashBag()->get('eccube.admin.success');
        $this->actual = array_shift($outPut);
        $this->expected = 'admin.register.complete';
        $this->verify();
    }

    /**
     * Add new name zero test
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1884
     */
    public function testNewNameIsZero()
    {
        $formData = $this->createFormData($this->entityTest);
        $editForm = $this->createFormDataEdit($this->entityTest);
        $id = count($editForm['data']) + 1;
        $editForm['data'][$id]['id'] = $id;
        $editForm['data'][$id]['name'] = 0;

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
     * Edit test
     */
    public function testEditSuccess()
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
        $editForm['data'][1]['name'] = null;

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
