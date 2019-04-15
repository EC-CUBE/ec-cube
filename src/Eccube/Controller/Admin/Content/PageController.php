<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\PageLayout;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Util\Str;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;

class PageController extends AbstractController
{
    public function index(Application $app, Request $request)
    {
        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayouts = $app['eccube.repository.page_layout']->getPageList($DeviceType);

        $event = new EventArgs(
            array(
                'DeviceType' => $DeviceType,
                'PageLayouts' => $PageLayouts,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_CONTENT_PAGE_INDEX_COMPLETE, $event);

        return $app->render('Content/page.twig', array(
            'PageLayouts' => $PageLayouts,
        ));
    }

    public function edit(Application $app, Request $request, $id = null)
    {
        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayout = $app['eccube.repository.page_layout']
            ->findOrCreate($id, $DeviceType);

        $editable = true;

        $builder = $app['form.factory']
            ->createBuilder('main_edit', $PageLayout);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'DeviceType' => $DeviceType,
                'PageLayout' => $PageLayout,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_CONTENT_PAGE_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        // 更新時
        $fileName = null;
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

            $fileName = $PageLayout->getFileName();
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $PageLayout = $form->getData();

            if (!$editable) {
                $PageLayout
                    ->setUrl($PrevPageLayout->getUrl())
                    ->setFileName($PrevPageLayout->getFileName())
                    ->setName($PageLayout->getName());
            }
            // DB登録
            $app['orm.em']->persist($PageLayout);
            $app['orm.em']->flush();

            // ファイル生成・更新
            $templatePath = $app['eccube.repository.page_layout']->getWriteTemplatePath($editable);
            $filePath = $templatePath.'/'.$PageLayout->getFileName().'.twig';

            $fs = new Filesystem();
            $pageData = $form->get('tpl_data')->getData();
            $pageData = Str::convertLineFeed($pageData);
            $fs->dumpFile($filePath, $pageData);

            // 更新でファイル名を変更した場合、以前のファイルを削除
            if ($PageLayout->getFileName() != $fileName && !is_null($fileName)) {
                $oldFilePath = $templatePath.'/'.$fileName.'.twig';
                if ($fs->exists($oldFilePath)) {
                    $fs->remove($oldFilePath);
                }
            }

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'PageLayout' => $PageLayout,
                    'templatePath' => $templatePath,
                    'filePath' => $filePath,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_CONTENT_PAGE_EDIT_COMPLETE, $event);

            $app->addSuccess('admin.register.complete', 'admin');

            // twig キャッシュの削除.
            $finder = Finder::create()->in($app['config']['root_dir'].'/app/cache/twig');
            $fs->remove($finder);

            return $app->redirect($app->url('admin_content_page_edit', array('id' => $PageLayout->getId())));
        }

        $templatePath = $app['eccube.repository.page_layout']->getWriteTemplatePath($editable);

        return $app->render('Content/page_edit.twig', array(
            'form' => $form->createView(),
            'page_id' => $PageLayout->getId(),
            'editable' => $editable,
            'template_path' => $templatePath,
        ));
    }

    public function delete(Application $app, Request $request, $id = null)
    {
        $this->isTokenValid($app);

        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayout = $app['eccube.repository.page_layout']
            ->findOneBy(array(
                'id' => $id,
                'DeviceType' => $DeviceType
            ));

        if (!$PageLayout) {
            $app->deleteMessage();

            return $app->redirect($app->url('admin_content_page'));
        }

        // ユーザーが作ったページのみ削除する
        if ($PageLayout->getEditFlg() == PageLayout::EDIT_FLG_USER) {
            $templatePath = $app['eccube.repository.page_layout']->getWriteTemplatePath(true);
            $file = $templatePath.'/'.$PageLayout->getFileName().'.twig';
            $fs = new Filesystem();
            if ($fs->exists($file)) {
                $fs->remove($file);
            }
            $app['orm.em']->remove($PageLayout);
            $app['orm.em']->flush();

            $event = new EventArgs(
                array(
                    'DeviceType' => $DeviceType,
                    'PageLayout' => $PageLayout,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_CONTENT_PAGE_DELETE_COMPLETE, $event);

            $app->addSuccess('admin.delete.complete', 'admin');
        }

        return $app->redirect($app->url('admin_content_page'));
    }
}
