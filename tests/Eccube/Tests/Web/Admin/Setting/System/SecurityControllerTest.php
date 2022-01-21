<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * @group cache-clear
 */
class SecurityControllerTest extends AbstractAdminWebTestCase
{
    protected $envFile;

    protected $env;

    public function setUp()
    {
        parent::setUp();

        $this->envFile = self::$container->getParameter('kernel.project_dir').'/.env';
        if (file_exists($this->envFile)) {
            $this->env = file_get_contents($this->envFile);
        }
    }

    public function tearDown()
    {
        if ($this->env) {
            file_put_contents($this->envFile, $this->env);
        }

        parent::tearDown();
    }

    /**
     * Routing test
     */
    public function testRouting()
    {
        $this->client->request('GET', $this->generateUrl('admin_setting_system_security'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Submit test
     *
     * @group cache-clear
     */
    public function testSubmit()
    {
        $formData = $this->createFormData();

        $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_system_security'),
            [
                'admin_security' => $formData,
            ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        // Message
        $outPut = self::$container->get('session')->getFlashBag()->get('eccube.admin.success');
        $this->actual = array_shift($outPut);
        $this->expected = 'admin.setting.system.security.admin_url_changed';
        $this->verify();

        self::assertRegexp('/ECCUBE_ADMIN_ROUTE='.$formData['admin_route_dir'].'/', file_get_contents($this->envFile));
    }

    /**
     * Submit when empty
     */
    public function testSubmitEmpty()
    {
        $formData = $this->createFormData();
        $formData['admin_route_dir'] = null;
        $formData['admin_allow_hosts'] = null;
        $formData['admin_deny_hosts'] = null;
        $formData['force_ssl'] = null;

        $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_system_security'),
            [
                'admin_security' => $formData,
            ]
        );

        self::assertTrue($this->client->getResponse()->isSuccessful());

        $newEnv = file_exists($this->envFile) ? file_get_contents($this->envFile) : null;
        self::assertSame($this->env, $newEnv);
    }

    /**
     * Submit form
     *
     * @return array
     */
    public function createFormData()
    {
        $formData = [
            '_token' => 'dummy',
            'admin_route_dir' => 'admintest',
            'admin_allow_hosts' => '127.0.0.1',
            'admin_deny_hosts' => '127.0.0.1',
            'trusted_hosts' => '^127\.0\.0\.1$,^localhost$',
        ];

        return $formData;
    }
}
