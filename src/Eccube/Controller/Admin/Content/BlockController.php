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

use Eccube\Controller\AbstractController;
use Eccube\Entity\Block;
use Eccube\Entity\Master\DeviceType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\BlockType;
use Eccube\Repository\BlockRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class BlockController extends AbstractController
{
    /**
     * @var BlockRepository
     */
    protected $blockRepository;

    /**
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    public function __construct(
        BlockRepository $blockRepository,
        DeviceTypeRepository $deviceTypeRepository
    ) {
        $this->blockRepository = $blockRepository;
        $this->deviceTypeRepository = $deviceTypeRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/content/block", name="admin_content_block")
     * @Template("@admin/Content/block.twig")
     */
    public function index(Request $request)
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
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_BLOCK_INDEX_COMPLETE, $event);

        return [
            'Blocks' => $Blocks,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/content/block/new", name="admin_content_block_new")
     * @Route("/%eccube_admin_route%/content/block/{id}/edit", requirements={"id" = "\d+"}, name="admin_content_block_edit")
     * @Template("@admin/Content/block_edit.twig")
     */
    public function edit(Request $request, $id = null, Environment $twig, FileSystem $fs)
    {
        $DeviceType = $this->deviceTypeRepository
            ->find(DeviceType::DEVICE_TYPE_PC);

        $Block = $this->blockRepository
            ->findOrCreate($id, $DeviceType);

        if (!$Block) {
            throw new NotFoundHttpException();
        }

        $builder = $this->formFactory
            ->createBuilder(BlockType::class, $Block);

        $html = '';
        $previousFileName = null;

        if ($id) {
            $previousFileName = $Block->getFileName();
            $html = $twig->getLoader()
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
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_BLOCK_EDIT_INITIALIZE, $event);
        $html = $event->getArgument('html');

        $form = $builder->getForm();
        $form->get('block_html')->setData($html);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Block = $form->getData();
            $this->entityManager->persist($Block);
            $this->entityManager->flush();

            $dir = sprintf('%s/app/template/%s/Block',
                $this->getParameter('kernel.project_dir'),
                $this->getParameter('eccube.theme'));

            $file = $dir.'/'.$Block->getFileName().'.twig';

            $source = $form->get('block_html')->getData();
            $source = StringUtil::convertLineFeed($source);
            $fs->dumpFile($file, $source);

            // 更新でファイル名を変更した場合、以前のファイルを削除
            if (null !== $previousFileName && $Block->getFileName() !== $previousFileName) {
                $old = $dir.'/'.$previousFileName.'.twig';
                if ($fs->exists($old)) {
                    $fs->remove($old);
                }
            }

            // twigキャッシュの削除
            $cacheDir = $this->getParameter('kernel.cache_dir').'/twig';
            $fs->remove($cacheDir);

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Block' => $Block,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_BLOCK_EDIT_COMPLETE, $event);

            $this->addSuccess('admin.register.complete', 'admin');

            return $this->redirectToRoute('admin_content_block_edit', ['id' => $Block->getId()]);
        }

        return [
            'form' => $form->createView(),
            'block_id' => $id,
            'deletable' => $Block->isDeletable(),
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/content/block/{id}/delete", requirements={"id" = "\d+"}, name="admin_content_block_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Block $Block, Filesystem $fs)
    {
        $this->isTokenValid();

        // ユーザーが作ったブロックのみ削除する
        if ($Block->isDeletable()) {
            $dir = sprintf('%s/app/template/%s/Block',
                $this->getParameter('kernel.project_dir'),
                $this->getParameter('eccube.theme'));

            $file = $dir.'/'.$Block->getFileName().'.twig';

            if ($fs->exists($file)) {
                $fs->remove($file);
            }

            $this->entityManager->remove($Block);
            $this->entityManager->flush();

            $event = new EventArgs(
                [
                    'Block' => $Block,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_BLOCK_DELETE_COMPLETE, $event);

            $this->addSuccess('admin.delete.complete', 'admin');

            // twigキャッシュの削除
            $cacheDir = $this->getParameter('kernel.cache_dir').'/twig';
            $fs->remove($cacheDir);
        }

        return $this->redirectToRoute('admin_content_block');
    }
}
