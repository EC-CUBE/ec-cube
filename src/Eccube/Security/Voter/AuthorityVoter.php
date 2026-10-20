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
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AuthorityVoter implements VoterInterface
{
    public function __construct(protected AuthorityRoleRepository $authorityRoleRepository, protected RequestStack $requestStack, protected EccubeConfig $eccubeConfig)
    {
    }

    /**
     * @param array<mixed> $attributes
     */
    #[\Override]
    public function vote(TokenInterface $token, mixed $subject, array $attributes, ?Vote $vote = null): int
    {
        $path = null;

        try {
            $request = $this->requestStack->getMainRequest();
        } catch (\RuntimeException) {
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
                // 正規表現でURLチェック
                $denyUrl = str_replace('/', '\/', (string) $AuthorityRole->getDenyUrl());
                $matched = @preg_match("/^(\/{$adminRoute}{$denyUrl})/i", (string) $path);
                if (false === $matched) {
                    // 拒否URLの指定が正規表現として不正な場合は、エスケープしてリテラルの前方一致で判定する。
                    // preg_match の失敗は警告であり例外ではないため、戻り値 false を明示的に拾う(フェイルオープンの防止)。
                    $denyUrl = preg_quote((string) $AuthorityRole->getDenyUrl(), '/');
                    $matched = preg_match("/^(\/{$adminRoute}{$denyUrl})/i", (string) $path);
                }
                if (1 === $matched) {
                    return VoterInterface::ACCESS_DENIED;
                }
            }
        }

        return VoterInterface::ACCESS_GRANTED;
    }
}
