<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Admin\Content;

use Doctrine\ORM\NoResultException;
use Eccube\Controller\AbstractController;
use Eccube\Entity\BlockPosition;
use Eccube\Entity\Layout;
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\NotBlank;
use Twig\Environment as Twig;

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
     *
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
            $Layout = $this->layoutRepository->findById($id);
        }

        // 未使用ブロックの取得
        $Blocks = $Layout->getBlocks();
        if (empty($Blocks)) {
            $UnusedBlocks = $this->blockRepository->findAll();
        } else {
            $UnusedBlocks = $this->blockRepository->getUnusedBlocks($Layout);
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

        if ($form->isSubmitted()) {
            // Layoutの更新
            $Layout = $form->getData();
            // ブロックの個数分登録を行う.
            $data = $request->request->all();
            // 削除対象ブロックポジション
            $RemovedBlockPositions = [];
            for ($i = 0; $i <count($data); $i++) {
                // ブロックidが取得できない場合はinsertしない
                if (!isset($data['block_id_'.$i])) {
                    continue;
                }

                // ブロック情報を取得
                $Block = $this->blockRepository->find($data['block_id_'.$i]);

                // 未使用ブロックの場合
                if ($data['section_'.$i] == \Eccube\Entity\Page::TARGET_ID_UNUSED) {
                    // 配置済み → 未使用ブロックとされた場合、未使用ブロック配列に追加
                    $is_exist_unused_block = false;
                    foreach($UnusedBlocks as $UnusedBlock) {
                        if ($UnusedBlock->getId() == $Block->getId()) {
                            $is_exist_unused_block = true;
                            break;
                        }
                    }

                    if (!$is_exist_unused_block) {
                        $UnusedBlocks[] = $Block;
                    }

                    // 未使用ブロックIdをキーとして、レイアウト内に配置されているブロックポジションを取得
                    $RemovedBlockPosition = $Layout->removeBlockPositionByBlockId($Block->getId());

                    // レイアウト内に未使用化されたブロックが存在する場合、永続化処理でまとめて削除する為、
                    // レイアウトから外れたブロックポジションを保持
                    if ($RemovedBlockPosition) {
                        $RemovedBlockPositions[] = $RemovedBlockPosition;
                    }

                    continue;
                }
                
                // 未使用 → 配置済みブロックとされた場合、未使用ブロックリストより削除
                foreach ($UnusedBlocks as $unUsedBlockKey => $UnusedBlock) {
                    if ($Block->getId() == $UnusedBlock->getId()) {
                        unset($UnusedBlocks[$unUsedBlockKey]);
                    }
                }

                // 配置済みブロックが、レイアウト内に存在しているかチェック
                $BlockPositions = $Layout->getBlockPositions();
                $BlockPositions = $BlockPositions->filter(
                    function ($BlockPosition) use ($Block) {
                        return $BlockPosition->getBlock()->getId() == $Block->getId();
                    }
                );
    
                if ($BlockPositions && $BlockPositions->count() > 0) {
                    // 配置済みブロックの場合は、レイアウト内のブロックポジションをリクエストで更新
                    $BlockPosition = current($BlockPositions->toArray());
                    $BlockPosition
                        ->setBlockId($data['block_id_'.$i])
                        ->setBlockRow($data['block_row_'.$i])
                        ->setSection($data['section_'.$i])
                        ->setBlock($Block)
                        ->setLayout($Layout);
                } else {
                    // 新規配置ブロックの場合は、リクエストから新規ブロックポジションを生成し、レイアウトへ登録
                    $BlockPosition = new BlockPosition();
                    $BlockPosition
                        ->setBlockId($data['block_id_'.$i])
                        ->setLayoutId($Layout->getId())
                        ->setBlockRow($data['block_row_'.$i])
                        ->setSection($data['section_'.$i])
                        ->setBlock($Block)
                        ->setLayout($Layout);
    
                    $Layout->addBlockPosition($BlockPosition);
                }
            }

            // 正常データかつ登録・更新ボタンクリックの場合は永続化を実行
            if ($form->isValid()) {
                // レイアウトを更新
                $this->entityManager->persist($Layout);
                $this->entityManager->flush($Layout);

                // 未使用化されたブロックポジションを削除
                foreach($RemovedBlockPositions as $BlockPosition) {
                    $this->entityManager->remove($BlockPosition);
                    $this->entityManager->flush($BlockPosition);
                }

                // 配置されているブロックポジションを更新
                $BlockPositions = $Layout->getBlockPositions();

                foreach($BlockPositions as $BlockPosition) {
                    $this->entityManager->persist($BlockPosition);
                    $this->entityManager->flush($BlockPosition);
                }

                $this->addSuccess('admin.register.complete', 'admin');

                return $this->redirectToRoute('admin_content_layout_edit', ['id' => $Layout->getId()]);
            }
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
}
