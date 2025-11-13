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
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AuthorityVoterTest extends EccubeTestCase
{
    protected ?AuthorityRoleRepository $authorityRoleRepository = null;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->authorityRoleRepository = $this->entityManager->getRepository(AuthorityRole::class);
        $this->eccubeConfig = static::getContainer()->get(EccubeConfig::class);
    }

    /**
     * @param mixed $accessUrl
     * @param mixed $expected
     */
    #[DataProvider(methodName: 'voteProvider')]
    public function testVote(array $deniedUrls, $accessUrl, $expected)
    {
        $request = $this->createMock(Request::class);
        $request->method('getPathInfo')->willReturn($accessUrl);

        /** @var RequestStack&\PHPUnit\Framework\MockObject\MockObject $requestStack */
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getMainRequest')->willReturn($request);

        $voter = new AuthorityVoter($this->authorityRoleRepository, $requestStack, $this->eccubeConfig);

        $Member = $this->createMember();

        foreach ($deniedUrls as $denyUrl) {
            $AuthorityRole = new AuthorityRole();
            $AuthorityRole->setDenyUrl($denyUrl);
            $AuthorityRole->setAuthority($Member->getAuthority());
            $this->entityManager->persist($AuthorityRole);
            $this->entityManager->flush();
        }

        /** @var TokenInterface&\PHPUnit\Framework\MockObject\MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($Member);

        $this->assertEquals($expected, $voter->vote($token, null, []));
    }

    public static function voteProvider(): \Iterator
    {
        yield [[], '/admin/content', VoterInterface::ACCESS_GRANTED];
        yield [['/content'], '/admin/content', VoterInterface::ACCESS_DENIED];
        yield [['/content'], '/admin/content/page', VoterInterface::ACCESS_DENIED];
        yield [['/content'], '/content', VoterInterface::ACCESS_GRANTED];
        yield [['/content'], '/admin', VoterInterface::ACCESS_GRANTED];
        yield [['/content'], '/admin/product', VoterInterface::ACCESS_GRANTED];
    }
}
