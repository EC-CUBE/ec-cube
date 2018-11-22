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
use Knp\Component\Pager\Paginator;
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

    public function __construct(SystemService $systemService) {
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
        $isMaintenace = $this->systemService->isMaintenanceMode();

        $builder = $this->formFactory->createBuilder(FormType::class);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->systemService->switchMaintenance(($request->request->get('maintenance') == "on"), null);

            $isMaintenace = $this->systemService->isMaintenanceMode();

            $this->addSuccess(($isMaintenace) ? 'admin.content.maintenance_switch__on_message' : 'admin.content.maintenance_switch__off_message', 'admin');

            return $this->redirectToRoute('admin_content_maintenance');
        }

        return [
            'form' => $form->createView(),
            'isMaintenance' => $isMaintenace,
        ];
    }

}
