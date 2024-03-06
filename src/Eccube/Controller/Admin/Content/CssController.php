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

class CssController extends AbstractController
{
    /**
     * @Route("/%eccube_admin_route%/content/css", name="admin_content_css", methods={"GET", "POST"})
     * @Template("@admin/Content/css.twig")
     */
    public function index(Request $request)
    {
        $this->addInfoOnce('admin.common.restrict_file_upload_info', 'admin');

        $builder = $this->formFactory
            ->createBuilder(FormType::class)
            ->add('css', TextareaType::class, [
                'required' => false,
            ]);
        $form = $builder->getForm();

        $cssPath = $this->getParameter('eccube_html_dir').'/user_data/assets/css/customize.css';
        if (file_exists($cssPath) && is_writable($cssPath)) {
            $form->get('css')->setData(file_get_contents($cssPath));
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fs = new Filesystem();
            try {
                $fs->dumpFile($cssPath, $form->get('css')->getData());
                $this->addSuccess('admin.common.save_complete', 'admin');

                return $this->redirectToRoute('admin_content_css');
            } catch (IOException $e) {
                $message = trans('admin.common.save_error');
                $this->addError($message, 'admin');
                log_error($message, [$cssPath, $e]);
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
