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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController
{
    public function index(Application $app, Request $request, $page_no = null)
    {

        $session = $request->getSession();

        $searchForm = $app['form.factory']
            ->createBuilder('admin_search_product')
            ->getForm();

        $pagination = array();

        $em = $app['orm.em'];
        $disps = $em->getRepository('Eccube\Entity\Master\Disp')->findAll();
        $pageMaxis = $em->getRepository('Eccube\Entity\Master\PageMax')->findAll();
        $page_count = $app['config']['default_page_count'];
        $page_status = null;

        if ('POST' === $request->getMethod()) {

            $searchForm->handleRequest($request);

            if ($searchForm->isValid()) {
                $searchData = $searchForm->getData();

                // paginator
                $qb = $app['eccube.repository.product']->getQueryBuilderBySearchDataForAdmin($searchData);
                $page_no = 1;
                $pagination = $app['paginator']()->paginate(
                    $qb,
                    $page_no,
                    $page_count
                );

                // sessionのデータ保持
                $session->set('eccube.admin.product.search', $searchData);
            }
        } else {
            if (is_null($page_no)) {
                // sessionを削除
                $session->remove('eccube.admin.product.search');
            } else {
                // pagingなどの処理
                $searchData = $session->get('eccube.admin.product.search');
                if (!is_null($searchData)) {

                    // 公開ステータス
                    $status = $request->get('status');
                    if (!empty($status)) {
                        if ($status != $app['config']['admin_product_stock_status']) {
                            $searchData['status']->clear();
                            $searchData['status']->add($status);
                            $session->set('eccube.admin.product.search', $searchData);
                        } else {
                            $searchData['stock_status'] = $app['config']['disabled'];
                        }
                        $page_status = $status;
                    }
                    // 表示件数
                    $pcount = $request->get('page_count');

                    $page_count = empty($pcount) ? $page_count : $pcount;

                    $qb = $app['eccube.repository.product']->getQueryBuilderBySearchDataForAdmin($searchData);
                    $pagination = $app['paginator']()->paginate(
                        $qb,
                        $page_no,
                        $page_count
                    );

                }
            }
        }

        return $app->render('Product/index.twig', array(
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'disps' => $disps,
            'pageMaxis' => $pageMaxis,
            'page_no' => $page_no,
            'page_status' => $page_status,
            'page_count' => $page_count,
        ));
    }

    public function addImage(Application $app, Request $request)
    {
        $images = $request->files->get('admin_product');

        $files = array();
        if (count($images) > 0) {
            foreach ($images as $img) {
                foreach ($img as $image) {
                    $extension = $image->guessExtension();
                    $filename = date('mdHis') . uniqid('_') . '.' . $extension;
                    $image->move($app['config']['image_temp_realdir'], $filename);
                    $files[] = $filename;
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
        $ProductImages = $Product->getProductImage();
        foreach ($ProductImages as $ProductImage) {
            $images[] = $ProductImage->getFileName();
        }
        $form['images']->setData($images);

        $categories = array();
        $ProductCategories = $Product->getProductCategories();
        foreach ($ProductCategories as $ProductCategory) {
            /* @var $ProductCategory \Eccube\Entity\ProductCategory */
            $categories[] = $ProductCategory->getCategory();
        }
        $form['Category']->setData($categories);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $Product = $form->getData();

                if (!$has_class) {
                    $ProductClass = $form['class']->getData();
                    $app['orm.em']->persist($ProductClass);
                }

                // カテゴリの登録
                // 一度クリア
                /* @var $Product \Eccube\Entity\Product */
                foreach ($Product->getProductCategories() as $ProductCategory) {
                    $Product->removeProductCategory($ProductCategory);
                    $app['orm.em']->remove($ProductCategory);
                }
                $app['orm.em']->persist($Product);
                $app['orm.em']->flush();

                $count = 1;
                $Categories = $form->get('Category')->getData();
                foreach ($Categories as $Category) {
                    $ProductCategory = new \Eccube\Entity\ProductCategory();
                    $ProductCategory
                        ->setProduct($Product)
                        ->setProductId($Product->getId())
                        ->setCategory($Category)
                        ->setCategoryId($Category->getId())
                        ->setRank($count)
                    ;
                    $app['orm.em']->persist($ProductCategory);
                    $count ++;
                    /* @var $Product \Eccube\Entity\Product */
                    $Product->addProductCategory($ProductCategory);
                }

                // 画像の登録
                $add_images = $form->get('add_images')->getData();
                foreach ($add_images as $add_image) {
                    $ProductImage = new \Eccube\Entity\ProductImage();
                    $ProductImage
                        ->setFileName($add_image)
                        ->setProduct($Product)
                        ->setRank(1);
                    $Product->addProductImage($ProductImage);
                    $app['orm.em']->persist($ProductImage);

                    // 移動
                    $file = new File($app['config']['image_temp_realdir'] . $add_image);
                    $file->move($app['config']['image_save_realdir']);
                }

                // 画像の削除
                $delete_images = $form->get('delete_images')->getData();
                foreach ($delete_images as $delete_image) {
                    $ProductImage = $app['eccube.repository.product_image']
                        ->findOneBy(array('file_name' => $delete_image));
                    // 追加してすぐに削除した画像は、Entityに追加されない
                    if ($ProductImage instanceof \Eccube\Entity\ProductImage) {
                        $Product->removeProductImage($ProductImage);
                        $app['orm.em']->remove($ProductImage);
                    }
                    $app['orm.em']->persist($Product);

                    // 削除
                    $fs = new Filesystem();
                    $fs->remove($app['config']['image_save_realdir'] . $delete_image);
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
