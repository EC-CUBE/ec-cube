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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Eccube\Repository\MemberRepository;

class MemberControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var MemberRepository
     */
    protected $memberRepository;

    /**
     * @{@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->memberRepository = $this->container->get(MemberRepository::class);
    }

    public function testRoutingAdminSettingSystemMember()
    {
        $this->client->request('GET', $this->generateUrl('admin_setting_system_member'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminSettingSystemMemberNew()
    {
        $this->client->request('GET', $this->generateUrl('admin_setting_system_member_new'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminSettingSystemMemberEdit()
    {
        // before
        $TestMember = $this->createMember();
        $this->entityManager->persist($TestMember);
        $this->entityManager->flush();
        $memberId = $this->memberRepository
            ->findOneBy(['login_id' => $TestMember->getLoginId()])
            ->getId();

        // main
        $this->client->request('GET',
            $this->generateUrl('admin_setting_system_member_edit', ['id' => $memberId])
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminSettingSystemMemberDelete()
    {
        // before
        $TestMember = $this->createMember();
        $this->entityManager->persist($TestMember);
        $this->entityManager->flush();
        $test_member_id = $this->memberRepository
            ->findOneBy(['login_id' => $TestMember->getLoginId()])
            ->getId();

        // main
        $redirectUrl = $this->generateUrl('admin_setting_system_member');
        $this->client->request('DELETE',
            $this->generateUrl('admin_setting_system_member_delete', ['id' => $test_member_id])
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    public function testRoutingAdminSettingSystemMemberUp()
    {
        // before
        $TestMember = $this->createMember();
        $this->entityManager->persist($TestMember);
        $this->entityManager->flush();
        $memberId = $this->memberRepository
            ->findOneBy(['login_id' => $TestMember->getLoginId()])
            ->getId();

        // main
        $redirectUrl = $this->generateUrl('admin_setting_system_member');
        $this->client->request('PUT',
            $this->generateUrl('admin_setting_system_member_up', ['id' => $memberId])
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    public function testRoutingAdminSettingSystemMemberDown()
    {
        // before
        $TestMember = $this->createMember();
        $this->entityManager->persist($TestMember);
        $this->entityManager->flush();
        $test_member_id = $this->memberRepository
            ->findOneBy(['login_id' => $TestMember->getLoginId()])
            ->getId();

        // main
        $redirectUrl = $this->generateUrl('admin_setting_system_member');
        $this->client->request('PUT',
            $this->generateUrl('admin_setting_system_member_down', ['id' => $test_member_id])
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testMemberEditFail()
    {
        // before
        $test_member_id = 99999;

        // main
        $this->client->request('GET',
            $this->app->url('admin_setting_system_member_edit', array('id' => $test_member_id))
        );
        $this->fail();
    }

    public function testMemberNewSubmit()
    {
        // before
        $formData = $this->createFormData();

        // main
        $this->client->request('POST',
            $this->app->url('admin_setting_system_member_new'),
            array(
                'admin_member' => $formData
            )
        );

        $redirectUrl = $this->app->url('admin_setting_system_member');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $Member = $this->app['eccube.repository.member']->findOneBy(array('login_id' => $formData['login_id']));
        $this->actual = $Member->getLoginId();
        $this->expected = $formData['login_id'];
        $this->verify();
    }

    public function testMemberNewSubmitFail()
    {
        // before
        $formData = $this->createFormData();
        $formData['login_id'] = '';
        // main
        $this->client->request('POST',
            $this->app->url('admin_setting_system_member_new'),
            array(
                'admin_member' => $formData
            )
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testMemberEditSubmit()
    {
        // before
        $formData = $this->createFormData();
        $formData['password'] = array(
            'first' => '**********',
            'second' => '**********',
        );
        $Member = $this->createMember();
        $Member->setPassword('**********');
        $this->app['orm.em']->persist($Member);
        $this->app['orm.em']->flush();
        $mid = $Member->getId();

        // main
        $this->client->request('POST',
            $this->app->url('admin_setting_system_member_edit', array('id' => $mid)),
            array(
                'admin_member' => $formData
            )
        );

        $redirectUrl = $this->app->url('admin_setting_system_member');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = $Member->getLoginId();
        $this->expected = $formData['login_id'];
        $this->verify();
    }

    public function testMemberEditSubmitFail()
    {
        // before
        $formData = $this->createFormData();
        $formData['login_id'] = '';
        $Member = $this->createMember();
        $Member->setPassword('**********');
        $this->app['orm.em']->persist($Member);
        $this->app['orm.em']->flush();
        $mid = $Member->getId();

        // main
        $this->client->request('POST',
            $this->app->url('admin_setting_system_member_edit', array('id' => $mid)),
            array(
                'admin_member' => $formData
            )
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testMemberUpNotFoundMember()
    {
        // before
        $mid = 9999;

        // main
        $this->client->request('PUT',
            $this->app->url('admin_setting_system_member_up', array('id' => $mid))
        );
        $this->fail();
    }

    public function testMemberUpSuccess()
    {
        // before
        $MemberOne = $this->createMember('test1');
        $this->app['orm.em']->persist($MemberOne);
        $this->app['orm.em']->flush();
        $MemberTwo = $this->createMember('test2');
        $this->app['orm.em']->persist($MemberTwo);
        $this->app['orm.em']->flush();

        $oldSortNo = $MemberOne->getSortNo();
        $newSortNo = $MemberTwo->getSortNo();
        $mid = $MemberOne->getId();
        // main
        $this->client->request('PUT',
            $this->app->url('admin_setting_system_member_up', array('id' => $mid))
        );

        $redirectUrl = $this->app->url('admin_setting_system_member');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = array($MemberOne->getSortNo(), $MemberTwo->getSortNo());
        $this->expected = array($newSortNo, $oldSortNo);
        $this->verify();
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testMemberDownNotFoundMember()
    {
        // before
        $mid = 9999;

        // main
        $this->client->request('PUT',
            $this->app->url('admin_setting_system_member_down', array('id' => $mid))
        );
        $this->fail();
    }

    public function testMemberDownFail()
    {
        // before
        $Member = $this->app['eccube.repository.member']->findOneBy(array('sort_no' => 1));
        $mid = $Member->getId();
        $oldSortNo = $Member->getSortNo();
        // main
        $this->client->request('PUT',
            $this->app->url('admin_setting_system_member_down', array('id' => $mid))
        );

        $redirectUrl = $this->app->url('admin_setting_system_member');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = $Member->getSortNo();
        $this->expected = $oldSortNo;
        $this->verify();
    }

    public function testMemberDownSuccess()
    {
        // before
        $MemberOne = $this->createMember('test1');
        $this->app['orm.em']->persist($MemberOne);
        $this->app['orm.em']->flush();
        $MemberTwo = $this->createMember('test2');
        $this->app['orm.em']->persist($MemberTwo);
        $this->app['orm.em']->flush();

        $oldSortNo = $MemberOne->getSortNo();
        $newSortNo = $MemberTwo->getSortNo();
        $mid = $MemberTwo->getId();
        // main
        $this->client->request('PUT',
            $this->app->url('admin_setting_system_member_down', array('id' => $mid))
        );

        $redirectUrl = $this->app->url('admin_setting_system_member');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = array($MemberOne->getSortNo(), $MemberTwo->getSortNo());
        $this->expected = array($newSortNo, $oldSortNo);
        $this->verify();
    }

    public function testMemberDeleteIdNotFound()
    {
        // before
        $mid = 99999;
        try {
            // main
            $this->client->request(
                'DELETE',
                $this->app->url('admin_setting_system_member_delete', array('id' => $mid))
            );
            $this->fail();
        } catch (NotFoundHttpException $e) {

        }
    }

    protected function createFormData()
    {
        $faker = $this->getFaker();
        $formData = array(
            '_token' => 'dummy',
            'name' => $faker->word,
            'department' => $faker->word,
            'login_id' => 'logintest',
            'password' => array(
                'first' => 'password',
                'second' => 'password',
            ),
            'Authority' => rand(0, 1),
            'Work' => rand(0, 1),
        );

        return $formData;
    }
}
