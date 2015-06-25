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

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class LayoutController
{
    private $isPreview = false;

    public function index(Application $app, Request $request, $id = 1)
    {
        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(\Eccube\Entity\Master\DeviceType::DEVICE_TYPE_PC);

        // 編集対象ページ
        /* @var $TargetPageLayout \Eccube\Entity\PageLayout */
        $TargetPageLayout = $app['eccube.repository.page_layout']->get($DeviceType, $id);
        $Blocks = $app['orm.em']->getRepository('Eccube\Entity\Block')
            ->findBy(array(
                'DeviceType' => $DeviceType,
            ));
        $BlockPositions = $TargetPageLayout->getBlockPositions();


        $listForm = $app['form.factory']
            ->createBuilder('admin_page_layout')
            ->getForm();
        $listForm->get('layout')->setData($TargetPageLayout);

        // 未使用ブロックの取得
        $unusedBlocks = $app['eccube.repository.page_layout']->findUnusedBlocks($DeviceType, $id);
        foreach ($unusedBlocks as $unusedBlock) {
            $UnusedBlockPosition = new \Eccube\Entity\BlockPosition();
            $UnusedBlockPosition
                ->setPageId($id)
                ->setTargetId(\Eccube\Entity\PageLayout::TARGET_ID_UNUSED)
                ->setAnywhere(0)
                ->setBlockRow(0)
                ->setBlockId($unusedBlock->getId())
                ->setBlock($unusedBlock)
                ->setPageLayout($TargetPageLayout);
            $TargetPageLayout->addBlockPosition($UnusedBlockPosition);
        }

        $form = $app['form.factory']
            ->createBuilder()
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                // 消す
                foreach ($BlockPositions as $BlockPosition) {
                    if ($BlockPosition->getPageId() == $id || $BlockPosition->getAnywhere() == 0) {
                        $TargetPageLayout->removeBlockPosition($BlockPosition);
                        $app['orm.em']->remove($BlockPosition);
                    }
                }
                $app['orm.em']->flush();

                // TODO: collection を利用

                $data = $request->request->all();
                for ($i = 0; $i < count($Blocks); $i++) {
                    // block_id が取得できない場合は INSERT しない
                    if (!isset($data['id_' . $i])) {
                        continue;
                    }
                    // 未使用は INSERT しない
                    if ($data['target_id_' . $i] == \Eccube\Entity\PageLayout::TARGET_ID_UNUSED) {
                        continue;
                    }
                    // 他のページに anywhere が存在する場合は INSERT しない
                    $anywhere = (isset($data['anywhere_' . $i]) && $data['anywhere_' . $i] == 1) ? 1 : 0;
                    if (isset($data['anywhere_' . $i]) && $data['anywhere_' . $i] == 1) {
                        $Other = $app['orm.em']->getRepository('Eccube\Entity\BlockPosition')
                            ->findBy(array(
                                'anywhere' => 1,
                                'block_id' => $data['id_' . $i],
                            ));
                        if (count($Other) > 0) {
                            continue;
                        }
                    }

                    $BlockPosition = new \Eccube\Entity\BlockPosition();
                    $Block = $app['orm.em']->getRepository('Eccube\Entity\Block')
                        ->findOneBy(array(
                            'id' => $data['id_' . $i],
                            'DeviceType' => $DeviceType,
                        ));
                    $BlockPosition
                        ->setPageId($id)
                        ->setBlockId($data['id_' . $i])
                        ->setBlockRow($data['top_' . $i])
                        ->setTargetId($data['target_id_' . $i])
                        ->setBlock($Block)
                        ->setPageLayout($TargetPageLayout)
                        ->setAnywhere($anywhere);
                    if ($id == 0) {
                        $BlockPosition->setAnywhere(0);
                    }
                    $TargetPageLayout->addBlockPosition($BlockPosition);
                    $app['orm.em']->persist($BlockPosition);
                }

                $app['orm.em']->persist($TargetPageLayout);
                $app['orm.em']->flush();

                if ($this->isPreview) {
                    $app->addSuccess('admin.preview.register.complete', 'admin');
                } else {
                    $app->addSuccess('admin.register.complete', 'admin');
                }

                return $app->redirect($app->url('admin_content_layout_edit', array('id' => $id)));
            }

        }

        return $app->render('Content/layout.twig', array(
            'form' => $form->createView(),
            'list_form' => $listForm->createView(),
            'TargetPageLayout' => $TargetPageLayout,
        ));
    }

    public function preview(Application $app, Request $request, $id)
    {
        $this->isPreview = true;
        return $this->index($app, $request, 0);
    }

}
