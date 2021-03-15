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

namespace Eccube\Controller\Admin\Content;

use Eccube\Controller\AbstractController;
use Eccube\Service\SystemService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MaintenanceController extends AbstractController
{
    /**
     * @var SystemService
     */
    protected $systemService;

    public function __construct(SystemService $systemService)
    {
        $this->systemService = $systemService;
    }

    /**
     * メンテナンス管理ページを表示
     *
     * @Route("/%eccube_admin_route%/content/maintenance", name="admin_content_maintenance", methods={"GET", "POST"})
     * @Template("@admin/Content/maintenance.twig")
     */
    public function index(Request $request)
    {
        $isMaintenance = $this->systemService->isMaintenanceMode();

        $builder = $this->formFactory->createBuilder(FormType::class);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $changeTo = $request->request->get('maintenance');

            if ($isMaintenance === false && $changeTo == 'on') {
                // 現在メンテナンスモードではない　かつ　メンテナンスモードを有効　にした場合
                // メンテナンスモードを有効にする
                $this->systemService->enableMaintenance('', true);
                $this->addSuccess('admin.content.maintenance_switch__on_message', 'admin');
            } elseif ($isMaintenance && $changeTo == 'off') {
                // 現在メンテナンスモード　かつ　メンテナンスモードを無効　にした場合
                // メンテナンスモードを無効にする
                $this->systemService->disableMaintenanceNow('', true);

                $this->addSuccess('admin.content.maintenance_switch__off_message', 'admin');
            }

            return $this->redirectToRoute('admin_content_maintenance');
        }

        return [
            'form' => $form->createView(),
            'isMaintenance' => $isMaintenance,
        ];
    }

    /**
     * メンテナンス解除
     *
     * キャッシュ管理やプラグインのインストール等の操作時にajax経由で解除する
     * 権限管理設定でアクセス不可になるのを避けるため、ルーティングは/admin/disable_maintenanceで設定しています
     *
     * @Route("/%eccube_admin_route%/disable_maintenance/{mode}", requirements={"mode": "manual|auto_maintenance|auto_maintenance_update"}, name="admin_disable_maintenance", methods={"POST"})
     */
    public function disableMaintenance(Request $request, $mode, SystemService $systemService)
    {
        $this->isTokenValid();

        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        if ($mode === 'manual') {
            $path = $this->container->getParameter('eccube_content_maintenance_file_path');
            if (file_exists($path)) {
                unlink($this->container->getParameter('eccube_content_maintenance_file_path'));
            }
        } else {
            $maintenanceMode = [
                'auto_maintenance' => SystemService::AUTO_MAINTENANCE,
                'auto_maintenance_update' => SystemService::AUTO_MAINTENANCE_UPDATE
            ];
            $systemService->disableMaintenance($maintenanceMode[$mode]);
        }

        return $this->json(['success' => true]);
    }
}
