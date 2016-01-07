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

class AuthorityiControllerTest extends AbstractAdminWebTestCase
{

    public function testRoutingAdminSettingSystemAuthority()
    {
        $client = $this->client;
        $client->request('GET',
            $this->app->url('admin_setting_system_authority')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminSettingSystemAuthorityPost()
    {
        $client = $this->client;

        $url = $this->app->url('admin_setting_system_authority');

        $crawler = $client->request('GET', $url, array(
            'form' => array(
                'AuthorityRoles' => $this->createFormData(),
                '_token' => 'dummy',
            ),
        ));

        // makes the POST request
        $crawler = $this->client->request('POST', $url, array(
            'form' => array(
                'AuthorityRoles' => $this->createFormData(),
                '_token' => 'dummy',
            ),
        ));


        $this->expected = '/abab';
        $values = $crawler->filter('input[name="form[AuthorityRoles][0][deny_url]"]')->extract(array('value'));
         $this->actual = $values[0];
        $this->verify();

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    private function newTestAuthorityRole()
    {
        $TestCreator = $this->app['eccube.repository.member']->find(1);
        $AuthorityRole = new \Eccube\Entity\AuthorityRole();
        $Authority = $this->app['eccube.repository.master.authority']->find(0);
        $AuthorityRole->setAuthority($Authority);
        $AuthorityRole->setDenyUrl('/abab');
        $AuthorityRole->setCreator($TestCreator);

        return $AuthorityRole;
    }

    protected function createFormData()
    {

        $AuthorityRole = $this->newTestAuthorityRole();
        $form = array(
            array(
                 'Authority' => $AuthorityRole->getAuthority(),
                 'deny_url' => $AuthorityRole->getDenyUrl(),
                ),
            );

        return $form;
    }

}