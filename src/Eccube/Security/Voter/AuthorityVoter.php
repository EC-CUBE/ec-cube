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
            $AuthorityRoles = $this->authorityRoleRepository->findBy(array('Authority' => $Member->getAuthority()));
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
