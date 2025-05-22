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
use Eccube\Util\CacheUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CacheController extends AbstractController
{
    /**
     * @Route("/%eccube_admin_route%/content/cache", name="admin_content_cache", methods={"GET", "POST"})
     * @Template("@admin/Content/cache.twig")
     */
    public function index(Request $request, CacheUtil $cacheUtil, SystemService $systemService)
    {
        $builder = $this->formFactory->createBuilder(FormType::class);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $systemService->switchMaintenance(true);

            $cacheUtil->clearCache();

            $this->addFlash('eccube.admin.disable_maintenance', '');

            $this->addSuccess('admin.common.delete_complete', 'admin');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
