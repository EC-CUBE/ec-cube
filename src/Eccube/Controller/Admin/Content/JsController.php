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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class JsController extends AbstractController
{
    /**
     * @Route("/%eccube_admin_route%/content/js", name="admin_content_js", methods={"GET", "POST"})
     * @Template("@admin/Content/js.twig")
     */
    public function index(Request $request)
    {
        $builder = $this->formFactory
            ->createBuilder(FormType::class)
            ->add('js', TextareaType::class, [
                'required' => false,
            ]);
        $form = $builder->getForm();
        $jsPath = $this->getParameter('eccube_html_dir').'/user_data/assets/js/customize.js';
        if (file_exists($jsPath) && is_writable($jsPath)) {
            $form->get('js')->setData(file_get_contents($jsPath));
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fs = new Filesystem();
            try {
                $fs->dumpFile($jsPath, $form->get('js')->getData());
                $this->addSuccess('admin.common.save_complete', 'admin');

                return $this->redirectToRoute('admin_content_js');
            } catch (IOException $e) {
                $message = trans('admin.common.save_error');
                $this->addError($message, 'admin');
                log_error($message, [$jsPath, $e]);
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
