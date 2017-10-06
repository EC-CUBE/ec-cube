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

use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Form\Type\Admin\SecurityType;
use Eccube\Util\Str;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(service=SecurityController::class)
 */
class SecurityController extends AbstractController
{
    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Route("/{_admin}/setting/system/security", name="admin_setting_system_security")
     * @Template("Setting/System/security.twig")
     */
    public function index(Application $app, Request $request)
    {

        $builder = $this->formFactory->createBuilder(SecurityType::class);
        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                // 現在のセキュリティ情報を更新
                $adminRoot = $this->appConfig['admin_route'];

                $configFile = $this->appConfig['root_dir'].'/app/config/eccube/config.php';
                $config = require $configFile;

                // trim処理
                $allowHost = Str::convertLineFeed($data['admin_allow_hosts']);
                if (empty($allowHost)) {
                    $config['admin_allow_hosts'] = null;
                } else {
                    $config['admin_allow_hosts'] = explode("\n", $allowHost);
                }

                if ($data['force_ssl']) {
                    // SSL制限にチェックをいれた場合、https経由で接続されたか確認
                    if ($request->isSecure()) {
                        // httpsでアクセスされたらSSL制限をチェック
                        $config['force_ssl'] = Constant::ENABLED;
                    } else {
                        // httpから変更されたらfalseのまま
                        $config['force_ssl'] = Constant::DISABLED;
                        $data['force_ssl'] = (bool)Constant::DISABLED;
                    }
                } else {
                    $config['force_ssl'] = Constant::DISABLED;
                }
                $form = $builder->getForm();
                $form->setData($data);

                file_put_contents($configFile, sprintf('<?php return %s', var_export($config, true)).';');

                // ルーティングのキャッシュを削除
                $cacheDir = $this->appConfig['root_dir'].'/app/cache/routing';
                if (file_exists($cacheDir)) {
                    $finder = Finder::create()->in($cacheDir);
                    $filesystem = new Filesystem();
                    $filesystem->remove($finder);
                }

                if ($adminRoot != $data['admin_route_dir']) {
                    // admin_routeが変更されればpath.phpを更新
                    $pathFile = $this->appConfig['root_dir'].'/app/config/eccube/path.php';
                    $config = require $pathFile;
                    $config['admin_route'] = $data['admin_route_dir'];

                    file_put_contents($pathFile, sprintf('<?php return %s', var_export($config, true)).';');

                    $app->addSuccess('admin.system.security.route.dir.complete', 'admin');

                    // ログアウト
                    $this->getSecurity($app)->setToken(null);

                    // 管理者画面へ再ログイン
                    return $app->redirect($request->getBaseUrl().'/'.$config['admin_route']);
                }

                $app->addSuccess('admin.system.security.save.complete', 'admin');

            }
        } else {
            // セキュリティ情報の取得
            $form->get('admin_route_dir')->setData($this->appConfig['admin_route']);
            $allowHost = $this->appConfig['admin_allow_hosts'];
            if (count($allowHost) > 0) {
                $form->get('admin_allow_hosts')->setData(Str::convertLineFeed(implode("\n", $allowHost)));
            }
            $form->get('force_ssl')->setData((bool)$this->appConfig['force_ssl']);
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
