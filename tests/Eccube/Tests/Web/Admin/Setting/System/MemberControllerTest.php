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

class MemberControllerTest extends AbstractAdminWebTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testRoutingAdminSettingSystemMember()
    {
        $this->client->request('GET',
            $this->app->url('admin_setting_system_member')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminSettingSystemMemberNew()
    {
        $this->client->request('GET',
            $this->app->url('admin_setting_system_member_new')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminSettingSystemMemberEdit()
    {
        // before
        $TestMember = $this->newTestMember();
        $this->app['orm.em']->persist($TestMember);
        $this->app['orm.em']->flush();
        $test_member_id = $this->app['eccube.repository.member']
            ->findOneBy(array(
                'login_id' => $TestMember->getLoginId()
            ))
            ->getId();

        // main
        $this->client->request('GET',
            $this->app->url('admin_setting_system_member_edit', array('id' => $test_member_id))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // after
        $this->app['orm.em']->remove($TestMember);
        $this->app['orm.em']->flush();
    }

    public function testRoutingAdminSettingSystemMemberDelete()
    {
        // before
        $TestMember = $this->newTestMember();
        $this->app['orm.em']->persist($TestMember);
        $this->app['orm.em']->flush();
        $test_member_id = $this->app['eccube.repository.member']
            ->findOneBy(array(
                'login_id' => $TestMember->getLoginId()
            ))
            ->getId();

        // main
        $redirectUrl = $this->app->url('admin_setting_system_member');
        $this->client->request('GET',
            $this->app->url('admin_setting_system_member_delete',array('id' => $test_member_id))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // after
        $this->app['orm.em']->remove($TestMember);
        $this->app['orm.em']->flush();
    }

    public function testRoutingAdminSettingSystemMemberUp()
    {
        // before
        $TestMember = $this->newTestMember();
        $this->app['orm.em']->persist($TestMember);
        $this->app['orm.em']->flush();
        $test_member_id = $this->app['eccube.repository.member']
            ->findOneBy(array(
                'login_id' => $TestMember->getLoginId()
            ))
            ->getId();

        // main
        $redirectUrl = $this->app->url('admin_setting_system_member');
        $this->client->request('POST',
            $this->app->url('admin_setting_system_member_up', array('id' => $test_member_id))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // after
        $this->app['orm.em']->remove($TestMember);
        $this->app['orm.em']->flush();
    }

    public function testRoutingAdminSettingSystemMemberDown()
    {
        // before
        $TestMember = $this->newTestMember();
        $this->app['orm.em']->persist($TestMember);
        $this->app['orm.em']->flush();
        $test_member_id = $this->app['eccube.repository.member']
            ->findOneBy(array(
                'login_id' => $TestMember->getLoginId()
            ))
            ->getId();

        // main
        $redirectUrl = $this->app->url('admin_setting_system_member');
        $this->client->request('POST',
            $this->app->url('admin_setting_system_member_down', array('id' => $test_member_id))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // after
        $this->app['orm.em']->remove($TestMember);
        $this->app['orm.em']->flush();
    }

    private function newTestMember()
    {
        $Authority = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Master\Authority')
            ->find(0);
        $Work = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Master\Work')
            ->find(1);
        $TestMember = new \Eccube\Entity\Member();
        $TestMember->setName('takahashi')
            ->setDepartment('EC-CUBE事業部')
            ->setLoginId('takahashi')
            ->setPassword('password')
            ->setRank(100)
            ->setDelFlg(false)
            ->setSalt('abcdefg')
            ->setAuthority($Authority)
            ->setWork($Work);

        return $TestMember;
    }
}
