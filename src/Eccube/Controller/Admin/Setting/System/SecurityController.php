<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Admin\Setting\System;

use Eccube\Controller\AbstractController;
use Eccube\Form\Type\Admin\SecurityType;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
            ]);

            file_put_contents($envFile, $env);

            // 管理画面URLの更新. 変更されている場合はログアウトし再ログインさせる.
            $adminRoute = $this->eccubeConfig['eccube_admin_route'];
            if ($adminRoute !== $data['admin_route_dir']) {
                $env = StringUtil::replaceOrAddEnv($env, [
                    'ECCUBE_ADMIN_ROUTE' => $data['admin_route_dir'],
                ]);

                file_put_contents($envFile, $env);

                $this->addSuccess('admin.setting.system.security.admin_url_changed', 'admin');

                // ログアウト
                $this->tokenStorage->setToken(null);

                // キャッシュの削除
                $cacheUtil->clearCache();

                // 管理者画面へ再ログイン
                return $this->redirect($request->getBaseUrl().'/'.$data['admin_route_dir']);
            }

            $this->addSuccess('admin.common.save_complete', 'admin');

            // キャッシュの削除
            $cacheUtil->clearCache();

            return $this->redirectToRoute('admin_setting_system_security');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
