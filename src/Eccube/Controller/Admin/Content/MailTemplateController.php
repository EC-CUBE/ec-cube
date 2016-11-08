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
use Eccube\Controller\AbstractController;
use Eccube\Util\Cache;
use Eccube\Util\Str;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;

class MailTemplateController extends AbstractController
{

    public function index(Application $app, Request $request)
    {
        // Mailディレクトリ(app/template、Resource/template)からメールファイルを取得
        $finder = Finder::create()->depth(0);
        $mailDir = $app['config']['template_default_realdir'].'/Mail';

        $files = array();
        foreach ($finder->in($mailDir) as $file) {
            $files[$file->getFilename()] = $file->getFilename();
        }

        $mailDir = $app['config']['template_realdir'].'/Mail';
        foreach ($finder->in($mailDir) as $file) {
            $files[$file->getFilename()] = $file->getFilename();
        }

        return $app->render('Content/mail.twig', array(
            'files' => $files,
        ));

    }

    public function edit(Application $app, Request $request, $name)
    {

        $readPaths = array(
            $app['config']['template_realdir'],
            $app['config']['template_default_realdir'],
        );

        $fs = new Filesystem();
        $tplData = null;
        foreach ($readPaths as $readPath) {
            $filePath = $readPath.'/Mail/'.$name;
            if ($fs->exists($filePath)) {
                $tplData = file_get_contents($filePath);
                break;
            }
        }

        if (!$tplData) {
            log_info("対象ファイルが存在しません");
            $app->addError('admin.content.mail.edit.error', 'admin');

            return $app->redirect($app->url('admin_content_mail'));
        }

        $builder = $app['form.factory']->createBuilder('admin_mail_template');

        $form = $builder->getForm();

        $form->get('tpl_data')->setData($tplData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ファイル生成・更新
            $filePath = $app['config']['template_realdir'].'/Mail/'.$name;

            $fs = new Filesystem();
            $pageData = $form->get('tpl_data')->getData();
            $pageData = Str::convertLineFeed($pageData);
            $fs->dumpFile($filePath, $pageData);

            $app->addSuccess('admin.register.complete', 'admin');

            // twig キャッシュの削除.
            Cache::clear($app, false, true);

            return $app->redirect($app->url('admin_content_mail_edit', array(
                'name' => $name,
            )));
        }

        return $app->render('Content/mail_edit.twig', array(
            'name' => $name,
            'form' => $form->createView(),
        ));
    }

    public function reedit(Application $app, Request $request, $name)
    {

        $this->isTokenValid($app);

        $readPaths = array(
            $app['config']['template_default_realdir'],
        );

        $fs = new Filesystem();
        $tplData = null;
        foreach ($readPaths as $readPath) {
            $filePath = $readPath.'/Mail/'.$name;
            if ($fs->exists($filePath)) {
                $tplData = file_get_contents($filePath);
                break;
            }
        }

        if (!$tplData) {
            log_info("対象ファイルが存在しません");
            $app->addError('admin.content.mail.edit.error', 'admin');

            return $app->redirect($app->url('admin_content_mail'));
        }

        $builder = $app['form.factory']->createBuilder('admin_mail_template');

        $form = $builder->getForm();

        $form->get('tpl_data')->setData($tplData);

        $app->addSuccess('admin.content.mail.init.complete', 'admin');

        return $app->render('Content/mail_edit.twig', array(
            'name' => $name,
            'form' => $form->createView(),
        ));
    }

}
