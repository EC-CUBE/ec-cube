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
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
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
    public function index(Request $request, KernelInterface $kernel)
    {
        $builder = $this->formFactory->createBuilder(SecurityType::class);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $envFile = $this->getParameter('kernel.project_dir').'/.env';
            $env = file_get_contents($envFile);

            $adminAllowHosts = \json_encode(
                \explode("\n", StringUtil::convertLineFeed($data['admin_allow_hosts']))
            );
            $env = $this->replaceEnv($env, [
                'ECCUBE_ADMIN_ALLOW_HOSTS' => "'{$adminAllowHosts}'",
                'ECCUBE_FORCE_SSL' => $data['force_ssl'] ? 'true' : 'false',
                'ECCUBE_SCHEME' => $data['force_ssl'] ? 'https' : 'http',
            ]);

            file_put_contents($envFile, $env);

            // 管理画面URLの更新. 変更されている場合はログアウトし再ログインさせる.
            $adminRoot = $this->eccubeConfig['eccube_admin_route'];
            if ($adminRoot !== $data['admin_route_dir']) {

                $env = $this->replaceEnv($env, [
                    'ECCUBE_ADMIN_ROUTE' => $data['admin_route_dir'],
                ]);

                file_put_contents($envFile, $env);

                // ログアウト
                $this->tokenStorage->setToken(null);

                $this->addSuccess('admin.system.security.route.dir.complete', 'admin');

                $this->processCacheClearCommand($kernel);

                // 管理者画面へ再ログイン
                return $this->redirect($request->getBaseUrl().'/'.$data['admin_route_dir']);
            }

            $this->addSuccess('admin.system.security.save.complete', 'admin');

            $this->processCacheClearCommand($kernel);

            return $this->redirectToRoute('admin_setting_system_security');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param string $env
     * @param array $replacement
     * @return string
     */
    protected function replaceEnv($env, array $replacement)
    {
        foreach ($replacement as $key => $value) {
            $env = preg_replace('/('.$key.')=(.*)/', '$1='.$value, $env);
        }

        return $env;
    }

    /**
     * @param KernelInterface $kernel
     * @return string
     */
    protected function processCacheClearCommand(KernelInterface $kernel)
    {
        $console = new Application($kernel);
        $console->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'cache:clear',
            '--no-warmup' => null,
            '--no-ansi' => null,
        ));

        $output = new BufferedOutput(
            OutputInterface::VERBOSITY_DEBUG,
            true
        );

        $console->run($input, $output);

        return $output->fetch();
    }
}
