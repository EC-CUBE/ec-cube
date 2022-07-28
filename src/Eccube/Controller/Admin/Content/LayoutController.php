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

use Doctrine\ORM\NoResultException;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Layout;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Form\Type\Admin\LayoutType;
use Eccube\Repository\BlockPositionRepository;
use Eccube\Repository\BlockRepository;
use Eccube\Repository\LayoutRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\PageLayoutRepository;
use Eccube\Repository\PageRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Util\CacheUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment as Twig;

class LayoutController extends AbstractController
{
    public const DUMMY_BLOCK_ID = 9999999999;

    /**
     * @var BlockRepository
     */
    protected $blockRepository;
    /**
     * @var BlockPositionRepository
     */
    protected $blockPositionRepository;

    /**
     * @var LayoutRepository
     */
    protected $layoutRepository;

    /**
     * @var PageLayoutRepository
     */
    protected $pageLayoutRepository;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * @var boolean
     */
    protected $isPreview = false;

    /**
     * LayoutController constructor.
     *
     * @param BlockRepository $blockRepository
     * @param LayoutRepository $layoutRepository
     * @param PageLayoutRepository $pageLayoutRepository
     * @param pageRepository $pageRepository
     * @param ProductRepository $productRepository
     * @param DeviceTypeRepository $deviceTypeRepository
     */
    public function __construct(BlockRepository $blockRepository, BlockPositionRepository $blockPositionRepository, LayoutRepository $layoutRepository, PageLayoutRepository $pageLayoutRepository, PageRepository $pageRepository, ProductRepository $productRepository, DeviceTypeRepository $deviceTypeRepository)
    {
        $this->blockRepository = $blockRepository;
        $this->blockPositionRepository = $blockPositionRepository;
        $this->layoutRepository = $layoutRepository;
        $this->pageLayoutRepository = $pageLayoutRepository;
        $this->pageRepository = $pageRepository;
        $this->productRepository = $productRepository;
        $this->deviceTypeRepository = $deviceTypeRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/content/layout", name="admin_content_layout", methods={"GET"})
     * @Template("@admin/Content/layout_list.twig")
     */
    public function index()
    {
        $qb = $this->layoutRepository->createQueryBuilder('l');
        $Layouts = $qb->where('l.id != :DefaultLayoutPreviewPage')
                    ->orderBy('l.DeviceType', 'DESC')
                    ->addOrderBy('l.id', 'ASC')
                    ->setParameter('DefaultLayoutPreviewPage', Layout::DEFAULT_LAYOUT_PREVIEW_PAGE)
                    ->getQuery()
                    ->getResult();

        return [
            'Layouts' => $Layouts,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/content/layout/{id}/delete", requirements={"id" = "\d+"}, name="admin_content_layout_delete", methods={"DELETE"})
     *
     * @param Layout $Layout
     *
     * @return RedirectResponse
     */
    public function delete(Layout $Layout, CacheUtil $cacheUtil)
    {
        $this->isTokenValid();

        /** @var Layout $Layout */
        if (!$Layout->isDeletable()) {
            $this->addWarning(trans('admin.common.delete_error_foreign_key', ['%name%' => $Layout->getName()]), 'admin');

            return $this->redirectToRoute('admin_content_layout');
        }

        $this->entityManager->remove($Layout);
        $this->entityManager->flush();

        $this->addSuccess('admin.common.delete_complete', 'admin');

        // キャッシュの削除
        $cacheUtil->clearDoctrineCache();

        return $this->redirectToRoute('admin_content_layout');
    }

    /**
     * @Route("/%eccube_admin_route%/content/layout/new", name="admin_content_layout_new", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/content/layout/{id}/edit", requirements={"id" = "\d+"}, name="admin_content_layout_edit", methods={"GET", "POST"})
     * @Template("@admin/Content/layout.twig")
     */
    public function edit(Request $request, CacheUtil $cacheUtil, $id = null, $previewPageId = null)
    {
        if (is_null($id)) {
            $Layout = new Layout();
        } else {
            $Layout = $this->layoutRepository->get($this->isPreview ? 0 : $id);
            if (is_null($Layout)) {
                throw new NotFoundHttpException();
            }
        }

        // 未使用ブロックの取得
        $Blocks = $Layout->getBlocks();
        if (empty($Blocks)) {
            $UnusedBlocks = $this->blockRepository->findAll();
        } else {
            $UnusedBlocks = $this->blockRepository->getUnusedBlocks($Blocks);
        }

        $builder = $this->formFactory->createBuilder(LayoutType::class, $Layout, ['layout_id' => $id]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Layoutの更新
            $Layout = $form->getData();
            $this->entityManager->persist($Layout);
            $this->entityManager->flush();

            // BlockPositionの更新
            // delete/insertのため、一度削除する.
            $BlockPositions = $Layout->getBlockPositions();
            foreach ($BlockPositions as $BlockPosition) {
                $Layout->removeBlockPosition($BlockPosition);
                $this->entityManager->remove($BlockPosition);
                $this->entityManager->flush();
            }

            // ブロックの個数分登録を行う.
            $data = $request->request->all();
            $this->blockPositionRepository->register($data, $Blocks, $UnusedBlocks, $Layout);

            // キャッシュの削除
            $cacheUtil->clearDoctrineCache();

            // プレビューモード
            if ($this->isPreview) {
                // プレビューする画面を取得
                try {
                    $Page = $this->pageRepository->find($previewPageId);
                } catch (NoResultException $e) {
                    throw new NotFoundHttpException();
                }

                if ($Page->getEditType() >= \Eccube\Entity\Page::EDIT_TYPE_DEFAULT) {
                    if ($Page->getUrl() === 'product_detail') {
                        $product = $this->productRepository->findOneBy(['Status' => ProductStatus::DISPLAY_SHOW]);
                        if (is_null($product)) {
                            throw new NotFoundHttpException();
                        }

                        return $this->redirectToRoute($Page->getUrl(), ['preview' => 1, 'id' => $product->getId()]);
                    } else {
                        return $this->redirectToRoute($Page->getUrl(), ['preview' => 1]);
                    }
                }

                return $this->redirectToRoute('user_data', ['route' => $Page->getUrl(), 'preview' => 1]);
            }

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_content_layout_edit', ['id' => $Layout->getId()]);
        }

        return [
            'form' => $form->createView(),
            'Layout' => $Layout,
            'UnusedBlocks' => $UnusedBlocks,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/content/layout/view_block", name="admin_content_layout_view_block", methods={"GET"})
     *
     * @param Request $request
     * @param Twig $twig
     *
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

        return $this->json([
            'id' => $Block->getId(),
            'source' => $source,
        ]);
    }

    /**
     * @Route("/%eccube_admin_route%/content/layout/{id}/preview", requirements={"id" = "\d+"}, name="admin_content_layout_preview", methods={"POST"})
     */
    public function preview(Request $request, $id, CacheUtil $cacheUtil)
    {
        $form = $request->get('admin_layout');
        $this->isPreview = true;

        return $this->edit($request, $cacheUtil, $id, $form['Page']);
    }
}
