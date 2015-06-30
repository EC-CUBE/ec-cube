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
use Eccube\Common\Constant;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController
{
    public function index(Application $app, Request $request, $page_no = null)
    {

        $session = $app['session'];

        $searchForm = $app['form.factory']
            ->createBuilder('admin_search_product')
            ->getForm();

        $pagination = array();

        $disps = $app['eccube.repository.master.disp']->findAll();
        $pageMaxis = $app['eccube.repository.master.page_max']->findAll();
        $page_count = $app['config']['default_page_count'];
        $page_status = null;
        $active = false;

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
                $active = true;
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
                            $searchData['link_status'] = $app['eccube.repository.master.disp']->find($status);
                            $searchData['status'] = null;
                            $session->set('eccube.admin.product.search', $searchData);
                        } else {
                            $searchData['stock_status'] = Constant::DISABLED;
                        }
                        $page_status = $status;
                    } else {
                        $searchData['link_status'] = null;
                        $searchData['stock_status'] = null;
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

                    // セッションから検索条件を復元
                    if (!empty($searchData['category_id'])) {
                        $searchData['category_id'] = $app['eccube.repository.category']->find($searchData['category_id']);
                    }
                    if (empty($status)) {
                        if (count($searchData['status']) > 0) {
                            $status_ids = array();
                            foreach ($searchData['status'] as $Status) {
                                $status_ids[] = $Status->getId();
                            }
                            $searchData['status'] = $app['eccube.repository.master.disp']->findBy(array('id' => $status_ids));
                        }
                        $searchData['link_status'] = null;
                        $searchData['stock_status'] = null;
                    }
                    /*
                    if (count($searchData['product_status']) > 0) {
                        $product_status_ids = array();
                        foreach ($searchData['product_status'] as $ProductStatus) {
                            $product_status_ids[] = $ProductStatus->getId();
                        }
                        $searchData['product_status'] = $app['eccube.repository.master.product_status']->findBy(array('id' => $product_status_ids));
                    }
                    */
                    $searchForm->setData($searchData);
                    $active = true;
                }
            }
        }

        return $app->renderView('Product/index.twig', array(
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'disps' => $disps,
            'pageMaxis' => $pageMaxis,
            'page_no' => $page_no,
            'page_status' => $page_status,
            'page_count' => $page_count,
            'active' => $active,
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
            $Disp = $app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_HIDE);
            $Product
                ->setDelFlg(0)
                ->addProductClass($ProductClass)
            ->setStatus($Disp);
            $ProductClass
                ->setDelFlg(0)
                ->setStockUnlimited(true)
                ->setProduct($Product);
            $ProductStock = new \Eccube\Entity\ProductStock();
            $ProductClass->setProductStock($ProductStock);
            $ProductStock->setProductClass($ProductClass);
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
                $ProductStock = $ProductClasses[0]->getProductStock();
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

                    // 在庫情報を作成
                    if (!$ProductClass->getStockUnlimited()) {
                        $ProductStock->setStock($ProductClass->getStock());
                    } else {
                        // 在庫無制限時はnullを設定
                        $ProductStock->setStock(null);
                    }
                    $app['orm.em']->persist($ProductStock);
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
                        ->setRank($count);
                    $app['orm.em']->persist($ProductCategory);
                    $count++;
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
                    $file = new File($app['config']['image_temp_realdir'] . '/' . $add_image);
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
                    $fs->remove($app['config']['image_save_realdir'] . '/' . $delete_image);
                }
                $app['orm.em']->persist($Product);
                $app['orm.em']->flush();


                $ranks = $request->get('rank_images');
                if ($ranks) {
                    foreach ($ranks as $rank) {
                        list($filename, $rank_val) = explode('//', $rank);
                        $ProductImage = $app['eccube.repository.product_image']
                            ->findOneBy(array(
                                'file_name' => $filename,
                                'Product' => $Product,
                            ));
                        $ProductImage->setRank($rank_val);
                        $app['orm.em']->persist($ProductImage);
                    }
                }
                $app['orm.em']->flush();

                $app->addSuccess('admin.register.complete', 'admin');

                return $app->redirect($app->url('admin_product_product_edit', array('id' => $Product->getId())));
            } else {
                $app->addError('admin.register.failed', 'admin');
            }
        }

        // 検索結果の保持
        $searchForm = $app['form.factory']
            ->createBuilder('admin_search_product')
            ->getForm();
        if ('POST' === $request->getMethod()) {
            $searchForm->handleRequest($request);
        }

        return $app->render('Product/product.twig', array(
            'Product' => $Product,
            'form' => $form->createView(),
            'searchForm' => $searchForm->createView(),
            'has_class' => $has_class,
            'id' => $id,
        ));
    }

    public function delete(Application $app, Request $request, $id = null)
    {
        if (!is_null($id)) {
            /* @var $Product \Eccube\Entity\Product */
            $Product = $app['eccube.repository.product']->find($id);
            if ($Product instanceof \Eccube\Entity\Product) {
                $Product->setDelFlg(1);
                $app['orm.em']->persist($Product);
                $app['orm.em']->flush();

                $app->addSuccess('admin.delete.complete', 'admin');
            } else {
                $app->addError('admin.delete.failed', 'admin');
            }
        } else {
            $app->addError('admin.delete.failed', 'admin');
        }

        return $app->redirect($app->url('admin_product'));
    }

    public function copy(Application $app, Request $request, $id = null)
    {
        if (!is_null($id)) {
            $Product = $app['eccube.repository.product']->find($id);
            if ($Product instanceof \Eccube\Entity\Product) {
                $CopyProduct = clone $Product;
                $CopyProduct->copy();
                $Disp = $app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_HIDE);
                $CopyProduct->setStatus($Disp);

                $CopyProductCategories = $CopyProduct->getProductCategories();
                foreach ($CopyProductCategories as $Category) {
                    $app['orm.em']->persist($Category);
                }
                $CopyProductClasses = $CopyProduct->getProductClasses();
                foreach ($CopyProductClasses as $Class) {
                    $Stock = $Class->getProductStock();
                    $CopyStock = clone $Stock;
                    $CopyStock->setProductClass($Class);
                    $app['orm.em']->persist($CopyStock);

                    $app['orm.em']->persist($Class);
                }
                $Images = $CopyProduct->getProductImage();
                foreach ($Images as $Image) {
                    $app['orm.em']->persist($Image);
                }
                $Tags = $CopyProduct->getProductTag();
                foreach ($Tags as $Tag) {
                    $app['orm.em']->persist($Tag);
                }

                $app['orm.em']->persist($CopyProduct);
                $app['orm.em']->flush();

                $app->addSuccess('admin.product.copy.complete', 'admin');

                return $app->redirect($app->url('admin_product_product_edit', array('id' => $CopyProduct->getId())));
            } else {
                $app->addError('admin.product.copy.failed', 'admin');
            }
        } else {
            $app->addError('admin.product.copy.failed', 'admin');
        }

        return $app->redirect($app->url('admin_product'));
    }

    public function display(Application $app, Request $request, $id = null)
    {
        if (!is_null($id)) {
            return $app->redirect($app->url('product_detail', array('id' => $id, 'admin' => '1')));
        }

        return $app->redirect($app->url('admin_product'));
    }
}
