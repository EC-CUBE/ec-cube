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
use Eccube\Entity\Block;
use Eccube\Entity\Master\DeviceType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\BlockType;
use Eccube\Repository\BlockRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Service\Content\BlockContentService;
use Eccube\Util\CacheUtil;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;
use Twig\Error\LoaderError;

class BlockController extends AbstractController
{
    public function __construct(protected BlockRepository $blockRepository, protected DeviceTypeRepository $deviceTypeRepository, private readonly Environment $twig, private readonly CacheUtil $cacheUtil, private readonly BlockContentService $blockContentService)
    {
    }

    /**
     * @return array<string, mixed>
     */
    #[Route(path: '/%eccube_admin_route%/content/block', name: 'admin_content_block', methods: ['GET'])]
    #[Template(template: '@admin/Content/block.twig')]
    public function index(Request $request): array
    {
        $DeviceType = $this->deviceTypeRepository
            ->find(DeviceType::DEVICE_TYPE_PC);

        // 登録されているブロック一覧の取得
        $Blocks = $this->blockRepository->getList($DeviceType);

        $event = new EventArgs(
            [
                'DeviceType' => $DeviceType,
                'Blocks' => $Blocks,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CONTENT_BLOCK_INDEX_COMPLETE);

        return [
            'Blocks' => $Blocks,
        ];
    }

    /**
     * @param int|null $id
     *
     * @return RedirectResponse|array<string, mixed>
     *
     * @throws NotFoundHttpException|LoaderError
     */
    #[Route(path: '/%eccube_admin_route%/content/block/new', name: 'admin_content_block_new', methods: ['GET', 'POST'])]
    #[Route(path: '/%eccube_admin_route%/content/block/{id}/edit', name: 'admin_content_block_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Content/block_edit.twig')]
    public function edit(Request $request, $id = null): RedirectResponse|array
    {
        $this->addInfoOnce('admin.common.restrict_file_upload_info', 'admin');

        $DeviceType = $this->deviceTypeRepository
            ->find(DeviceType::DEVICE_TYPE_PC);

        if (null === $id) {
            $Block = $this->blockRepository->newBlock($DeviceType);
        } else {
            $Block = $this->blockRepository->findOneBy(
                [
                    'id' => $id,
                    'DeviceType' => $DeviceType,
                ]
            );
        }

        if (!$Block) {
            throw new NotFoundHttpException();
        }

        $builder = $this->formFactory
            ->createBuilder(BlockType::class, $Block);

        $html = '';
        $previousFileName = null;

        if ($id) {
            $previousFileName = $Block->getFileName();
            $html = $this->twig->getLoader()
                ->getSourceContext('Block/'.$Block->getFileName().'.twig')
                ->getCode();
        }

        $event = new EventArgs(
            [
                'builder' => $builder,
                'DeviceType' => $DeviceType,
                'Block' => $Block,
                'html' => $html,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CONTENT_BLOCK_EDIT_INITIALIZE);
        $html = $event->getArgument('html');

        $form = $builder->getForm();
        $form->get('block_html')->setData($html);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Block = $form->getData();
            // DB 登録とテンプレートファイルの生成は Service に委譲する
            $this->blockContentService->save($Block, (string) $form->get('block_html')->getData(), $previousFileName);

            // キャッシュの削除
            $this->cacheUtil->clearTwigCache();
            $this->cacheUtil->clearDoctrineCache();

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Block' => $Block,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CONTENT_BLOCK_EDIT_COMPLETE);

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_content_block_edit', ['id' => $Block->getId()]);
        }

        return [
            'form' => $form->createView(),
            'block_id' => $id,
            'deletable' => $Block->isDeletable(),
        ];
    }

    #[Route(path: '/%eccube_admin_route%/content/block/{id}/delete', name: 'admin_content_block_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(Request $request, Block $Block): RedirectResponse
    {
        $this->isTokenValid();

        // ユーザーが作ったブロックのみ削除する
        if ($Block->isDeletable()) {
            $this->blockContentService->remove($Block);

            $event = new EventArgs(
                [
                    'Block' => $Block,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CONTENT_BLOCK_DELETE_COMPLETE);

            $this->addSuccess('admin.common.delete_complete', 'admin');

            // キャッシュの削除
            $this->cacheUtil->clearTwigCache();
            $this->cacheUtil->clearDoctrineCache();
        }

        return $this->redirectToRoute('admin_content_block');
    }
}
