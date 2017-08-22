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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Component;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\DeviceType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\BlockType;
use Eccube\Repository\BlockRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Util\Str;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Component
 * @Route(service=BlockController::class)
 */
class BlockController extends AbstractController
{
    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject(BlockRepository::class)
     * @var BlockRepository
     */
    protected $blockRepository;

    /**
     * @Inject(DeviceTypeRepository::class)
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * @Route("/{_admin}/content/block", name="admin_content_block")
     * @Template("Content/block.twig")
     */
    public function index(Application $app, Request $request)
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
     * @Route("/{_admin}/content/block/new", name="admin_content_block_new")
     * @Route("/{_admin}/content/block/{id}/edit", requirements={"id" = "\d+"}, name="admin_content_block_edit")
     * @Template("Content/block_edit.twig")
     */
    public function edit(Application $app, Request $request, $id = null)
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
        $previous_filename = null;
        $deletable = $Block->getDeletableFlg();

        if ($id) {
            // テンプレートファイルの取得
            $previous_filename = $Block->getFileName();
            $file = $this->blockRepository
                ->getReadTemplateFile($previous_filename, $deletable);
            $html = $file['tpl_data'];
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

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) { // FIXME isSubmitted() && isValid() に修正する
                $Block = $form->getData();

                // DB登録
                $this->entityManager->persist($Block);
                $this->entityManager->flush();

                // ファイル生成・更新
                $tplDir = $this->appConfig['block_realdir'];

                $filePath = $tplDir . '/' . $Block->getFileName() . '.twig';

                $fs = new Filesystem();
                $blockData = $form->get('block_html')->getData();
                $blockData = Str::convertLineFeed($blockData);
                $fs->dumpFile($filePath, $blockData);
                // 更新でファイル名を変更した場合、以前のファイルを削除
                if ($Block->getFileName() != $previous_filename && !is_null($previous_filename)) {
                    $oldFilePath = $tplDir . '/' . $previous_filename . '.twig';
                    if ($fs->exists($oldFilePath)) {
                        $fs->remove($oldFilePath);
                    }
                }

                //twigテンプレートのみ削除
                \Eccube\Util\Cache::clear($app, false, true);

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'Block' => $Block,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_BLOCK_EDIT_COMPLETE, $event);

                $app->addSuccess('admin.register.complete', 'admin');

                return $app->redirect($app->url('admin_content_block_edit', array('id' => $Block->getId())));
            }
        }


        return $app->render('Content/block_edit.twig', array(
            'form' => $form->createView(),
            'block_id' => $id,
            'deletable' => $deletable,
        ));
    }

    /**
     * @Method("DELETE")
     * @Route("/{_admin}/content/news/{id}/delete", requirements={"id" = "\d+"}, name="admin_content_block_delete")
     */
    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $DeviceType = $this->deviceTypeRepository
            ->find(DeviceType::DEVICE_TYPE_PC);

        $Block = $this->blockRepository->findOneBy(array(
            'id' => $id,
            'DeviceType' => $DeviceType
        ));

        if (!$Block) {
            $app->deleteMessage();
            return $app->redirect($app->url('admin_content_block'));
        }

        // ユーザーが作ったブロックのみ削除する
        // テンプレートが変更されていた場合、DBからはブロック削除されるがtwigファイルは残る
        if ($Block->getDeletableFlg() > 0) {
            $tplDir = $this->appConfig['block_realdir'];
            $file = $tplDir . '/' . $Block->getFileName() . '.twig';
            $fs = new Filesystem();
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

            $app->addSuccess('admin.delete.complete', 'admin');
            //twigテンプレートのみ削除
            \Eccube\Util\Cache::clear($app, false, true);
        }


        return $app->redirect($app->url('admin_content_block'));
    }
}
