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
use Eccube\Entity\PageLayout;
use Symfony\Component\Filesystem\Filesystem;

class PageController
{
    public function index(Application $app)
    {
        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayouts = $app['eccube.repository.page_layout']->getPageList($DeviceType);

        return $app->render('Content/page.twig', array(
            'PageLayouts' => $PageLayouts,
        ));
    }

    public function edit(Application $app, $id = null)
    {
        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayout = $app['eccube.repository.page_layout']
            ->findOrCreate($id, $DeviceType);

        $editable = true;
        $form = $app['form.factory']
            ->createBuilder('main_edit', $PageLayout)
            ->getForm();

        // 更新時
        if ($id) {
            // 編集不可ページはURL、ページ名、ファイル名を保持
            if ($PageLayout->getEditFlg() == PageLayout::EDIT_FLG_DEFAULT) {
                $editable = false;
                $PrevPageLayout = clone $PageLayout;
            }
            // テンプレートファイルの取得
            $file = $app['eccube.repository.page_layout']
                ->getReadTemplateFile($PageLayout->getFileName(), $editable);

            $form->get('tpl_data')->setData($file['tpl_data']);
        }

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $PageLayout = $form->getData();

                $url = strtolower($form->get('file_name')->getData());
                $url = str_replace('/', '_', $url);
                $PageLayout->setUrl($url);

                if (!$editable) {
                    $PageLayout
                        ->setUrl($PrevPageLayout->getUrl())
                        ->setFileName($PrevPageLayout->getFileName())
                        ->setName($PrevPageLayout->getName());
                }
                // DB登録
                $app['orm.em']->persist($PageLayout);
                $app['orm.em']->flush();

                // ファイル生成・更新
                $templatePath = $app['eccube.repository.page_layout']->getWriteTemplatePath($editable);
                $filePath = $templatePath . '/' . $PageLayout->getFileName() . '.twig';

                $fs = new Filesystem();
                $fs->dumpFile($filePath, $form->get('tpl_data')->getData());

                $app->addSuccess('admin.register.complete', 'admin');

                return $app->redirect($app->url('admin_content_page_edit', array('id' => $PageLayout->getId())));
            }
        }

        return $app->render('Content/page_edit.twig', array(
            'form' => $form->createView(),
            'page_id' => $PageLayout->getId(),
            'editable' => $editable,
        ));
    }

    public function delete(Application $app, $id = null)
    {
        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayout = $app['eccube.repository.page_layout']
            ->findOneBy(array(
                'id' => $id,
                'DeviceType' => $DeviceType
            ));

        // ユーザーが作ったページのみ削除する
        if ($PageLayout->getEditFlg() == PageLayout::EDIT_FLG_USER) {
            $templatePath = $app['eccube.repository.page_layout']
                ->getWriteTemplatePath($DeviceType, true);
            $file = $templatePath . '/' . $PageLayout->getFileName() . '.twig';
            $fs = new Filesystem();
            if ($fs->exists($file)) {
                $fs->remove($file);
            }
            $app['orm.em']->remove($PageLayout);
            $app['orm.em']->flush();

            $app->addSuccess('admin.delete.complete', 'admin');
        }

        return $app->redirect($app->url('admin_content_page'));
    }
}
