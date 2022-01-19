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

namespace Eccube\Tests\Web\Admin\Setting\System;

use Eccube\Repository\MemberRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

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

        $this->memberRepository = $this->entityManager->getRepository(\Eccube\Entity\Member::class);
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

    public function testMemberEditFail()
    {
        // before
        $memberId = 99999;

        // main
        $this->client->request('GET',
            $this->generateUrl('admin_setting_system_member_edit', ['id' => $memberId])
        );

        $this->expected = 404;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();
    }

    public function testMemberNewSubmit()
    {
        // before
        $formData = $this->createFormData();

        // main
        $this->client->request('POST',
            $this->generateUrl('admin_setting_system_member_new'),
            [
                'admin_member' => $formData,
            ]
        );

        $Member = $this->memberRepository->findOneBy(['login_id' => $formData['login_id']]);

        $redirectUrl = $this->generateUrl('admin_setting_system_member_edit', ['id' => $Member->getId()]);
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

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
            $this->generateUrl('admin_setting_system_member_new'),
            [
                'admin_member' => $formData,
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testMemberEditSubmit()
    {
        // before
        $formData = $this->createFormData();
        $formData['plain_password'] = [
            'first' => '**********',
            'second' => '**********',
        ];
        $Member = $this->createMember();
        $loginId = $Member->getLoginId();
        $Member->setPassword('**********');
        $this->entityManager->persist($Member);
        $this->entityManager->flush();
        $mid = $Member->getId();

        // main
        $this->client->request('POST',
            $this->generateUrl('admin_setting_system_member_edit', ['id' => $mid]),
            ['admin_member' => $formData]
        );

        $redirectUrl = $this->generateUrl('admin_setting_system_member_edit', ['id' => $Member->getId()]);
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = $Member->getName();
        $this->expected = $formData['name'];
        $this->verify();

        // login_idが変更されないことを確認
        $this->assertSame($Member->getLoginId(), $loginId);
    }

    public function testMemberEditSubmitFail()
    {
        // before
        $formData = $this->createFormData();
        $formData['name'] = '';
        $Member = $this->createMember();
        $Member->setPassword('**********');
        $this->entityManager->persist($Member);
        $this->entityManager->flush();
        $mid = $Member->getId();

        // main
        $this->client->request('POST',
            $this->generateUrl('admin_setting_system_member_edit', ['id' => $mid]),
            ['admin_member' => $formData]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testMemberUpNotFoundMember()
    {
        // before
        $mid = 9999;

        // main
        $this->client->request('PUT',
            $this->generateUrl('admin_setting_system_member_up', ['id' => $mid])
        );

        $this->expected = 404;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();
    }

    public function testMemberUpSuccess()
    {
        // before
        $MemberOne = $this->createMember('test1');
        $this->entityManager->persist($MemberOne);
        $this->entityManager->flush();
        $MemberTwo = $this->createMember('test2');
        $this->entityManager->persist($MemberTwo);
        $this->entityManager->flush();

        $oldSortNo = $MemberOne->getSortNo();
        $newSortNo = $MemberTwo->getSortNo();
        $mid = $MemberOne->getId();
        // main
        $this->client->request('PUT',
            $this->generateUrl('admin_setting_system_member_up', ['id' => $mid])
        );

        $redirectUrl = $this->generateUrl('admin_setting_system_member');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = [$MemberOne->getSortNo(), $MemberTwo->getSortNo()];
        $this->expected = [$newSortNo, $oldSortNo];
        $this->verify();
    }

    public function testMemberDownNotFoundMember()
    {
        // before
        $mid = 9999;

        // main
        $this->client->request('PUT',
            $this->generateUrl('admin_setting_system_member_down', ['id' => $mid])
        );

        $this->expected = 404;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();
    }

    public function testMemberDownFail()
    {
        // before
        $Member = $this->memberRepository->findOneBy(['sort_no' => 1]);
        $mid = $Member->getId();
        $oldSortNo = $Member->getSortNo();
        // main
        $this->client->request('PUT',
            $this->generateUrl('admin_setting_system_member_down', ['id' => $mid])
        );

        $redirectUrl = $this->generateUrl('admin_setting_system_member');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = $Member->getSortNo();
        $this->expected = $oldSortNo;
        $this->verify();
    }

    public function testMemberDownSuccess()
    {
        // before
        $MemberOne = $this->createMember('test1');
        $this->entityManager->persist($MemberOne);
        $this->entityManager->flush();
        $MemberTwo = $this->createMember('test2');
        $this->entityManager->persist($MemberTwo);
        $this->entityManager->flush();

        $oldSortNo = $MemberOne->getSortNo();
        $newSortNo = $MemberTwo->getSortNo();
        $mid = $MemberTwo->getId();
        // main
        $this->client->request('PUT',
            $this->generateUrl('admin_setting_system_member_down', ['id' => $mid])
        );

        $redirectUrl = $this->generateUrl('admin_setting_system_member');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = [$MemberOne->getSortNo(), $MemberTwo->getSortNo()];
        $this->expected = [$newSortNo, $oldSortNo];
        $this->verify();
    }

    public function testMemberDeleteIdNotFound()
    {
        // before
        $mid = 99999;

        // main
        $this->client->request('DELETE',
            $this->generateUrl('admin_setting_system_member_delete', ['id' => $mid])
        );

        $this->expected = 404;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();
    }

    protected function createFormData()
    {
        $faker = $this->getFaker();
        $formData = [
            '_token' => 'dummy',
            'name' => $faker->word,
            'department' => $faker->word,
            'login_id' => 'logintest',
            'plain_password' => [
                'first' => 'password',
                'second' => 'password',
            ],
            'Authority' => rand(0, 1),
            'Work' => rand(0, 1),
        ];

        return $formData;
    }
}
