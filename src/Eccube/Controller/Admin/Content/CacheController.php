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


namespace Eccube\Controller\Admin\Content;

use Eccube\Controller\AbstractController;
use Eccube\Util\CacheUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;

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
