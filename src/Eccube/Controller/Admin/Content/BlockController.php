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

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Block;
use Eccube\Entity\Master\DeviceType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\BlockType;
use Eccube\Repository\BlockRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

class BlockController extends AbstractController
{
    /**
     * @var array
     */
    protected $eccubeConfig;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var BlockRepository
     */
    protected $blockRepository;

    /**
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    public function __construct(
        $eccubeConfig,
        EntityManagerInterface $entityManager,
        BlockRepository $blockRepository,
        DeviceTypeRepository $deviceTypeRepository
    ) {
        $this->eccubeConfig = $eccubeConfig;
        $this->entityManager = $entityManager;
        $this->blockRepository = $blockRepository;
        $this->deviceTypeRepository = $deviceTypeRepository;
    }

    /**
     * @Route("/%admin_route%/content/block", name="admin_content_block")
     * @Template("@admin/Content/block.twig")
     */
    public function index(Request $request)
    {
        $DeviceType = $this->deviceTypeRepository
            ->find(DeviceType::DEVICE_TYPE_PC);

        // 登録されているブロック一覧の取得
        $Blocks = $this->blockRepository->getList($DeviceType);

        $event = new EventArgs(
            array(
                'DeviceType' => $DeviceType,
                'Blocks' => $Blocks,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_BLOCK_INDEX_COMPLETE, $event);

        return [
            'Blocks' => $Blocks,
        ];
    }

    /**
     * @Route("/%admin_route%/content/block/new", name="admin_content_block_new")
     * @Route("/%admin_route%/content/block/{id}/edit", requirements={"id" = "\d+"}, name="admin_content_block_edit")
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
            array(
                'builder' => $builder,
                'DeviceType' => $DeviceType,
                'Block' => $Block,
                'html' => $html,
            ),
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
                array(
                    'form' => $form,
                    'Block' => $Block,
                ),
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
     * @Method("DELETE")
     * @Route("/%admin_route%/content/block/{id}/delete", requirements={"id" = "\d+"}, name="admin_content_block_delete")
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
                array(
                    'Block' => $Block,
                ),
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
