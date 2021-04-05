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

use Eccube\Repository\AuthorityRoleRepository;
use Eccube\Repository\Master\AuthorityRepository;
use Eccube\Repository\MemberRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * Class AuthorityControllerTest
 */
class AuthorityControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var MemberRepository;
     */
    protected $memberRepository;

    /**
     * @var AuthorityRepository
     */
    protected $authorityMasterRepository;

    /**
     * @var AuthorityRoleRepository
     */
    protected $authorityRoleRepository;

    public function setUp()
    {
        parent::setUp();

        $this->memberRepository = $this->entityManager->getRepository(\Eccube\Entity\Member::class);
        $this->authorityMasterRepository = $this->entityManager->getRepository(\Eccube\Entity\Master\Authority::class);
        $this->authorityRoleRepository = $this->entityManager->getRepository(\Eccube\Entity\AuthorityRole::class);
    }

    /**
     * testRoutingAdminSettingSystemAuthority
     */
    public function testRoutingAdminSettingSystemAuthority()
    {
        $client = $this->client;
        $client->request(
            'GET',
            $this->generateUrl('admin_setting_system_authority')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * testRoutingAdminSettingSystemAuthorityPost
     */
    public function testRoutingAdminSettingSystemAuthorityPost()
    {
        $client = $this->client;

        $url = $this->generateUrl('admin_setting_system_authority');

        $client->request(
            'GET',
            $url,
            [
                'form' => [
                    'AuthorityRoles' => $this->createFormData(),
                    '_token' => 'dummy',
                ],
            ]
        );

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * testAuthoritySubmit
     */
    public function testAuthoritySubmit()
    {
        $this->deleteAllRows(['dtb_authority_role']);
        $AuthorityRole = $this->newTestAuthorityRole();
        $form = $this->createFormData($AuthorityRole);
        $url = $this->generateUrl('admin_setting_system_authority');
        // makes the POST request
        $this->client->request(
            'POST',
            $url,
            [
                'form' => [
                    'AuthorityRoles' => $form,
                    '_token' => 'dummy',
                ],
            ]
        );

        $redirectUrl = $this->generateUrl('admin_setting_system_authority');
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
        $this->deleteAllRows(['dtb_authority_role']);
        $form = [
            [
                'Authority' => 0,
                'deny_url' => '/test2',
            ],
        ];
        $url = $this->generateUrl('admin_setting_system_authority');
        // makes the POST request
        $this->client->request(
            'POST',
            $url,
            [
                'form' => [
                    'AuthorityRoles' => $form,
                    '_token' => 'dummy',
                ],
            ]
        );

        $redirectUrl = $this->generateUrl('admin_setting_system_authority');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->expected = $form[0]['deny_url'];
        $AuthorityRole = $this->authorityRoleRepository->findOneBy(['deny_url' => $form[0]['deny_url']]);
        $this->actual = $AuthorityRole->getDenyUrl();
        $this->verify();
    }

    /**
     * test Authority Submit Remove Authority
     */
    public function testAuthoritySubmitRemoveAuthority()
    {
        $this->deleteAllRows(['dtb_authority_role']);
        $form = [
            [
                'Authority' => null,
                'deny_url' => null,
            ],
        ];
        $AuthorityRole = $this->newTestAuthorityRole();

        $url = $this->generateUrl('admin_setting_system_authority');
        // makes the POST request
        $this->client->request(
            'POST',
            $url,
            [
                'form' => [
                    'AuthorityRoles' => $form,
                    '_token' => 'dummy',
                ],
            ]
        );

        $redirectUrl = $this->generateUrl('admin_setting_system_authority');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->assertNull($AuthorityRole->getId());
    }

    /**
     * @return \Eccube\Entity\AuthorityRole
     */
    private function newTestAuthorityRole()
    {
        $TestCreator = $this->memberRepository->find(1);
        $AuthorityRole = new \Eccube\Entity\AuthorityRole();
        $Authority = $this->authorityMasterRepository->find(0);
        $AuthorityRole->setAuthority($Authority);
        $AuthorityRole->setDenyUrl('/abab');
        $AuthorityRole->setCreator($TestCreator);

        $this->entityManager->persist($AuthorityRole);
        $this->entityManager->flush();

        return $AuthorityRole;
    }

    /**
     * @param null $AuthorityRole
     *
     * @return array
     */
    protected function createFormData($AuthorityRole = null)
    {
        if (!$AuthorityRole) {
            $AuthorityRole = $this->newTestAuthorityRole();
        }

        $form = [
            [
                 'Authority' => $AuthorityRole->getAuthority()->getId(),
                 'deny_url' => '/test',
                ],
            ];

        return $form;
    }
}
