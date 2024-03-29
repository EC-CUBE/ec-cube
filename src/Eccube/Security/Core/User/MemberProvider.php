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

namespace Eccube\Security\Core\User;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Master\Work;
use Eccube\Entity\Member;
use Eccube\Repository\MemberRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class MemberProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    /**
     * @var MemberRepository
     */
    protected $memberRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(MemberRepository $memberRepository, EntityManagerInterface $entityManager)
    {
        $this->memberRepository = $memberRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @return UserInterface
     *
     * @throws UserNotFoundException
     *
     * @deprecated since Symfony 5.3, use loadUserByIdentifier() instead
     */
    public function loadUserByUsername($username): Member
    {
        return $this->loadUserByIdentifier($username);
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the user is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof Member) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return Member::class === $class || is_subclass_of($class, Member::class);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $Member = $this->memberRepository->findOneBy(['login_id' => $identifier, 'Work' => Work::ACTIVE]);

        if (null === $Member) {
            throw new UserNotFoundException(sprintf('Username "%s" does not exist.', $identifier));
        }

        return $Member;
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        $user->setPassword($newHashedPassword);
        $this->entityManager->flush();
    }
}
