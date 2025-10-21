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

namespace Eccube\Controller\Admin\Product;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Tag;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\ProductTag;
use Eccube\Repository\TagRepository;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TagController extends AbstractController
{
    /**
     * @var TagRepository
     */
    protected $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @param Request $request
     *
     * @return array<string, mixed>|RedirectResponse
     */
    #[Route(path: '/%eccube_admin_route%/product/tag', name: 'admin_product_tag', methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Product/tag.twig')]
    public function index(Request $request): array|RedirectResponse
    {
        $Tag = new Tag();
        $Tags = $this->tagRepository->getList();

        /**
         * 新規登録用フォーム
         **/
        $builder = $this->formFactory
            ->createBuilder(ProductTag::class, $Tag);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Tag' => $Tag,
            ],
            $request
        );

        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_PRODUCT_TAG_INDEX_INITIALIZE);

        $form = $builder->getForm();

        /**
         * 編集用フォーム
         */
        $forms = [];
        foreach ($Tags as $EditTag) {
            $id = $EditTag->getId();
            $forms[$id] = $this
                ->formFactory
                ->createNamed('tag_'.$id, ProductTag::class, $EditTag);
        }

        if ('POST' === $request->getMethod()) {
            /*
             * 登録処理
             */
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->tagRepository->save($form->getData());

                $this->dispatchComplete($request, $form, $form->getData());

                $this->addSuccess('admin.common.save_complete', 'admin');

                return $this->redirectToRoute('admin_product_tag');
            }
            /*
             * 編集処理
             */
            foreach ($forms as $editForm) {
                $editForm->handleRequest($request);
                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $this->tagRepository->save($editForm->getData());

                    $this->dispatchComplete($request, $editForm, $editForm->getData());

                    $this->addSuccess('admin.common.save_complete', 'admin');

                    return $this->redirectToRoute('admin_product_tag');
                }
            }
        }

        $formViews = [];
        foreach ($forms as $key => $value) {
            $formViews[$key] = $value->createView();
        }

        return [
            'form' => $form->createView(),
            'Tag' => $Tag,
            'Tags' => $Tags,
            'forms' => $formViews,
        ];
    }

    /**
     * @param Request $request
     * @param Tag $Tag
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    #[Route(path: '/%eccube_admin_route%/product/tag/{id}/delete', name: 'admin_product_tag_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(Request $request, Tag $Tag): RedirectResponse
    {
        $this->isTokenValid();

        log_info('タグ削除開始', [$Tag->getId()]);

        try {
            $this->tagRepository->delete($Tag);

            $event = new EventArgs(
                [
                    'Tag' => $Tag,
                ], $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_PRODUCT_TAG_DELETE_COMPLETE);

            $this->addSuccess('admin.common.delete_complete', 'admin');

            log_info('タグ削除完了', [$Tag->getId()]);
        } catch (\Exception $e) {
            log_info('タグ削除エラー', [$Tag->getId(), $e]);

            $message = trans('admin.common.delete_error_foreign_key', ['%name%' => $Tag->getName()]);
            $this->addError($message, 'admin');
        }

        return $this->redirectToRoute('admin_product_tag');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[Route(path: '/%eccube_admin_route%/product/tag/sort_no/move', name: 'admin_product_tag_sort_no_move', methods: ['POST'])]
    public function moveSortNo(Request $request): Response
    {
        if ($request->isXmlHttpRequest() && $this->isTokenValid()) {
            $sortNos = $request->request->all();
            foreach ($sortNos as $tagId => $sortNo) {
                /** @var Tag $Tag */
                $Tag = $this->tagRepository
                    ->find($tagId);
                $Tag->setSortNo($sortNo);
                $this->entityManager->persist($Tag);
            }
            $this->entityManager->flush();
        }

        return new Response();
    }

    /**
     * @param Request $request
     * @param FormInterface $form
     * @param Tag $Tag
     *
     * @return void
     */
    protected function dispatchComplete(Request $request, FormInterface $form, Tag $Tag): void
    {
        $event = new EventArgs(
            [
                'form' => $form,
                'Tag' => $Tag,
            ],
            $request
        );

        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_PRODUCT_TAG_INDEX_COMPLETE);
    }
}
