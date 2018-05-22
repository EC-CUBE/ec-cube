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

namespace Eccube\Controller\Admin\Setting\System;

use Eccube\Controller\AbstractController;
use Eccube\Form\Type\Admin\SecurityType;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route(service=SecurityController::class)
 */
class SecurityController extends AbstractController
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * SecurityController constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/%eccube_admin_route%/setting/system/security", name="admin_setting_system_security")
     * @Template("@admin/Setting/System/security.twig")
     */
    public function index(Request $request, CacheUtil $cacheUtil)
    {
        $builder = $this->formFactory->createBuilder(SecurityType::class);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $envFile = $this->getParameter('kernel.project_dir').'/.env';
            $env = file_get_contents($envFile);

            $adminAllowHosts = \json_encode(
                array_filter(\explode("\n", StringUtil::convertLineFeed($data['admin_allow_hosts'])), function ($str) {
                    return StringUtil::isNotBlank($str);
                })
            );
            $env = StringUtil::replaceOrAddEnv($env, [
                'ECCUBE_ADMIN_ALLOW_HOSTS' => "'{$adminAllowHosts}'",
                'ECCUBE_FORCE_SSL' => $data['force_ssl'] ? 'true' : 'false',
                'ECCUBE_SCHEME' => $data['force_ssl'] ? 'https' : 'http',
            ]);

            file_put_contents($envFile, $env);

            // 管理画面URLの更新. 変更されている場合はログアウトし再ログインさせる.
            $adminRoot = $this->eccubeConfig['eccube_admin_route'];
            if ($adminRoot !== $data['admin_route_dir']) {
                $env = StringUtil::replaceOrAddEnv($env, [
                    'ECCUBE_ADMIN_ROUTE' => $data['admin_route_dir'],
                ]);

                file_put_contents($envFile, $env);

                $this->addSuccess('admin.system.security.route.dir.complete', 'admin');

                // ログアウト
                $this->tokenStorage->setToken(null);

                // キャッシュの削除
                $cacheUtil->clearCache();

                // 管理者画面へ再ログイン
                return $this->redirect($request->getBaseUrl().'/'.$data['admin_route_dir']);
            }

            $this->addSuccess('admin.system.security.save.complete', 'admin');

            // キャッシュの削除
            $cacheUtil->clearCache();

            return $this->redirectToRoute('admin_setting_system_security');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
