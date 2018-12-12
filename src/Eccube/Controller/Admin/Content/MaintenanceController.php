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

namespace Eccube\Controller\Admin\Content;

use Eccube\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Eccube\Service\SystemService;

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
     * @Route("/%eccube_admin_route%/content/maintenance", name="admin_content_maintenance")
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
            $path = $this->container->getParameter('eccube_content_maintenance_file_path');

            if ($isMaintenance === false && $changeTo == 'on') {
                // 現在メンテナンスモードではない　かつ　メンテナンスモードを有効　にした場合
                // メンテナンスモードを有効にする
                file_put_contents($path, null);

                $this->addSuccess('admin.content.maintenance_switch__on_message', 'admin');
            } elseif ($isMaintenance && $changeTo == 'off') {
                // 現在メンテナンスモード　かつ　メンテナンスモードを無効　にした場合
                // メンテナンスモードを無効にする
                unlink($path);

                $this->addSuccess('admin.content.maintenance_switch__off_message', 'admin');
            }

            return $this->redirectToRoute('admin_content_maintenance');
        }

        return [
            'form' => $form->createView(),
            'isMaintenance' => $isMaintenance,
        ];
    }
}
