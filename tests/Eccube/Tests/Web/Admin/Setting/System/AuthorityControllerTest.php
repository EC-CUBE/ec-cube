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
 * Class AuthorityControllerTest
 * @package Eccube\Tests\Web\Admin\Setting\System
 */
class AuthorityControllerTest extends AbstractAdminWebTestCase
{
    /**
     * testRoutingAdminSettingSystemAuthority
     */
    public function testRoutingAdminSettingSystemAuthority()
    {
        $client = $this->client;
        $client->request(
            'GET',
            $this->app->url('admin_setting_system_authority')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * testRoutingAdminSettingSystemAuthorityPost
     */
    public function testRoutingAdminSettingSystemAuthorityPost()
    {
        $client = $this->client;

        $url = $this->app->url('admin_setting_system_authority');

        $client->request(
            'GET',
            $url,
            array(
                'form' => array(
                    'AuthorityRoles' => $this->createFormData(),
                    '_token' => 'dummy',
                ),
            )
        );

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * testAuthoritySubmit
     */
    public function testAuthoritySubmit()
    {
        $this->deleteAllRows(array('dtb_authority_role'));
        $AuthorityRole = $this->newTestAuthorityRole();
        $form = $this->createFormData($AuthorityRole);
        $url = $this->app->url('admin_setting_system_authority');
        // makes the POST request
        $this->client->request(
            'POST',
            $url,
            array(
                'form' => array(
                    'AuthorityRoles' => $form,
                    '_token' => 'dummy',
                ),
            )
        );

        $redirectUrl = $this->app->url('admin_setting_system_authority');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->expected = $form[0]['deny_url'];
        $this->actual = $AuthorityRole->getDenyUrl();
        $this->verify();
    }

    /**
     * count authority role = 0
     */
    public function testAuthoritySubmitWithoutAuthorityRole()
    {
        $this->deleteAllRows(array('dtb_authority_role'));
        $form = array(
            array(
                'Authority' => 0,
                'deny_url' => '/test2',
            ),
        );
        $url = $this->app->url('admin_setting_system_authority');
        // makes the POST request
        $this->client->request(
            'POST',
            $url,
            array(
                'form' => array(
                    'AuthorityRoles' => $form,
                    '_token' => 'dummy',
                ),
            )
        );

        $redirectUrl = $this->app->url('admin_setting_system_authority');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->expected = $form[0]['deny_url'];
        $AuthorityRole = $this->app['eccube.repository.authority_role']->findOneBy(array('deny_url' => $form[0]['deny_url']));
        $this->actual = $AuthorityRole->getDenyUrl();
        $this->verify();
    }

    /**
     * test Authority Submit Remove Authority
     */
    public function testAuthoritySubmitRemoveAuthority()
    {
        $this->deleteAllRows(array('dtb_authority_role'));
        $form = array(
            array(
                'Authority' => null,
                'deny_url' => null,
            ),
        );
        $AuthorityRole = $this->newTestAuthorityRole();

        $url = $this->app->url('admin_setting_system_authority');
        // makes the POST request
        $this->client->request(
            'POST',
            $url,
            array(
                'form' => array(
                    'AuthorityRoles' => $form,
                    '_token' => 'dummy',
                ),
            )
        );

        $redirectUrl = $this->app->url('admin_setting_system_authority');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->assertNull($AuthorityRole->getId());
    }

    /**
     * @return \Eccube\Entity\AuthorityRole
     */
    private function newTestAuthorityRole()
    {
        $TestCreator = $this->app['eccube.repository.member']->find(1);
        $AuthorityRole = new \Eccube\Entity\AuthorityRole();
        $Authority = $this->app['eccube.repository.master.authority']->find(0);
        $AuthorityRole->setAuthority($Authority);
        $AuthorityRole->setDenyUrl('/abab');
        $AuthorityRole->setCreator($TestCreator);

        $this->app['orm.em']->persist($AuthorityRole);
        $this->app['orm.em']->flush();

        return $AuthorityRole;
    }

    /**
     * @param null $AuthorityRole
     * @return array
     */
    protected function createFormData($AuthorityRole = null)
    {
        if (!$AuthorityRole) {
            $AuthorityRole = $this->newTestAuthorityRole();
        }

        $form = array(
            array(
                 'Authority' => $AuthorityRole->getAuthority()->getId(),
                 'deny_url' => '/test',
                ),
            );

        return $form;
    }
}
