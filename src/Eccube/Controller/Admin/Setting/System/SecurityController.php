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
use Eccube\Service\EnvFileService;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityController extends AbstractController
{
    /**
     * この画面が .env へ書き込む環境変数キー（OS 環境変数によるオーバーライド判定に使用）.
     */
    private const ENV_KEYS = [
        'ECCUBE_FRONT_ALLOW_HOSTS',
        'ECCUBE_FRONT_DENY_HOSTS',
        'ECCUBE_ADMIN_ALLOW_HOSTS',
        'ECCUBE_ADMIN_DENY_HOSTS',
        'ECCUBE_FORCE_SSL',
        'TRUSTED_HOSTS',
        'ECCUBE_ADMIN_ROUTE',
    ];

    /**
     * SecurityController constructor.
     */
    public function __construct(
        protected TokenStorageInterface $tokenStorage,
        private readonly CacheUtil $cacheUtil,
        private readonly EnvFileService $envFileService,
    ) {
    }

    /**
     * @return RedirectResponse|array<string, mixed>
     */
    #[Route(path: '/%eccube_admin_route%/setting/system/security', name: 'admin_setting_system_security', methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Setting/System/security.twig')]
    public function index(Request $request): RedirectResponse|array
    {
        $builder = $this->formFactory->createBuilder(SecurityType::class);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // .env ファイル自体が読み込まれない状況（全キー反映不可）では保存を拒否する
            $ineffectiveReasons = $this->envFileService->getIneffectiveReasons();
            if ([] !== $ineffectiveReasons) {
                $this->addError('admin.common.save_error', 'admin');
                foreach ($ineffectiveReasons as $reason) {
                    $this->addError('admin.system.env.ineffective.'.$reason, 'admin');
                }

                return $this->redirectToRoute('admin_setting_system_security');
            }

            // OS 環境変数やカスケードファイルで上書きされているキーは書き込んでも反映されないため除外する
            $overriddenKeys = $this->envFileService->getOverriddenKeys(self::ENV_KEYS);

            $data = $form->getData();
            $envFile = $this->getParameter('kernel.project_dir').'/.env';
            $env = file_get_contents($envFile);

            $frontAllowHosts = \json_encode(
                array_filter(\explode("\n", StringUtil::convertLineFeed($data['front_allow_hosts'])), fn ($str) => StringUtil::isNotBlank($str))
            );
            $frontDenyHosts = \json_encode(
                array_filter(\explode("\n", StringUtil::convertLineFeed($data['front_deny_hosts'])), fn ($str) => StringUtil::isNotBlank($str))
            );

            $adminAllowHosts = \json_encode(
                array_filter(\explode("\n", StringUtil::convertLineFeed($data['admin_allow_hosts'])), fn ($str) => StringUtil::isNotBlank($str))
            );
            $adminDenyHosts = \json_encode(
                array_filter(\explode("\n", StringUtil::convertLineFeed($data['admin_deny_hosts'])), fn ($str) => StringUtil::isNotBlank($str))
            );

            // 上書きされているキーは書き込み対象から除外する
            $replace = array_diff_key([
                'ECCUBE_FRONT_ALLOW_HOSTS' => "'{$frontAllowHosts}'",
                'ECCUBE_FRONT_DENY_HOSTS' => "'{$frontDenyHosts}'",
                'ECCUBE_ADMIN_ALLOW_HOSTS' => "'{$adminAllowHosts}'",
                'ECCUBE_ADMIN_DENY_HOSTS' => "'{$adminDenyHosts}'",
                'ECCUBE_FORCE_SSL' => $data['force_ssl'] ? '1' : '0',
                'TRUSTED_HOSTS' => $data['trusted_hosts'],
            ], array_flip($overriddenKeys));

            if ([] !== $replace) {
                $env = StringUtil::replaceOrAddEnv($env, $replace);
                file_put_contents($envFile, $env);
            }

            // 上書きされ反映されなかったキーは名指しで警告する
            if ([] !== $overriddenKeys) {
                $this->addWarning(trans('admin.system.env.ineffective.overridden', ['%keys%' => implode(', ', $overriddenKeys)]), 'admin');
            }

            // 管理画面URLの更新. 上書きされておらず変更されている場合はログアウトし再ログインさせる.
            $adminRoute = $this->eccubeConfig['eccube_admin_route'];
            if ($adminRoute !== $data['admin_route_dir'] && !in_array('ECCUBE_ADMIN_ROUTE', $overriddenKeys, true)) {
                $env = StringUtil::replaceOrAddEnv($env, [
                    'ECCUBE_ADMIN_ROUTE' => $data['admin_route_dir'],
                ]);

                file_put_contents($envFile, $env);

                $this->addSuccess('admin.setting.system.security.admin_url_changed', 'admin');

                // ログアウト
                $this->tokenStorage->setToken(null);

                // キャッシュの削除
                $this->cacheUtil->clearCache();

                // 管理者画面へ再ログイン
                return $this->redirect($request->getBaseUrl().'/'.$data['admin_route_dir']);
            }

            $this->addSuccess('admin.common.save_complete', 'admin');

            // キャッシュの削除
            $this->cacheUtil->clearCache();

            return $this->redirectToRoute('admin_setting_system_security');
        }

        // 管理画面URLがadminの場合アラートを表示する。
        $adminRoute = $this->eccubeConfig['eccube_admin_route'];
        if ($adminRoute === 'admin') {
            $this->addWarning('admin.setting.system.security.admin_url_warning', 'admin');
        }

        // .env ファイル自体が読み込まれない状況では警告を出し、登録ボタンを無効化する。
        $ineffectiveReasons = $this->envFileService->getIneffectiveReasons();
        foreach ($ineffectiveReasons as $reason) {
            $this->addWarning('admin.system.env.ineffective.'.$reason, 'admin');
        }

        // 個々のキーが OS 環境変数等で上書きされている場合は該当キーを名指しで警告する。
        $overriddenKeys = $this->envFileService->getOverriddenKeys(self::ENV_KEYS);
        if ([] !== $overriddenKeys) {
            $this->addWarning(trans('admin.system.env.ineffective.overridden', ['%keys%' => implode(', ', $overriddenKeys)]), 'admin');
        }

        return [
            'form' => $form->createView(),
            'envWritable' => [] === $ineffectiveReasons,
        ];
    }
}
