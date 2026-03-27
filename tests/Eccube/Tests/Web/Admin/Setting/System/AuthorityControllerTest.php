<?php

declare(strict_types=1);

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

use Eccube\Entity\AuthorityRole;
use Eccube\Entity\Master\Authority;
use Eccube\Entity\Member;
use Eccube\Repository\AuthorityRoleRepository;
use Eccube\Repository\Master\AuthorityRepository;
use Eccube\Repository\MemberRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AuthorityControllerTest
 */
final class AuthorityControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var MemberRepository;
     */
    protected $memberRepository;

    protected ?AuthorityRepository $authorityMasterRepository = null;

    protected ?AuthorityRoleRepository $authorityRoleRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->memberRepository = $this->entityManager->getRepository(Member::class);
        $this->authorityMasterRepository = $this->entityManager->getRepository(Authority::class);
        $this->authorityRoleRepository = $this->entityManager->getRepository(AuthorityRole::class);
    }

    /**
     * testRoutingAdminSettingSystemAuthority
     */
    public function testRoutingAdminSettingSystemAuthority()
    {
        $client = $this->client;
        $client->request(
            Request::METHOD_GET,
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
            Request::METHOD_GET,
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
            Request::METHOD_POST,
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
            Request::METHOD_POST,
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
        $this->assertInstanceOf(AuthorityRole::class, $AuthorityRole);
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
            Request::METHOD_POST,
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

    private function newTestAuthorityRole(): AuthorityRole
    {
        $TestCreator = $this->memberRepository->find(1);
        $AuthorityRole = new AuthorityRole();
        $Authority = $this->authorityMasterRepository->find(0);
        $AuthorityRole->setAuthority($Authority);
        $AuthorityRole->setDenyUrl('/abab');
        $AuthorityRole->setCreator($TestCreator);

        $this->entityManager->persist($AuthorityRole);
        $this->entityManager->flush();

        return $AuthorityRole;
    }

    protected function createFormData(?AuthorityRole $AuthorityRole = null): array
    {
        if (!$AuthorityRole) {
            $AuthorityRole = $this->newTestAuthorityRole();
        }

        return [
            [
                'Authority' => $AuthorityRole->getAuthority()->getId(),
                'deny_url' => '/test',
            ],
        ];
    }
}
