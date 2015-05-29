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


namespace Eccube\Controller\Admin\Product;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController
{
    public function index(Application $app, Request $request)
    {
        $searchForm = $app['form.factory']
            ->createBuilder('admin_search_product')
            ->getForm();

        $searchForm->handleRequest($request);
        if ($searchForm->isValid()) {
            $searchData = $searchForm->getData();
        } else {
            $searchData = array();
        }

        // paginator
        $qb = $app['eccube.repository.product']->getQueryBuilderBySearchDataForAdmin($searchData);
        $pagination = $app['paginator']()->paginate(
            $qb,
            !empty($searchData['pageno']) ? $searchData['pageno'] : 1,
            10 // TODO
        );

        return $app['view']->render('Product/index.twig', array(
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
        ));
    }

    public function addImage(Application $app, Request $request)
    {
        $images = $request->files->get('admin_product');
        error_log(json_encode($images));

        $files = array();
        if (count($images) > 0) {
            foreach ($images as $img) {
                foreach ($img as $image) {
                    $extension = $image->guessExtension();
                    $filename = date('mdHisu') . '.' . $extension;
                    $image->move($app['config']['image_temp_realdir'], $filename);
                    $files[] = str_replace(
                            $request->server->get('DOCUMENT_ROOT'),
                            '',
                            $app['config']['image_temp_realdir']
                        ) . $filename;
                }
            }
        }


        return $app->json(array('files' => $files), 200);
    }

    public function edit(Application $app, Request $request, $id = null)
    {
        /* @var $softDeleteFilter \Eccube\Doctrine\Filter\SoftDeleteFilter */
        $softDeleteFilter = $app['orm.em']->getFilters()->getFilter('soft_delete');
        $softDeleteFilter->setExcludes(array(
            'Eccube\Entity\ProductClass'
        ));

        $has_class = false;
        if (is_null($id)) {
            $Product = new \Eccube\Entity\Product();
            $ProductClass = new \Eccube\Entity\ProductClass();
            $Product
                ->setDelFlg(0)
                ->addProductClass($ProductClass);
            $ProductClass
                ->setDelFlg(0)
                ->setProduct($Product);
        } else {
            $Product = $app['eccube.repository.product']->find($id);
            if (!$Product) {
                throw new NotFoundHttpException();
            }
            // 規格あり商品か
            $has_class = $Product->hasProductClass();
            if (!$has_class) {
                $ProductClasses = $Product->getProductClasses();
                $ProductClass = $ProductClasses[0];
            }
        }

        $builder = $app['form.factory']
            ->createBuilder('admin_product', $Product);

        // 規格あり商品の場合、規格関連情報をFormから除外
        if ($has_class) {
            $builder->remove('class');
        }

        $form = $builder->getForm();
        if (!$has_class) {
            $form['class']->setData($ProductClass);
        }

        // ファイルの登録
        $images = array();
        $Images = $Product->getProductImage();
        foreach ($Images as $Image) {
            $images[] = $Image->getFileName();
        }
        $form['images']->setData($images);

        // タグの登録
        $tags = array();
        $Tags = $Product->getProductTag();
        foreach ($Tags as $Tag) {
            $tags[] = $Tag->getTag()->getName();
        }
        $form['tags']->setData($tags);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $Product = $form->getData();

                if (!$has_class) {
                    $ProductClass = $form['class']->getData();
                }

                // 画像の登録
                $files = $form->get('images')->getData();
                foreach ($files as $file) {

                }

                // タグの登録
                $Tags = $Product->getProductTag();
                foreach ($Tags as $Tag) {
                    $Product->removeProductTag($Tag);
                    $app['orm.em']->remove($Tag);
                }

                $tags = $form['tags']->getData();
                foreach ($tags as $tag) {
                    $Tag = $app['eccube.repository.master.tag']->findOrCreateByTagName($tag);
                    $ProductTag = new \Eccube\Entity\ProductTag();
                    $ProductTag
                        ->setProduct($Product)
                        ->setTag($Tag);
                    $Product->addProductTag($ProductTag);

                    $app['orm.em']->persist($Tag);
                    $app['orm.em']->persist($ProductTag);
                }

                $app['orm.em']->persist($Product);
                $app['orm.em']->flush();

                $app->addSuccess('admin.register.complete', 'admin');

                return $app->redirect($app['url_generator']->generate('admin_product'));
            }
            $app->addError('admin.register.failed', 'admin');
        }

        // 検索結果の保持
        $searchForm = $app['form.factory']
            ->createBuilder('admin_search_product')
            ->getForm();
        if ('POST' === $request->getMethod()) {
            $searchForm->handleRequest($request);
        }

        return $app->render('Product/product.twig', array(
            'form' => $form->createView(),
            'searchForm' => $searchForm->createView(),
            'has_class' => $has_class,
            'id' => $id,
        ));
    }
}
