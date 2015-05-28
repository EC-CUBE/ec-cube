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

class ProductController
{
    public function index(Application $app, Request $request, $page_no = null)
    {

        $session = $request->getSession();

        $searchForm = $app['form.factory']
            ->createBuilder('admin_search_product')
            ->getForm();

        $pagination = array();

        $default_page_count = $app['config']['default_page_count'];
        $showResult = true;
        $disps = $app['orm.em']->getRepository('Eccube\Entity\Master\Disp')->findAll();

        if ('POST' === $request->getMethod()) {

            $searchForm->handleRequest($request);

            if ($searchForm->isValid()) {
                $searchData = $searchForm->getData();

                $page_count = empty($searchData['page_count']) ? $default_page_count : $searchData['page_count'];

                // paginator
                $qb = $app['eccube.repository.product']->getQueryBuilderBySearchDataForAdmin($searchData);
                $pagination = $app['paginator']()->paginate(
                    $qb,
                    1,
                    $page_count
                );

                // sessionのデータ保持
                $session->set('searchData', $searchData);
            }
        } else {
            if (is_null($page_no)) {
                // sessionを削除
                $session->remove('searchData');
                $showResult = false;
            } else {
                $searchData = $session->get('searchData');
                if (!is_null($searchData)) {

                    $page_count = empty($searchData['page_count']) ? $default_page_count : $searchData['page_count'];

                    $qb = $app['eccube.repository.product']->getQueryBuilderBySearchDataForAdmin($searchData);
                    $pagination = $app['paginator']()->paginate(
                        $qb,
                        $page_no,
                        $page_count
                    );
                }
            }
        }

        return $app['view']->render('Product/index.twig', array(
            'searchForm' => $searchForm->createView(),
            'showResult' => $showResult,
            'pagination' => $pagination,
            'disps' => $disps,
        ));
    }

    public function edit(Application $app, Request $request)
    {
        /* @var $softDeleteFilter \Eccube\Doctrine\Filter\SoftDeleteFilter */
        $softDeleteFilter = $app['orm.em']->getFilters()->getFilter('soft_delete');
        $softDeleteFilter->setExcludes(array(
            'Eccube\Entity\ProductClass'
        ));

        if ($request->get('product_id')) {
            $Product = $app['eccube.repository.product']->find($request->get('product_id'));
            if (!$Product) {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
            }
        } else {
            $Product = new \Eccube\Entity\Product();
            $ProductClass = new \Eccube\Entity\ProductClass();
            $Product
                ->setDelFlg(0)
                ->addProductClass($ProductClass);
            $ProductClass
                ->setDelFlg(0)
                ->setProduct($Product);
        }

        $searchForm = $app['form.factory']
            ->createBuilder('admin_search_product')
            ->getForm();
        if ($request->getMethod() === 'POST') {
            $searchForm->handleRequest($request);
        }

        $builder= $app['form.factory']
            ->createBuilder('admin_product', $Product);
        $hasManyProductClasses = false;
        if (count($Product->getProductClasses()) > 1) {
            $hasManyProductClasses = true;
            $builder->remove('ProductClasses');
        }

        $form = $builder->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $Product = $form->getData();

                // 画像の登録
                $images = array('main_list_image', 'main_image', 'main_large_image');
                foreach ($images as $image) {
                    $file = $form->get($image)->getData();
                    if ($file !== null) {
                        $extension = $file->guessExtension();
                        if (!$extension) {
                            $extension = 'jpg';
                        }
                        $filename = date('mdHi_' . uniqid('')) . '.' . $extension;
                        $file->move($app['config']['image_save_realdir'], $filename);
                        switch ($image) {
                            case 'main_list_image':
                                $Product->setMainListImage($filename);
                                break;
                            case 'main_image':
                                $Product->setMainImage($filename);
                                break;
                            case 'main_large_image':
                                $Product->setMainLargeImage($filename);
                                break;
                        }
                    }
                }

                // ID取得のため、一度登録
                $app['orm.em']->persist($Product);
                $app['orm.em']->flush();

                // ProductClassがひとつの場合、ここでファイル登録を行う
                if (!$hasManyProductClasses) {
                    $ProductClassesForm = $form->get('ProductClasses');

                    $extensions = explode(',', $app['config']['download_extension']);
                    foreach ($ProductClassesForm as $ProductClassForm) {
                        $productTypeId = $ProductClassForm->getData()->getProductType()->getId();
                        $ProductClass = $ProductClassForm->getData();

                        if ($productTypeId == $app['config']['product_type_download']) {
                            $file = $ProductClassForm->get('down_file')->getData();
                            if ($file !== null) {
                                $extension = $file->guessExtension();

                                if (in_array($extension, $extensions)) {
                                    $filename = $file->getClientOriginalName();
                                    $file->move($app['config']['down_save_realdir'], $filename);
                                    $ProductClass->setDownRealFilename($filename);
                                }
                            }
                        } else {
                            $ProductClass
                                ->setDownRealFilename(null)
                                ->setDownFilename(null);
                        }
                        $Product->addProductClass($ProductClass);
                    }
                }
                // カテゴリ登録
                // TODO FormEventで実装？
                $ProductCateogriesOld = $app['orm.em']->getRepository('\Eccube\Entity\ProductCategory')
                    ->findBy(array('product_id' => $Product->getId()));
                foreach ($ProductCateogriesOld as $ProductCateogryOld) {
                    $app['orm.em']->remove($ProductCateogryOld);
                }

                $Categories = $form->get('Category')->getData();

                $rank = 1;
                foreach ($Categories as $Category) {
                    $ProductCategory = new \Eccube\Entity\ProductCategory();
                    $ProductCategory
                        ->setCategoryId($Category->getId())
                        ->setProductId($Product->getId())
                        ->setCategory($Category)
                        ->setProduct($Product)
                        ->setRank($rank);
                    $Product->addProductCategory($ProductCategory);
                    $rank ++;
                }

                $app['orm.em']->persist($Product);
                $app['orm.em']->flush();

                return $app->redirect($app['url_generator']->generate('admin_product'));
            }
        }

        return $app['view']->render('Product/product.twig', array(
            'form' => $form->createView(),
            'searchForm' => $searchForm->createView(),
        ));
    }
}
