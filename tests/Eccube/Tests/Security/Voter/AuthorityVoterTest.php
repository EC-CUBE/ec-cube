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

namespace Eccube\Tests\Security\Voter;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\AuthorityRole;
use Eccube\Repository\AuthorityRoleRepository;
use Eccube\Security\Voter\AuthorityVoter;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AuthorityVoterTest extends EccubeTestCase
{

    /**
     * @var AuthorityRoleRepository
     */
    protected $authorityRoleRepository;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    public function setUp()
    {
        parent::setUp();
        $this->authorityRoleRepository = $this->container->get(AuthorityRoleRepository::class);
        $this->eccubeConfig = $this->container->get(EccubeConfig::class);
    }

    /**
     * @dataProvider voteProvider
     */
    public function testVote(array $deniedUrls, $accessUrl, $expected)
    {
        $request = $this->createMock(Request::class);
        $request->method('getPathInfo')->willReturn($accessUrl);

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getMasterRequest')->willReturn($request);

        $voter = new AuthorityVoter($this->authorityRoleRepository, $requestStack, $this->eccubeConfig);

        $Member = $this->createMember();

        foreach ($deniedUrls as $denyUrl) {
            $AuthorityRole = new AuthorityRole();
            $AuthorityRole->setDenyUrl($denyUrl);
            $AuthorityRole->setAuthority($Member->getAuthority());
            $this->entityManager->persist($AuthorityRole);
            $this->entityManager->flush();
        }

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($Member);

        self::assertEquals($expected, $voter->vote($token, null, []));
    }

    public function voteProvider()
    {
        return [
            [[], '/admin/content', VoterInterface::ACCESS_GRANTED],
            [['/content'], '/admin/content', VoterInterface::ACCESS_DENIED],
            [['/content'], '/admin/content/page', VoterInterface::ACCESS_DENIED],
            [['/content'], '/content', VoterInterface::ACCESS_GRANTED],
            [['/content'], '/admin', VoterInterface::ACCESS_GRANTED],
            [['/content'], '/admin/product', VoterInterface::ACCESS_GRANTED],
        ];
    }
}
