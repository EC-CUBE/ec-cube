<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Web\Admin\Setting\System;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class MasterdataControllerTest
 */
class MasterdataControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var Session
     */
    private $session;

    public function setUp()
    {
        parent::setUp();

        $this->session = $this->container->get('session');
    }

    protected $entityTest = 'Eccube-Entity-Master-Sex';

    /**
     * routing test
     */
    public function testRouting()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_setting_system_masterdata')
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
            $this->generateUrl('admin_setting_system_masterdata'),
            [
                'admin_system_masterdata' => $formData,
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_setting_system_masterdata_view', ['entity' => $formData['masterdata']])));

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_setting_system_masterdata_view', ['entity' => $formData['masterdata']])
        );
        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $this->actual = $crawler->filter('#result_list__list')->html();
        $this->expected = $this->entityManager->getRepository($entityName)->find(1)->getName();
        $this->assertContains($this->expected, $this->actual);
    }

    /**
     * Edit test
     */
    public function testRoutingEdit()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_setting_system_masterdata_edit')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Edit name test
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1884
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
            $this->generateUrl('admin_setting_system_masterdata_edit'),
            [
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm,
            ]
        );
        $html = $crawler->html();
        $this->assertContains('入力されていません。', $html);

        // Cannot save
        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $this->actual = $this->entityManager->getRepository($entityName)->find($editForm['data'][1]['id'])->getName();
        $this->verify();
    }

    /**
     * New id is null
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1884
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
            $this->generateUrl('admin_setting_system_masterdata_edit'),
            [
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm,
            ]
        );
        $html = $crawler->html();
        $this->assertContains('入力されていません。', $html);

        // Cannot save
        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $actual = $this->entityManager->getRepository($entityName)->find($id);
        $this->assertTrue(empty($actual));
    }

    /**
     * Add new name test
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1884
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
            $this->generateUrl('admin_setting_system_masterdata_edit'),
            [
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm,
            ]
        );
        $html = $crawler->html();
        $this->assertContains('入力されていません。', $html);

        // Cannot save
        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $actual = $this->entityManager->getRepository($entityName)->find($id);
        $this->assertTrue(empty($actual));
    }

    /**
     * Edit id is null
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1884
     */
    public function testEditIdIsNull()
    {
        $formData = $this->createFormData($this->entityTest);
        $editForm = $this->createFormDataEdit($this->entityTest);
        $id = $editForm['data'][1]['id'];
        $editForm['data'][1]['id'] = null;

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_system_masterdata_edit'),
            [
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm,
            ]
        );
        $html = $crawler->html();
        $this->assertContains('入力されていません。', $html);

        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $actual = $this->entityManager->getRepository($entityName)->find($id);
        $this->assertFalse(empty($actual));
    }

    /**
     * Edit id is zero
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1884
     */
    public function testEditIdIsZero()
    {
        $formData = $this->createFormData($this->entityTest);
        $editForm = $this->createFormDataEdit($this->entityTest);
        $id = $editForm['data'][1]['id'];
        $editForm['data'][1]['id'] = 0;

        $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_system_masterdata_edit'),
            [
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm,
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_setting_system_masterdata_view', ['entity' => $formData['masterdata']])));

        $this->expected = $editForm['data'][1]['name'];

        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $this->actual = $this->entityManager->getRepository($entityName)->find($id)->getName();
        $this->verify();

        // message check
        $outPut = $this->session->getFlashBag()->get('eccube.admin.success');
        $this->actual = array_shift($outPut);
        $this->expected = 'admin.register.complete';
        $this->verify();
    }

    /**
     * Add new name zero test
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1884
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
            $this->generateUrl('admin_setting_system_masterdata_edit'),
            [
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm,
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_setting_system_masterdata_view', ['entity' => $formData['masterdata']])));

        $data = end($editForm['data']);
        $this->expected = $data['name'];

        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $this->actual = $this->entityManager->getRepository($entityName)->find($data['id'])->getName();
        $this->verify();

        // message check
        $outPut = $this->session->getFlashBag()->get('eccube.admin.success');
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
            $this->generateUrl('admin_setting_system_masterdata_edit'),
            [
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm,
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_setting_system_masterdata_view', ['entity' => $formData['masterdata']])));

        $data = end($editForm['data']);
        $this->expected = $data['name'];

        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $this->actual = $this->entityManager->getRepository($entityName)->find($data['id'])->getName();
        $this->verify();

        // message check
        $outPut = $this->session->getFlashBag()->get('eccube.admin.success');
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
            $this->generateUrl('admin_setting_system_masterdata_edit'),
            [
                'admin_system_masterdata' => $formData,
                'admin_system_masterdata_edit' => $editForm,
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_setting_system_masterdata_view', ['entity' => $formData['masterdata']])));

        $data = end($editForm['data']);
        $this->expected = $data['name'];

        $entityName = str_replace('-', '\\', $formData['masterdata']);
        $this->actual = $this->entityManager->getRepository($entityName)->find($data['id'])->getName();
        $this->verify();

        // message check
        $outPut = $this->session->getFlashBag()->get('eccube.admin.success');
        $this->actual = array_shift($outPut);
        $this->expected = 'admin.register.complete';
        $this->verify();
    }

    /**
     * @param string $entity
     *
     * @return array
     */
    protected function createFormData($entity = 'Eccube-Entity-Master-Sex')
    {
        $formData = [
            '_token' => 'dummy',
            'masterdata' => $entity,
        ];

        return $formData;
    }

    /**
     * @param string $entity
     *
     * @return array
     */
    protected function createFormDataEdit($entity = 'Eccube-Entity-Master-Sex')
    {
        $entityName = str_replace('-', '\\', $entity);
        $masterData = $this->entityManager->getRepository($entityName)->findBy([], ['sort_no' => 'ASC']);
        $data = [];
        $sortNo = 1;
        $id = 1;
        foreach ($masterData as $value) {
            $data[$value['sort_no']]['id'] = $value['id'];
            $data[$value['sort_no']]['name'] = $value['name'];
            $sortNo = $value['sort_no'] + 1;
            $id = $value['id'];
        }

        $data[$sortNo]['id'] = $id + 1;
        $data[$sortNo]['name'] = 'TestName';

        $editForm = [
            '_token' => 'dummy',
            'data' => $data,
            'masterdata_name' => $entity,
        ];

        return $editForm;
    }
}
