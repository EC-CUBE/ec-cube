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
use Eccube\Form\Type\Admin\EnhancementType;
use Eccube\Service\SystemService;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class EnhancementController extends AbstractController
{
    /**
     * @var SystemService
     */
    protected $systemService;

    /**
     * SystemController constructor.
     *
     * @param SystemService $systemService
     */
    public function __construct(SystemService $systemService)
    {
        $this->systemService = $systemService;
    }

    /**
     * @Route("/%eccube_admin_route%/setting/system/enhancement", name="admin_setting_system_enhancement")
     * @Template("@admin/Setting/System/enhancement.twig")
     */
    public function index(Request $request)
    {
        $builder = $this->formFactory->createBuilder(EnhancementType::class);
        $form = $builder->getForm();
        $form->handleRequest($request);


        return [
            'form' => $form->createView(),
        ];
    }
}
