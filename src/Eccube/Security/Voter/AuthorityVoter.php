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

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Eccube\Application;

class AuthorityVoter implements VoterInterface
{

    public $app;

    public function __construct(Application $app)
    {
        $this->app     = $app;
    }

    public function supportsAttribute($attribute)
    {
        return true;
    }

    public function supportsClass($class)
    {
        return true;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {

        $request = null;
        try {
            $request = $this->app['request'];
        } catch (\RuntimeException $e) {
            // requestが取得できない場合、無視する(テストプログラムで不要なため)
            return;
        }

        $path = rawurldecode($request->getPathInfo());

        $Member = $this->app->user();

        if ($Member instanceof \Eccube\Entity\Member) {
            // 管理者のロールをチェック
            $AuthorityRoles = $this->app['eccube.repository.authority_role']->findBy(array('Authority' => $Member->getAuthority()));
            foreach ($AuthorityRoles as $AuthorityRole) {
                // 許可しないURLが含まれていればアクセス拒否
                try {
                    // 正規表現でURLチェック
                    $denyUrl = str_replace('/', '\/', $AuthorityRole->getDenyUrl());
                    if (preg_match("/^(\/{$this->app['config']['admin_route']}$denyUrl)/i", $path)) {
                        return  VoterInterface::ACCESS_DENIED;
                    }
                } catch (\Exception $e) {
                    // 拒否URLの指定に誤りがある場合、エスケープさせてチェック
                    $denyUrl = preg_quote($AuthorityRole->getDenyUrl(), '/');
                    if (preg_match("/^(\/{$this->app['config']['admin_route']}$denyUrl)/i", $path)) {
                        return  VoterInterface::ACCESS_DENIED;
                    }
                }
            }
        }

        return VoterInterface::ACCESS_GRANTED;
    }
}