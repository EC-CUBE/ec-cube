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
use Eccube\Util\CacheUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CacheController extends AbstractController
{
    /**
     * @Route("/%eccube_admin_route%/content/cache", name="admin_content_cache")
     * @Template("@admin/Content/cache.twig")
     */
    public function index(Request $request, CacheUtil $cacheUtil)
    {
        $result = '';

        $builder = $this->formFactory->createBuilder(FormType::class);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $cacheUtil->clearCache();

            $this->addSuccess('admin.content.cache.save.complete', 'admin');
        }

        return [
            'form' => $form->createView(),
            'result' => $result,
        ];
    }
}
