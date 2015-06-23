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
use Eccube\Entity\Master\DeviceType;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class BlockController
{
    public function index(Application $app)
    {
        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);

        // 登録されているブロック一覧の取得
        $Blocks = $app['eccube.repository.block']->getList($DeviceType);

        return $app->render('Content/block.twig', array(
            'Blocks' => $Blocks,
        ));
    }

    public function edit(Application $app, $id = null)
    {
        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);

        $Block = $app['eccube.repository.block']
            ->findOrCreate($id, $DeviceType);

        $form = $app['form.factory']
            ->createBuilder('block', $Block)
            ->getForm();

        $html = '';
        $previous_filename = null;
        $deletable = $Block->getDeletableFlg();

        if ($id) {
            // テンプレートファイルの取得
            $previous_filename = $Block->getFileName();
            $file = $app['eccube.repository.block']
                ->getReadTemplateFile($previous_filename, $deletable);
            $html = $file['tpl_data'];
        }

        $form->get('block_html')->setData($html);

        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $Block = $form->getData();

                // DB登録
                $app['orm.em']->persist($Block);
                $app['orm.em']->flush();

                // ファイル生成・更新
                $tplDir = $app['eccube.repository.block']
                    ->getWriteTemplatePath($deletable);

                $filePath = $tplDir . '/' . $Block->getFileName() . '.twig';

                $fs = new Filesystem();
                $fs->dumpFile($filePath, $form->get('block_html')->getData());
                // 更新でファイル名を変更した場合、以前のファイルを削除
                if ($Block->getFileName() != $previous_filename && !is_null($previous_filename)) {
                    $oldFilePath = $tplDir . $previous_filename;
                    if ($fs->exists($oldFilePath)) {
                        $fs->remove($oldFilePath);
                    }
                }

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

    public function delete(Application $app, $id)
    {
        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);

        $Block = $app['eccube.repository.block']->findOrCreate($id, $DeviceType);

        // ユーザーが作ったブロックのみ削除する
        if ($Block->getDeletableFlg() > 0) {
            $tplDir = $app['eccube.repository.page_layout']
                ->getTemplatePath($DeviceType);
            $tplDir .= $app['config']['block_dir'];
            $file = $tplDir . $Block->getFileName();
            $fs = new Filesystem();
            if ($fs->exists($file)) {
                $fs->remove($file);
            }
            $app['orm.em']->remove($Block);
            $app['orm.em']->flush();
        }

        return $app->redirect($app->url('admin_content_block'));
    }
}
