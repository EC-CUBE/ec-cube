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
use Eccube\Form\Type\Admin\CacheType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Component
 * @Route(service=CacheController::class)
 */
class CacheController extends AbstractController
{
    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Route("/{_admin}/content/cache", name="admin_content_cache")
     * @Template("Content/cache.twig")
     */
    public function index(Application $app, Request $request)
    {

        $builder = $this->formFactory->createBuilder(CacheType::class);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->get('cache')->getData();

            $cacheDir = $this->appConfig['root_dir'].'/app/cache';

            $filesystem = new Filesystem();

            foreach ($data as $dir) {
                if (is_dir($cacheDir.'/'.$dir)) {
                    // 指定されたキャッシュディレクトリを削除
                    $finder = Finder::create()->in($cacheDir.'/'.$dir);
                    $filesystem->remove($finder);
                }
                if ($dir == 'doctrine') {
                    // doctrineが指定された場合は, cache driver経由で削除.
                    $config =  $this->entityManager->getConfiguration();
                    $this->deleteDoctrineCache($config->getMetadataCacheImpl());
                    $this->deleteDoctrineCache($config->getQueryCacheImpl());
                    $this->deleteDoctrineCache($config->getResultCacheImpl());
                    $this->deleteDoctrineCache($config->getHydrationCacheImpl());
                }
            }

            $app->addSuccess('admin.content.cache.save.complete', 'admin');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    protected function deleteDoctrineCache(\Doctrine\Common\Cache\Cache $cacheDriver)
    {
        $cacheDriver->deleteAll();
        $cacheDriver->flushAll();
    }
}
