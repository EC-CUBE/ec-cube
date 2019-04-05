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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;

class CacheController extends AbstractController
{

    public function index(Application $app, Request $request)
    {

        $builder = $app['form.factory']->createBuilder('admin_cache');

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->get('cache')->getData();

            $cacheDir = $app['config']['root_dir'].'/app/cache';

            $filesystem = new Filesystem();

            foreach ($data as $dir) {
                if (is_dir($cacheDir.'/'.$dir)) {
                    // 指定されたキャッシュディレクトリを削除
                    $finder = Finder::create()->in($cacheDir.'/'.$dir);
                    $filesystem->remove($finder);
                }
                if ($dir == 'doctrine') {
                    // doctrineが指定された場合は, cache driver経由で削除.
                    $config =  $app['orm.em']->getConfiguration();
                    $this->deleteDoctrineCache($config->getMetadataCacheImpl());
                    $this->deleteDoctrineCache($config->getQueryCacheImpl());
                    $this->deleteDoctrineCache($config->getResultCacheImpl());
                    $this->deleteDoctrineCache($config->getHydrationCacheImpl());
                }
            }

            $app->addSuccess('admin.content.cache.save.complete', 'admin');
        }

        return $app->render('Content/cache.twig', array(
            'form' => $form->createView(),
        ));
    }

    protected function deleteDoctrineCache(\Doctrine\Common\Cache\Cache $cacheDriver)
    {
        $cacheDriver->deleteAll();
        $cacheDriver->flushAll();
    }
}
