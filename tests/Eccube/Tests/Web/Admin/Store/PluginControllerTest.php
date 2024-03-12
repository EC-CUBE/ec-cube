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

namespace Eccube\Tests\Web\Admin\Store;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * @group cache-clear
 */
class PluginControllerTest extends AbstractAdminWebTestCase
{
    public function testRoutingAuthentication()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_store_authentication_setting')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testSubmit()
    {
        $form = [
            '_token' => 'dummy',
            'authentication_key' => 'abcxyzABCXYZ123098',
            'php_path' => '/usr/bin/php',
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('admin_store_authentication_setting'),
            [
                'admin_authentication' => $form,
            ]
        );

        $this->expected = $form['php_path'];
        $this->actual = $this->entityManager->getRepository(\Eccube\Entity\BaseInfo::class)->get()->getPhpPath();
        $this->verify();
    }
}
