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

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;


class SecurityController extends AbstractController
{
    public function index(Application $app, Request $request)
    {
        $form = $app['form.factory']->createBuilder('admin_security')->getForm();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                // 現在のセキュリティ情報を更新
                $pathFile = $app['config']['root_dir'] . '/app/config/eccube/path.yml';
                $config = Yaml::parse(file_get_contents($pathFile));
                $config['admin_route'] = $data['admin_route_dir'];
                file_put_contents($pathFile, Yaml::dump($config));

                $configFile = $app['config']['root_dir'] . '/app/config/eccube/config.yml';
                $config = Yaml::parse(file_get_contents($configFile));
                // trim処理
                $config['admin_allow_host'] = explode("\n", $data['admin_allow_host']);

                $url = 'https://' . $request->getHost();
                // $response = $app->render($view)

                if ($data['force_ssl']) {
                    // SSL制限にチェックをいれた場合、サーバがSSLを使用可能か確認
                    if (!$request->isSecure()) {
                        // httpでアクセスされたらsslの有効かどうかのチェックを行う
                    }
                }
                $config['force_ssl'] = $data['force_ssl'] ? Constant::ENABLED : Constant::DISABLED;

                file_put_contents($configFile, Yaml::dump($config));
                $app->addSuccess('admin.sysmte.security.save.complete', 'admin'); 

            }
        } else {
            // セキュリティ情報の取得
            $form->get('admin_route_dir')->setData($app['config']['admin_route']);
            $allowHost = $app['config']['admin_allow_host'];
            if (count($allowHost) > 0) {
                $form->get('admin_allow_host')->setData(implode("\n", $allowHost));
            }
            $form->get('force_ssl')->setData((bool)$app['config']['force_ssl']);
        }

        return $app->render('Setting/System/security.twig', array(
            'form' => $form->createView(),
        ));
    }
}
