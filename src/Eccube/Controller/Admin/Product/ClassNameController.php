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


namespace Eccube\Controller\Admin\Product;

use Eccube\Controller\AbstractController;
use Eccube\Entity\ClassName;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\ClassNameType;
use Eccube\Repository\ClassNameRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route(service=ClassNameController::class)
 */
class ClassNameController extends AbstractController
{
    /**
     * @var ClassNameRepository
     */
    protected $classNameRepository;

    /**
     * ClassNameController constructor.
     * @param ClassNameRepository $classNameRepository
     */
    public function __construct(ClassNameRepository $classNameRepository)
    {
        $this->classNameRepository = $classNameRepository;
    }


    /**
     * @Route("/%eccube_admin_route%/product/class_name", name="admin_product_class_name")
     * @Route("/%eccube_admin_route%/product/class_name/{id}/edit", requirements={"id" = "\d+"}, name="admin_product_class_name_edit")
     * @Template("@admin/Product/class_name.twig")
     */
    public function index(Request $request, $id = null)
    {
        if ($id) {
            $TargetClassName = $this->classNameRepository->find($id);
            if (!$TargetClassName) {
                throw new NotFoundHttpException(trans('classname.text.error.no_option'));
            }
        } else {
            $TargetClassName = new \Eccube\Entity\ClassName();
        }

        $builder = $this->formFactory
            ->createBuilder(ClassNameType::class, $TargetClassName);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'TargetClassName' => $TargetClassName,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_INDEX_INITIALIZE, $event);

        $ClassNames = $this->classNameRepository->getList();

        /**
         * 編集用フォーム
         */
        $forms = [];
        foreach ($ClassNames as $ClassName) {
            $id = $ClassName->getId();
            $forms[$id] = $this->formFactory->createNamed('class_name_'.$id, ClassNameType::class, $ClassName);
        }

        $form = $builder->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                log_info('商品規格登録開始', array($id));

                $this->classNameRepository->save($TargetClassName);

                log_info('商品規格登録完了', array($id));

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'TargetClassName' => $TargetClassName,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_INDEX_COMPLETE, $event);

                $this->addSuccess('admin.class_name.save.complete', 'admin');
                return $this->redirectToRoute('admin_product_class_name');
            }

            /**
             * 編集処理
             */
            foreach ($forms as $editForm) {
                $editForm->handleRequest($request);
                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $this->classNameRepository->save($editForm->getData());

                    $this->addSuccess('admin.class_name.save.complete', 'admin');

                    return $this->redirectToRoute('admin_product_class_name');
                }
            }
        }
        $formViews = [];
        foreach ($forms as $key => $value) {
            $formViews[$key] = $value->createView();
        }

        return [
            'form' => $form->createView(),
            'ClassNames' => $ClassNames,
            'TargetClassName' => $TargetClassName,
            'forms' => $formViews,
        ];
    }

    /**
     * @Method("DELETE")
     * @Route("/%eccube_admin_route%/product/class_name/{id}/delete", requirements={"id" = "\d+"}, name="admin_product_class_name_delete")
     */
    public function delete(Request $request, ClassName $ClassName)
    {
        $this->isTokenValid();

        log_info('商品規格削除開始', array($ClassName->getId()));

        try {
            $this->classNameRepository->delete($ClassName);

            $event = new EventArgs(['ClassName' => $ClassName,], $request);
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_DELETE_COMPLETE, $event);

            $this->addSuccess('admin.class_name.delete.complete', 'admin');

            log_info('商品規格削除完了', array($ClassName->getId()));

        } catch (\Exception $e) {
            $message = trans('admin.delete.failed.foreign_key', ['%name%' => trans('classname.text.name')]);
            $this->addError($message, 'admin');

            log_error('商品企画削除エラー', [$ClassName->getId(), $e]);
        }

        return $this->redirectToRoute('admin_product_class_name');
    }

    /**
     * @Method("POST")
     * @Route("/%eccube_admin_route%/product/class_name/sort_no/move", name="admin_product_class_name_sort_no_move")
     */
    public function moveSortNo(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $sortNos = $request->request->all();
            foreach ($sortNos as $classNameId => $sortNo) {
                $ClassName = $this->classNameRepository
                    ->find($classNameId);
                $ClassName->setSortNo($sortNo);
                $this->entityManager->persist($ClassName);
            }
            $this->entityManager->flush();
        }

        return new Response();
    }
}
