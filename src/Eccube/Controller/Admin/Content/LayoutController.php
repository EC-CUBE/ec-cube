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

use Doctrine\ORM\NoResultException;
use Eccube\Controller\AbstractController;
use Eccube\Entity\BlockPosition;
use Eccube\Entity\Layout;
use Eccube\Entity\Master\DeviceType;
use Eccube\Form\Type\Master\DeviceTypeType;
use Eccube\Repository\BlockRepository;
use Eccube\Repository\LayoutRepository;
use Eccube\Repository\PageLayoutRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\NotBlank;
use Twig\Environment as Twig;
use Symfony\Component\HttpFoundation\RedirectResponse;

// todo プレビュー実装
class LayoutController extends AbstractController
{
    const DUMMY_BLOCK_ID = 9999999999;

    /**
     * @var BlockRepository
     */
    protected $blockRepository;

    /**
     * @var LayoutRepository
     */
    protected $layoutRepository;

    /**
     * @var PageLayoutRepository
     */
    protected $pageLayoutRepository;

    /**
     * LayoutController constructor.
     * @param BlockRepository $blockRepository
     * @param LayoutRepository $layoutRepository
     * @param PageLayoutRepository $pageLayoutRepository
     */
    public function __construct(BlockRepository $blockRepository, LayoutRepository $layoutRepository, PageLayoutRepository $pageLayoutRepository)
    {
        $this->blockRepository = $blockRepository;
        $this->layoutRepository = $layoutRepository;
        $this->pageLayoutRepository = $pageLayoutRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/content/layout", name="admin_content_layout")
     * @Template("@admin/Content/layout_list.twig")
     */
    public function index()
    {
        $Layouts = $this->layoutRepository->findBy([], ['DeviceType' => 'DESC', 'id' => 'ASC']);

        return [
            'Layouts' => $Layouts,
        ];
    }

    /**
     * @Method("DELETE")
     * @Route("/%eccube_admin_route%/content/layout/{id}/delete", requirements={"id" = "\d+"}, name="admin_content_layout_delete")
     *
     * @param Layout $Layout
     *
     * @return RedirectResponse
     */
    public function delete(Layout $Layout)
    {
        $this->isTokenValid();

        /** @var Layout $Layout */
        if (!$Layout->isDeletable()) {
            $this->deleteMessage();

            return $this->redirectToRoute('admin_content_layout');
        }

        $this->entityManager->remove($Layout);
        $this->entityManager->flush($Layout);

        $this->addSuccess('admin.delete.complete', 'admin');

        return $this->redirectToRoute('admin_content_layout');
    }

    /**
     * @Route("/%eccube_admin_route%/content/layout/new", name="admin_content_layout_new")
     * @Route("/%eccube_admin_route%/content/layout/{id}/edit", requirements={"id" = "\d+"}, name="admin_content_layout_edit")
     * @Template("@admin/Content/layout.twig")
     */
    public function edit(Request $request, $id = null)
    {
        if (is_null($id)) {
            $Layout = new Layout();
        } else {
            // todo レポジトリへ移動
            try {
                $Layout = $this->layoutRepository->createQueryBuilder('l')
                    ->select('l, bp, b')
                    ->leftJoin('l.BlockPositions', 'bp')
                    ->leftJoin('bp.Block', 'b')
                    ->where('l.id = :layout_id')
                    ->orderBy('bp.block_row', 'ASC')
                    ->setParameter('layout_id', $id)
                    ->getQuery()
                    ->getSingleResult();
            } catch (NoResultException $e) {
                throw new NotFoundHttpException();
            }
        }

        // todo レポジトリへ移動
        // 未使用ブロックの取得
        $Blocks = $Layout->getBlocks();
        if (empty($Blocks)) {
            $UnusedBlocks = $this->blockRepository->findAll();
        } else {
            $UnusedBlocks = $this->blockRepository
                ->createQueryBuilder('b')
                ->select('b')
                ->where('b not in (:blocks)')
                ->setParameter('blocks', $Blocks)
                ->getQuery()
                ->getResult();
        }

        $builder = $this->formFactory->createBuilder(FormType::class, $Layout);
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'required' => false,
                    'label' => trans('layout.label'),
                ]
            )->add(
                'DeviceType',
                DeviceTypeType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'required' => false,
                ]
            );

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Layoutの更新
            $Layout = $form->getData();
            $this->entityManager->persist($Layout);
            $this->entityManager->flush($Layout);

            // BlockPositionの更新
            // delete/insertのため、一度削除する.
            $BlockPositions = $Layout->getBlockPositions();
            foreach ($BlockPositions as $BlockPosition) {
                $Layout->removeBlockPosition($BlockPosition);
                $this->entityManager->remove($BlockPosition);
                $this->entityManager->flush($BlockPosition);
            }

            // ブロックの個数分登録を行う.
            $max = count($Blocks) + count($UnusedBlocks);
            $data = $request->request->all();
            for ($i = 0; $i < $max; $i++) {
                // block_idが取得できない場合はinsertしない
                if (!isset($data['block_id_'.$i])) {
                    continue;
                }
                // 未使用ブロックはinsertしない
                if ($data['section_'.$i] == \Eccube\Entity\Page::TARGET_ID_UNUSED) {
                    continue;
                }
                $Block = $this->blockRepository->find($data['block_id_'.$i]);
                $BlockPosition = new BlockPosition();
                $BlockPosition
                    ->setBlockId($data['block_id_'.$i])
                    ->setLayoutId($Layout->getId())
                    ->setBlockRow($data['block_row_'.$i])
                    ->setSection($data['section_'.$i])
                    ->setBlock($Block)
                    ->setLayout($Layout);
                $Layout->addBlockPosition($BlockPosition);
                $this->entityManager->persist($BlockPosition);
                $this->entityManager->flush($BlockPosition);
            }

            $this->addSuccess('admin.register.complete', 'admin');

            return $this->redirectToRoute('admin_content_layout_edit', array('id' => $Layout->getId()));
        }

        return [
            'form' => $form->createView(),
            'Layout' => $Layout,
            'UnusedBlocks' => $UnusedBlocks,
        ];
    }

    /**
     * @Method("GET")
     * @Route("/%eccube_admin_route%/content/layout/view_block", name="admin_content_layout_view_block")
     *
     * @param Request $request
     * @param Twig $twig
     * @return JsonResponse
     */
    public function viewBlock(Request $request, Twig $twig)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        $id = $request->get('id');

        if (is_null($id)) {
            throw new BadRequestHttpException();
        }

        $Block = $this->blockRepository->find($id);

        if (null === $Block) {
            throw new NotFoundHttpException();
        }

        $source = $twig->getLoader()
                ->getSourceContext('Block/'.$Block->getFileName().'.twig')
                ->getCode();

        return new JsonResponse([
            'id' => $Block->getId(),
            'source' => $source,
        ]);
    }
}
