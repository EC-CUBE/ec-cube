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
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class BlockController
{
    public function index(Application $app, $id = null)
    {
        // TODO: 消したい
        $device_type_id = 10;

        // TODO: block_idをUniqにしてblock_idだけの検索にしたい。
        $Block = $app['eccube.repository.block']
            ->findOrCreate($id, $device_type_id);

        $builder = $app['form.factory']->createBuilder('block');
        $bloc_html = '';
        $previous_filename = null;
        if ($id) {
            // テンプレートファイルの取得
            $previous_filename = $Block->getTplPath();
            $file = $this->getTplFile($app, $previous_filename, $device_type_id);
            $bloc_html = $file['tpl_data'];
        }

        $form = $builder->getForm();
        $form->setData($Block);
        $form->get('bloc_html')->setData($bloc_html);

        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $Block = $form->getData();
                $Block->setTplPath($form->get('filename')->getData() . '.twig');

                // DB登録
                $app['orm.em']->persist($Block);
                $app['orm.em']->flush();
                // ファイル生成・更新
                $tplDir = $app['eccube.repository.page_layout']
                    ->getTemplatePath($device_type_id);
                $tplDir .= $app['config']['bloc_dir'];
                $filePath = $tplDir . $Block->getTplPath();

                $fs = new Filesystem();
                $fs->dumpFile($filePath, $form->get('bloc_html')->getData());
                // 更新でファイル名を変更した場合、以前のファイルを削除
                if ($Block->getTplPath() != $previous_filename && !is_null($previous_filename)) {
                    $oldFilePath = $tplDir . $previous_filename;
                    if ($fs->exists($oldFilePath)) {
                        $fs->remove($oldFilePath);
                    }
                }

                $app['session']->getFlashBag()->add('block.complete', 'admin.register.complete');

                return $app->redirect($app['url_generator']->generate('admin_content_block'));
            }
        }

        // 登録されているページ一覧の取得
        $Blocks = $app['eccube.repository.block']->getList($device_type_id);

        return $app['view']->render('Admin/Content/block.twig', array(
            'Blocks' => $Blocks,
            'block_id' => $id,
            'form' => $form->createView(),
        ));
    }

    public function delete(Application $app, $id)
    {
        // TODO: 消したい
        $device_type_id = 10;

        $Block = $app['eccube.repository.block']->findOrCreate($id, $device_type_id);

        // ユーザーが作ったブロックのみ削除する
        if ($Block->getDeletableFlg() > 0) {
            $tplDir = $app['eccube.repository.page_layout']
                ->getTemplatePath($device_type_id);
            $tplDir .= $app['config']['bloc_dir'];
            $file = $tplDir . $Block->getTplPath();
            $fs = new Filesystem();
            if ($fs->exists($file)) {
                $fs->remove($file);
            }
            $app['orm.em']->remove($Block);
            $app['orm.em']->flush();
        }

        return $app->redirect($app['url_generator']->generate('admin_content_block'));
    }

    private function getTplFile(Application $app, $tpl_path, $device_type_id)
    {
        $tplDir = $app['eccube.repository.page_layout']
            ->getTemplatePath($device_type_id);
        $tplDir .= $app['config']['bloc_dir'];

        $finder = Finder::create();
        $finder->followLinks();

        $finder->in($tplDir)->name($tpl_path);

        $data = null;
        if ($finder->count() === 1) {
            foreach ($finder as $file) {
                $data = array(
                    'file_name' => $file->getFileName(),
                    'tpl_data' => file_get_contents($file->getPathName())
                );
            }
        }

        return $data;

    }
}
