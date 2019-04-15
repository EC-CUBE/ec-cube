<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Util\Str;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;


class SecurityController extends AbstractController
{
    public function index(Application $app, Request $request)
    {

        $builder = $app['form.factory']->createBuilder('admin_security');
        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                // 現在のセキュリティ情報を更新
                $adminRoot = $app['config']['admin_route'];

                $configFile = $app['config']['root_dir'].'/app/config/eccube/config';
                if (file_exists($configFile.'.php')) {
                    $config = require $configFile.'.php';
                } elseif (file_exists($configFile.'.yml')) {
                    $config = Yaml::parse(file_get_contents($configFile.'.yml'));
                }

                // trim処理
                $allowHost = Str::convertLineFeed($data['admin_allow_host']);
                if (empty($allowHost)) {
                    $config['admin_allow_host'] = null;
                } else {
                    $config['admin_allow_host'] = explode("\n", $allowHost);
                }

                if ($data['force_ssl']) {
                    // SSL制限にチェックをいれた場合、https経由で接続されたか確認
                    if ($request->isSecure()) {
                        // httpsでアクセスされたらSSL制限をチェック
                        $config['force_ssl'] = Constant::ENABLED;
                    } else {
                        // httpから変更されたらfalseのまま
                        $config['force_ssl'] = Constant::DISABLED;
                        $data['force_ssl'] = (bool) Constant::DISABLED;
                    }
                } else {
                    $config['force_ssl'] = Constant::DISABLED;
                }
                $form = $builder->getForm();
                $form->setData($data);

                if (file_exists($configFile.'.php')) {
                    file_put_contents($configFile.'.php', sprintf('<?php return %s', var_export($config, true)).';');
                }
                if (file_exists($configFile.'.yml')) {
                    file_put_contents($configFile.'.yml', Yaml::dump($config));
                }

                if ($adminRoot != $data['admin_route_dir']) {
                    // admin_routeが変更されればpath.(yml|php)を更新
                    $pathFile = $app['config']['root_dir'].'/app/config/eccube/path';

                    if (file_exists($pathFile.'.php')) {
                        $config = require $pathFile.'.php';
                    } elseif (file_exists($pathFile.'.yml')) {
                        $config = Yaml::parse(file_get_contents($pathFile.'.yml'));
                    }

                    $config['admin_route'] = $data['admin_route_dir'];

                    if (file_exists($pathFile.'.php')) {
                        file_put_contents($pathFile.'.php', sprintf('<?php return %s', var_export($config, true)).';');
                    }
                    if (file_exists($pathFile.'.yml')) {
                        file_put_contents($pathFile.'.yml', Yaml::dump($config));
                    }

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
            $form->get('admin_route_dir')->setData($app['config']['admin_route']);
            $allowHost = $app['config']['admin_allow_host'];
            if (is_array($allowHost) && count($allowHost) > 0) {
                $form->get('admin_allow_host')->setData(Str::convertLineFeed(implode("\n", $allowHost)));
            }
            $form->get('force_ssl')->setData((bool)$app['config']['force_ssl']);
        }

        // 管理画面URLのチェック
        if (isset($app['config']['admin_route']) && $app['config']['admin_route'] == 'admin') {
            $app->addWarning('admin.system.security.admin.url.warning', 'admin');
        }

        return $app->render('Setting/System/security.twig', array(
            'form' => $form->createView(),
        ));
    }
}
