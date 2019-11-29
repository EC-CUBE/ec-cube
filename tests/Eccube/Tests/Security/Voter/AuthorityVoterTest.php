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
        $this->authorityRoleRepository = $this->entityManager->getRepository(\Eccube\Entity\AuthorityRole::class);
        $this->eccubeConfig = self::$container->get(EccubeConfig::class);
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
