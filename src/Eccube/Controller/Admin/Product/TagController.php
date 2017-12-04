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
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Eccube\Entity\Tag;
use Eccube\Repository\TagRepository;
use Eccube\Form\Type\Admin\TagType;

/**
 * @Route(service=TagController::class)
 */
class TagController extends AbstractController
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
     * @Inject(TagRepository::class)
     * @var TagRepository
     */
    protected $tagRepository;

    /**
     * @Route("/{_admin}/product/tag", name="admin_product_tag")
     * @Route("/{_admin}/product/tag/{id}/edit", requirements={"id" = "\d+"}, name="admin_product_tag_edit")
     * @Template("Product/tag.twig")
     */
    public function index(Application $app, Request $request, Tag $TargetTag = null)
    {
        
        if (is_null($TargetTag)) {
            $TargetTag = new Tag();
        }
        
        $builder = $this->formFactory
            ->createBuilder(TagType::class, $TargetTag);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'TargetTag' => $TargetTag,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_TAG_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        //
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
               
                log_info('タグ登録開始', array($TargetTag->getId()));
                
                $this->tagRepository->save($TargetTag);

                log_info('タグ登録完了', array($TargetTag->getId()));

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'TargetTag' => $TargetTag,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_TAG_INDEX_COMPLETE, $event);

                $app->addSuccess('admin.tag.save.complete', 'admin');

                return $app->redirect($app->url('admin_product_tag'));
            }
        }

        $Tags = $this->tagRepository->getList();

        return [
            'form' => $form->createView(),
            'Tags' => $Tags,
            'TargetTag' => $TargetTag,
        ];
    }

    /**
     * @Method("DELETE")
     * @Route("/{_admin}/product/tag/{id}/delete", requirements={"id" = "\d+"}, name="admin_product_tag_delete")
     */
    public function delete(Application $app, Request $request, Tag $TargetTag)
    {
        $this->isTokenValid($app);

        log_info('タグ削除開始', array($TargetTag->getId()));

        try {
            $this->tagRepository->delete($TargetTag);

            $event = new EventArgs(
                array(
                    'TargetTag' => $TargetTag,
                ), $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_TAG_DELETE_COMPLETE, $event);

            $app->addSuccess('admin.tag.delete.complete', 'admin');

            log_info('タグ削除完了', array($TargetTag->getId()));

        } catch (\Exception $e) {
            log_info('タグ削除エラー', [$TargetTag->getId(), $e]);

            $message = $app->trans('admin.delete.failed.foreign_key', ['%name%' => 'タグ']);
            $app->addError($message, 'admin');
        }

        return $app->redirect($app->url('admin_product_tag'));
    }

    /**
     * @Method("POST")
     * @Route("/{_admin}/product/tag/sort_no/move", name="admin_product_tag_sort_no_move")
     */
    public function moveSortNo(Application $app, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $sortNos = $request->request->all();
            foreach ($sortNos as $tagId => $sortNo) {
                /* @var $Tag \Eccube\Entity\Tag */
                $Tag = $this->tagRepository
                    ->find($tagId);
                $Tag->setSortNo($sortNo);
                $this->entityManager->persist($Tag);
            }
            $this->entityManager->flush();
        }
        return true;
    }
}
