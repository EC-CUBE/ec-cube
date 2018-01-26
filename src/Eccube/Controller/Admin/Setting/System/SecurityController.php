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

use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Form\Type\Admin\SecurityType;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Yaml\Yaml;

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
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }


    /**
     * @Route("/%admin_route%/setting/system/security", name="admin_setting_system_security")
     * @Template("@admin/Setting/System/security.twig")
     */
    public function index(Request $request)
    {

        $builder = $this->formFactory->createBuilder(SecurityType::class);
        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                // 現在のセキュリティ情報を更新
                $adminRoot = $this->eccubeConfig['admin_route'];

                $configFile = $this->getParameter('kernel.project_dir').'/app/config/eccube/packages/eccube.yaml';
                $config = Yaml::parseFile($configFile);

                // trim処理
                $allowHost = StringUtil::convertLineFeed($data['admin_allow_hosts']);
                if (empty($allowHost)) {
                    $config['parameters']['eccube.constants']['admin_allow_hosts'] = null;
                } else {
                    $config['parameters']['eccube.constants']['admin_allow_hosts'] = explode("\n", $allowHost);
                }

                if ($data['force_ssl']) {
                    // SSL制限にチェックをいれた場合、https経由で接続されたか確認
                    if ($request->isSecure()) {
                        // httpsでアクセスされたらSSL制限をチェック
                        $config['parameters']['eccube.constants']['force_ssl'] = Constant::ENABLED;
                    } else {
                        // httpから変更されたらfalseのまま
                        $config['parameters']['eccube.constants']['force_ssl'] = Constant::DISABLED;
                        $data['force_ssl'] = (bool)Constant::DISABLED;
                    }
                } else {
                    $config['parameters']['eccube.constants']['force_ssl'] = Constant::DISABLED;
                }
                $form = $builder->getForm();
                $form->setData($data);

                file_put_contents($configFile, Yaml::dump($config, 10, 2));

                // ルーティングのキャッシュを削除
                $cacheDir = $this->getParameter('kernel.project_dir').'/app/cache/routing';
                if (file_exists($cacheDir)) {
                    $finder = Finder::create()->in($cacheDir);
                    $filesystem = new Filesystem();
                    $filesystem->remove($finder);
                }

                if ($adminRoot != $data['admin_route_dir']) {
                    // admin_routeが変更されればpath.phpを更新
                    $pathFile = $this->getParameter('kernel.project_dir').'/app/config/eccube/services.yaml';
                    $config = Yaml::parseFile($pathFile);
                    $config['parameters']['admin_route'] = $data['admin_route_dir'];

                    file_put_contents($pathFile, Yaml::dump($config, 10, 2, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE));

                    $this->addSuccess('admin.system.security.route.dir.complete', 'admin');

                    // ログアウト
                    $this->tokenStorage->setToken(null);

                    // 管理者画面へ再ログイン
                    return $this->redirect($request->getBaseUrl().'/'.$config['parameters']['admin_route']);
                }

                $this->addSuccess('admin.system.security.save.complete', 'admin');

            }
        } else {
            // セキュリティ情報の取得
            $form->get('admin_route_dir')->setData($this->eccubeConfig['admin_route']);
            $allowHost = $this->eccubeConfig['admin_allow_hosts'];
            if (count($allowHost) > 0) {
                $form->get('admin_allow_hosts')->setData(StringUtil::convertLineFeed(implode("\n", $allowHost)));
            }
            $form->get('force_ssl')->setData((bool)$this->eccubeConfig['force_ssl']);
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
