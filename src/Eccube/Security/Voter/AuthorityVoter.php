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

namespace Eccube\Security\Voter;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Member;
use Eccube\Repository\AuthorityRoleRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AuthorityVoter implements VoterInterface
{
    /**
     * @var AuthorityRoleRepository
     */
    protected $authorityRoleRepository;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    public function __construct(
        AuthorityRoleRepository $authorityRoleRepository,
        RequestStack $requestStack,
        EccubeConfig $eccubeConfig
    ) {
        $this->authorityRoleRepository = $authorityRoleRepository;
        $this->requestStack = $requestStack;
        $this->eccubeConfig = $eccubeConfig;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $request = null;
        $path = null;

        try {
            $request = $this->requestStack->getMasterRequest();
        } catch (\RuntimeException $e) {
            // requestが取得できない場合、棄権する(テストプログラムで不要なため)
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (is_object($request)) {
            $path = rawurldecode($request->getPathInfo());
        }

        $Member = $token->getUser();
        if ($Member instanceof Member) {
            // 管理者のロールをチェック
            $AuthorityRoles = $this->authorityRoleRepository->findBy(['Authority' => $Member->getAuthority()]);
            $adminRoute = $this->eccubeConfig->get('eccube_admin_route');

            foreach ($AuthorityRoles as $AuthorityRole) {
                // 許可しないURLが含まれていればアクセス拒否
                try {
                    // 正規表現でURLチェック
                    $denyUrl = str_replace('/', '\/', $AuthorityRole->getDenyUrl());
                    if (preg_match("/^(\/{$adminRoute}{$denyUrl})/i", $path)) {
                        return VoterInterface::ACCESS_DENIED;
                    }
                } catch (\Exception $e) {
                    // 拒否URLの指定に誤りがある場合、エスケープさせてチェック
                    $denyUrl = preg_quote($AuthorityRole->getDenyUrl(), '/');
                    if (preg_match("/^(\/{$adminRoute}{$denyUrl})/i", $path)) {
                        return VoterInterface::ACCESS_DENIED;
                    }
                }
            }
        }

        return VoterInterface::ACCESS_GRANTED;
    }
}
