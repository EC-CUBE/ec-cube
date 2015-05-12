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
use \Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class CssController
{
    // TODO: 定数化
    protected $cssDir = 'default/css/';

    public function index(Application $app)
    {
        $builder = $app['form.factory']->createBuilder();
        $builder->add('file_name', 'text');
        $builder->add('content', 'textarea');
        $builder->add('save', 'submit', array('label' => '登録'));
        $form = $builder->getForm();

        // 登録実行(新規/編集)
        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                $fs = new Filesystem();
                $fs->dumpFile($this->getCssDir($app) . $data['file_name'], $data['content']);
                $app['session']->getFlashBag()->add('admin.content.css.complete', 'admin.register.complete');

                return $app->redirect($app['url_generator']->generate('admin_content_css'));
            }
        }

        // 編集初期表示
        $target = $app['request']->get('target');
        if ($target) {
            $finder = Finder::create();
            $finder->in($this->getCssDir($app))->name($target);
            if ($finder->count() === 1) {
                $data = null;
                foreach ($finder as $file) {
                    $data = array(
                        'file_name' => $file->getFileName(),
                        'content' => file_get_contents($file->getPathName()));
                }
                $form->setData($data);
            }
        }

        return $app['view']->render('Content/css.twig', array(
            'form' => $form->createView(),
            'filenames' => $this->getFileNames($app),
        ));
    }

    public function delete(Application $app)
    {
        $target = $app['request']->get('target');

        $finder = Finder::create();
        $finder->in($this->getCssDir($app))->name($target);

        if ($finder->count() == 1) {
            foreach ($finder->files() as $file) {
                $fs = new Filesystem();
                $fs->remove($file->getPathName());
            }
            $app['session']->getFlashBag()->add('admin.content.css.complete', 'admin.register.complete');
        }

        return $app->redirect($app['url_generator']->generate('admin_content_css'));
    }

    protected function getFileNames($app)
    {
        $finder = Finder::create();
        $finder
            ->in($this->getCssDir($app))
            ->files()
            ->sortByName()
            ->depth(0);

        $fileNames = array();
        foreach ($finder as $file) {
            $fileNames[] = $file->getFileName();
        }

        return $fileNames;
    }

    protected function getCssDir($app)
    {
        return $app['config']['user_template_realdir'] . $this->cssDir;
    }
}
