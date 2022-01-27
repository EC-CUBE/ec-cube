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

namespace Eccube\Controller\Admin\Setting\System;

use Eccube\Controller\AbstractController;
use Eccube\Form\Type\Admin\SecurityType;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/%eccube_admin_route%/setting/system/security", name="admin_setting_system_security", methods={"GET", "POST"})
     * @Template("@admin/Setting/System/security.twig")
     */
    public function index(Request $request, CacheUtil $cacheUtil)
    {
        $builder = $this->formFactory->createBuilder(SecurityType::class);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //.envファイルが存在しないときに設定は失敗する
            if (file_exists($this->getParameter('kernel.project_dir').'/.env') === false) {
                $this->addError('admin.common.save_error', 'admin');

                return $this->redirectToRoute('admin_setting_system_security');
            }

            $data = $form->getData();
            $envFile = $this->getParameter('kernel.project_dir').'/.env';
            $env = file_get_contents($envFile);

            $adminAllowHosts = \json_encode(
                array_filter(\explode("\n", StringUtil::convertLineFeed($data['admin_allow_hosts'])), function ($str) {
                    return StringUtil::isNotBlank($str);
                })
            );
            $adminDenyHosts = \json_encode(
                array_filter(\explode("\n", StringUtil::convertLineFeed($data['admin_deny_hosts'])), function ($str) {
                    return StringUtil::isNotBlank($str);
                })
            );

            $env = StringUtil::replaceOrAddEnv($env, [
                'ECCUBE_ADMIN_ALLOW_HOSTS' => "'{$adminAllowHosts}'",
                'ECCUBE_ADMIN_DENY_HOSTS' => "'{$adminDenyHosts}'",
                'ECCUBE_FORCE_SSL' => $data['force_ssl'] ? 'true' : 'false',
                'TRUSTED_HOSTS' => $data['trusted_hosts'],
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

        // 管理画面URLがadminの場合アラートを表示する。
        $adminRoute = $this->eccubeConfig['eccube_admin_route'];
        if ($adminRoute === 'admin') {
            $this->addWarning('admin.setting.system.security.admin_url_warning', 'admin');
        }

        // .envファイルが存在しない場合警告を出す。
        if (file_exists($this->getParameter('kernel.project_dir').'/.env') === false) {
            $this->addWarning('admin.setting.system.security.not_found_env_file', 'admin');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
