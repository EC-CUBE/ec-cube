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

use Eccube\Annotation\Inject;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Tag;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\ProductTag;
use Eccube\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TagController extends AbstractController
{
    /**
     * @var TagRepository
     */
    protected $tagRepository;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(TagRepository $tagRepository, ValidatorInterface $validator)
    {
        $this->tagRepository = $tagRepository;
        $this->validator = $validator;
    }

    /**
     * @Route("/%eccube_admin_route%/product/tag", name="admin_product_tag")
     * @Route("/%eccube_admin_route%/product/tag/{id}/edit", requirements={"id" = "\d+"}, name="admin_product_tag_edit")
     * @Template("@admin/Product/tag.twig")
     *
     * @param Request $request
     * @param Tag|null $Tag
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Request $request, Tag $Tag = null)
    {
        $Tags = $this->tagRepository->getList();
        if (!$Tag) {
            $Tag = new Tag();
        }

        $builder = $this->formFactory
            ->createBuilder(ProductTag::class, $Tag);
        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Tag' => $Tag,
            ),
            $request
        );

        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_TAG_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $this->tagRepository->save($Tag);
                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'Tag' => $Tag,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_TAG_INDEX_COMPLETE, $event);

                $this->addSuccess('admin.tag.save.complete', 'admin');
                return $this->redirectToRoute('admin_product_tag');
            }
        }

        return [
            'form' => $form->createView(),
            'Tag' => $Tag,
            'Tags' => $Tags,
        ];
    }

    /**
     * @Method("DELETE")
     * @Route("/%eccube_admin_route%/product/tag/{id}/delete", requirements={"id" = "\d+"}, name="admin_product_tag_delete")
     */
    public function delete(Request $request, Tag $Tag)
    {
        $this->isTokenValid();

        log_info('タグ削除開始', array($Tag->getId()));

        try {
            $this->tagRepository->delete($Tag);

            $event = new EventArgs(
                array(
                    'Tag' => $Tag,
                ), $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_TAG_DELETE_COMPLETE, $event);

            $this->addSuccess('admin.tag.delete.complete', 'admin');

            log_info('タグ削除完了', array($Tag->getId()));

        } catch (\Exception $e) {
            log_info('タグ削除エラー', [$Tag->getId(), $e]);

            $message = trans('admin.delete.failed.foreign_key', ['%name%' => trans('tag.text.name')]);
            $this->addError($message, 'admin');
        }

        return $this->redirectToRoute('admin_product_tag');
    }

    /**
     * @Method("POST")
     * @Route("/%eccube_admin_route%/product/tag/sort_no/move", name="admin_product_tag_sort_no_move")
     */
    public function moveSortNo(Request $request)
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

        return new Response();
    }
}
