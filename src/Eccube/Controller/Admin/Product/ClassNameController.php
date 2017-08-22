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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Component;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\ClassNameType;
use Eccube\Repository\ClassNameRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Component
 * @Route(service=ClassNameController::class)
 */
class ClassNameController extends AbstractController
{
    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject(ClassNameRepository::class)
     * @var ClassNameRepository
     */
    protected $classNameRepository;

    /**
     * @Route("/{_admin}/product/class_name", name="admin_product_class_name")
     * @Route("/{_admin}/product/class_name/{id}/edit", requirements={"id" = "\d+"}, name="admin_product_class_name_edit")
     * @Template("Product/class_name.twig")
     */
    public function index(Application $app, Request $request, $id = null)
    {
        if ($id) {
            $TargetClassName = $this->classNameRepository->find($id);
            if (!$TargetClassName) {
                throw new NotFoundHttpException('商品規格が存在しません');
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

        $form = $builder->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                log_info('商品規格登録開始', array($id));
                $status = $this->classNameRepository->save($TargetClassName);

                if ($status) {
                    log_info('商品規格登録完了', array($id));

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'TargetClassName' => $TargetClassName,
                        ),
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_INDEX_COMPLETE, $event);

                    $app->addSuccess('admin.class_name.save.complete', 'admin');

                    return $app->redirect($app->url('admin_product_class_name'));
                } else {
                    log_info('商品規格登録エラー', array($id));
                    $app->addError('admin.class_name.save.error', 'admin');
                }
            }
        }

        $ClassNames = $this->classNameRepository->getList();

        return [
            'form' => $form->createView(),
            'ClassNames' => $ClassNames,
            'TargetClassName' => $TargetClassName,
        ];
    }

    /**
     * @Method("DELETE")
     * @Route("/{_admin}/product/class_name/{id}/delete", requirements={"id" = "\d+"}, name="admin_product_class_name_delete")
     */
    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $TargetClassName = $this->classNameRepository->find($id);
        if (!$TargetClassName) {
            $app->deleteMessage();
            return $app->redirect($app->url('admin_product_class_name'));
        }

        log_info('商品規格削除開始', array($id));

        $status = $this->classNameRepository->delete($TargetClassName);

        if ($status === true) {
            log_info('商品規格削除完了', array($id));

            $event = new EventArgs(
                array(
                    'TargetClassName' => $TargetClassName,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_DELETE_COMPLETE, $event);

            $app->addSuccess('admin.class_name.delete.complete', 'admin');
        } else {
            $app->addError('admin.class_name.delete.error', 'admin');
        }

        return $app->redirect($app->url('admin_product_class_name'));
    }

    /**
     * @Method("POST")
     * @Route("/{_admin}/product/class_name/rank/move", name="admin_product_class_name_rank_move")
     */
    public function moveRank(Application $app, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $ranks = $request->request->all();
            foreach ($ranks as $classNameId => $rank) {
                $ClassName = $this->classNameRepository
                    ->find($classNameId);
                $ClassName->setRank($rank);
                $this->entityManager->persist($ClassName);
            }
            $this->entityManager->flush();
        }
        return true;
    }
}
