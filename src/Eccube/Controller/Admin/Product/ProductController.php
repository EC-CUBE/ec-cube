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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CsvType;
use Eccube\Entity\ProductTag;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\ProductType;
use Eccube\Form\Type\Admin\SearchProductType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\Master\DispRepository;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Repository\ProductImageRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Service\CsvExportService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

/**
 * @Component
 * @Route(service=ProductController::class)
 */
class ProductController extends AbstractController
{
    /**
     * @Inject(CsvExportService::class)
     * @var CsvExportService
     */
    protected $csvExportService;

    /**
     * @Inject(ProductClassRepository::class)
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * @Inject(ProductImageRepository::class)
     * @var ProductImageRepository
     */
    protected $productImageRepository;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject(TaxRuleRepository::class)
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /**
     * @Inject(BaseInfoRepository::class)
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    /**
     * @Inject(CategoryRepository::class)
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @Inject(ProductRepository::class)
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject(PageMaxRepository::class)
     * @var PageMaxRepository
     */
    protected $pageMaxRepository;

    /**
     * @Inject(DispRepository::class)
     * @var DispRepository
     */
    protected $dispRepository;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject("session")
     * @var Session
     */
    protected $session;

    /**
     * @Route("/{_admin}/product", name="admin_product")
     * @Route("/{_admin}/product/page/{page_no}", requirements={"page_no" = "\d+"}, name="admin_product_page")
     * @Template("Product/index.twig")
     */
    public function index(Application $app, Request $request, $page_no = null)
    {

        $session = $this->session;

        $builder = $this->formFactory
            ->createBuilder(SearchProductType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_INDEX_INITIALIZE, $event);

        $searchForm = $builder->getForm();

        $pagination = array();

        $disps = $this->dispRepository->findAll();
        $pageMaxis = $this->pageMaxRepository->findAll();
        $page_count = $this->appConfig['default_page_count'];
        $page_status = null;
        $active = false;

        if ('POST' === $request->getMethod()) {

            $searchForm->handleRequest($request);

            if ($searchForm->isValid()) {
                $searchData = $searchForm->getData();

                // paginator
                $qb = $this->productRepository->getQueryBuilderBySearchDataForAdmin($searchData);
                $page_no = 1;

                $event = new EventArgs(
                    array(
                        'qb' => $qb,
                        'searchData' => $searchData,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_INDEX_SEARCH, $event);
                $searchData = $event->getArgument('searchData');

                $pagination = $app['paginator']()->paginate(
                    $qb,
                    $page_no,
                    $page_count,
                    array('wrap-queries' => true)
                );

                // sessionのデータ保持
                $session->set('eccube.admin.product.search', $searchData);
                $session->set('eccube.admin.product.search.page_no', $page_no);
            }
        } else {
            if (is_null($page_no) && $request->get('resume') != Constant::ENABLED) {
                // sessionを削除
                $session->remove('eccube.admin.product.search');
                $session->remove('eccube.admin.product.search.page_no');
            } else {
                // pagingなどの処理
                $searchData = $session->get('eccube.admin.product.search');
                if (is_null($page_no)) {
                    $page_no = intval($session->get('eccube.admin.product.search.page_no'));
                } else {
                    $session->set('eccube.admin.product.search.page_no', $page_no);
                }
                if (!is_null($searchData)) {
                    // 公開ステータス
                    // 1:公開, 2:非公開, 3:在庫なし
                    $status = $request->get('status');
                    if (empty($status)) {
                        $searchData['link_status'] = null;
                        $searchData['stock_status'] = null;
                    } else {
                        $searchData['link_status'] = $this->dispRepository->find($status);
                        $searchData['stock_status'] = null;
                        if ($status == $this->appConfig['admin_product_stock_status']) {
                            // 在庫なし
                            $searchData['link_status'] = null;
                            $searchData['stock_status'] = Constant::DISABLED;
                        }
                        $page_status = $status;
                    }
                    $session->set('eccube.admin.product.search', $searchData);

                    // 表示件数
                    $page_count = $request->get('page_count', $page_count);

                    $qb = $this->productRepository->getQueryBuilderBySearchDataForAdmin($searchData);

                    $event = new EventArgs(
                        array(
                            'qb' => $qb,
                            'searchData' => $searchData,
                        ),
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_INDEX_SEARCH, $event);
                    $searchData = $event->getArgument('searchData');

                    $pagination = $app['paginator']()->paginate(
                        $qb,
                        $page_no,
                        $page_count,
                        array('wrap-queries' => true)
                    );

                    // セッションから検索条件を復元(カテゴリ)
                    if (!empty($searchData['category_id'])) {
                        $searchData['category_id'] = $this->categoryRepository->find($searchData['category_id']);
                    }

                    // セッションから検索条件を復元(スーテタス)
                    if (isset($searchData['status']) && count($searchData['status']) > 0) {
                        $status_ids = array();
                        foreach ($searchData['status'] as $Status) {
                            $status_ids[] = $Status->getId();
                        }
                        $searchData['status'] = $this->dispRepository->findBy(array('id' => $status_ids));
                    }
                    $searchForm->setData($searchData);
                }
            }
        }

        return [
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'disps' => $disps,
            'pageMaxis' => $pageMaxis,
            'page_no' => $page_no,
            'page_status' => $page_status,
            'page_count' => $page_count,
            'active' => $active,
        ];
    }

    /**
     * @Method("POST")
     * @Route("/{_admin}/product/product/image/add", name="admin_product_image_add")
     */
    public function addImage(Application $app, Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException('リクエストが不正です');
        }

        $images = $request->files->get('admin_product');

        $files = array();
        if (count($images) > 0) {
            foreach ($images as $img) {
                foreach ($img as $image) {
                    //ファイルフォーマット検証
                    $mimeType = $image->getMimeType();
                    if (0 !== strpos($mimeType, 'image')) {
                        throw new UnsupportedMediaTypeHttpException('ファイル形式が不正です');
                    }

                    $extension = $image->getClientOriginalExtension();
                    $filename = date('mdHis') . uniqid('_') . '.' . $extension;
                    $image->move($this->appConfig['image_temp_realdir'], $filename);
                    $files[] = $filename;
                }
            }
        }

        $event = new EventArgs(
            array(
                'images' => $images,
                'files' => $files,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_ADD_IMAGE_COMPLETE, $event);
        $files = $event->getArgument('files');

        return $app->json(array('files' => $files), 200);
    }

    /**
     * @Route("/{_admin}/product/new", name="admin_product_product_new")
     * @Route("/{_admin}/product/product/{id}/edit", requirements={"id" = "\d+"}, name="admin_product_product_edit")
     * @Template("Product/product.twig")
     */
    public function edit(Application $app, Request $request, $id = null)
    {
        $has_class = false;
        if (is_null($id)) {
            $Product = new \Eccube\Entity\Product();
            $ProductClass = new \Eccube\Entity\ProductClass();
            $Disp = $this->dispRepository->find(\Eccube\Entity\Master\Disp::DISPLAY_HIDE);
            $Product
                ->setDelFlg(Constant::DISABLED)
                ->addProductClass($ProductClass)
                ->setStatus($Disp);
            $ProductClass
                ->setDelFlg(Constant::DISABLED)
                ->setStockUnlimited(true)
                ->setProduct($Product);
            $ProductStock = new \Eccube\Entity\ProductStock();
            $ProductClass->setProductStock($ProductStock);
            $ProductStock->setProductClass($ProductClass);
        } else {
            $Product = $this->productRepository->find($id);
            if (!$Product) {
                throw new NotFoundHttpException();
            }
            // 規格あり商品か
            $has_class = $Product->hasProductClass();
            if (!$has_class) {
                $ProductClasses = $Product->getProductClasses();
                $ProductClass = $ProductClasses[0];
                $BaseInfo = $this->baseInfoRepository->get();
                if ($BaseInfo->getOptionProductTaxRule() == Constant::ENABLED && $ProductClass->getTaxRule() && !$ProductClass->getTaxRule()->getDelFlg()) {
                    $ProductClass->setTaxRate($ProductClass->getTaxRule()->getTaxRate());
                }
                $ProductStock = $ProductClasses[0]->getProductStock();
            }
        }

        $builder = $this->formFactory
            ->createBuilder(ProductType::class, $Product);

        // 規格あり商品の場合、規格関連情報をFormから除外
        if ($has_class) {
            $builder->remove('class');
        }

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Product' => $Product,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        if (!$has_class) {
            $ProductClass->setStockUnlimited((boolean)$ProductClass->getStockUnlimited());
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

        $Tags = array();
        $ProductTags = $Product->getProductTag();
        foreach ($ProductTags as $ProductTag) {
            $Tags[] = $ProductTag->getTag();
        }
        $form['Tag']->setData($Tags);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                log_info('商品登録開始', array($id));
                $Product = $form->getData();

                if (!$has_class) {
                    $ProductClass = $form['class']->getData();

                    // 個別消費税
                    $BaseInfo = $this->baseInfoRepository->get();
                    if ($BaseInfo->getOptionProductTaxRule() == Constant::ENABLED) {
                        if ($ProductClass->getTaxRate() !== null) {
                            if ($ProductClass->getTaxRule()) {
                                if ($ProductClass->getTaxRule()->getDelFlg() == Constant::ENABLED) {
                                    $ProductClass->getTaxRule()->setDelFlg(Constant::DISABLED);
                                }

                                $ProductClass->getTaxRule()->setTaxRate($ProductClass->getTaxRate());
                            } else {
                                $taxrule = $this->taxRuleRepository->newTaxRule();
                                $taxrule->setTaxRate($ProductClass->getTaxRate());
                                $taxrule->setApplyDate(new \DateTime());
                                $taxrule->setProduct($Product);
                                $taxrule->setProductClass($ProductClass);
                                $ProductClass->setTaxRule($taxrule);
                            }
                        } else {
                            if ($ProductClass->getTaxRule()) {
                                $ProductClass->getTaxRule()->setDelFlg(Constant::ENABLED);
                            }
                        }
                    }
                    $this->entityManager->persist($ProductClass);

                    // 在庫情報を作成
                    if (!$ProductClass->getStockUnlimited()) {
                        $ProductStock->setStock($ProductClass->getStock());
                    } else {
                        // 在庫無制限時はnullを設定
                        $ProductStock->setStock(null);
                    }
                    $this->entityManager->persist($ProductStock);
                }

                // カテゴリの登録
                // 一度クリア
                /* @var $Product \Eccube\Entity\Product */
                foreach ($Product->getProductCategories() as $ProductCategory) {
                    $Product->removeProductCategory($ProductCategory);
                    $this->entityManager->remove($ProductCategory);
                }
                $this->entityManager->persist($Product);
                $this->entityManager->flush();

                $count = 1;
                $Categories = $form->get('Category')->getData();
                $categoriesIdList = array();
                foreach ($Categories as $Category) {
                    foreach($Category->getPath() as $ParentCategory){
                        if (!isset($categoriesIdList[$ParentCategory->getId()])){
                            $ProductCategory = $this->createProductCategory($Product, $ParentCategory, $count);
                            $this->entityManager->persist($ProductCategory);
                            $count++;
                            /* @var $Product \Eccube\Entity\Product */
                            $Product->addProductCategory($ProductCategory);
                            $categoriesIdList[$ParentCategory->getId()] = true;
                        }
                    }
                    if (!isset($categoriesIdList[$Category->getId()])){
                        $ProductCategory = $this->createProductCategory($Product, $Category, $count);
                        $this->entityManager->persist($ProductCategory);
                        $count++;
                        /* @var $Product \Eccube\Entity\Product */
                        $Product->addProductCategory($ProductCategory);
                        $categoriesIdList[$Category->getId()] = true;
                    }
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
                    $this->entityManager->persist($ProductImage);

                    // 移動
                    $file = new File($this->appConfig['image_temp_realdir'] . '/' . $add_image);
                    $file->move($this->appConfig['image_save_realdir']);
                }

                // 画像の削除
                $delete_images = $form->get('delete_images')->getData();
                foreach ($delete_images as $delete_image) {
                    $ProductImage = $this->productImageRepository
                        ->findOneBy(array('file_name' => $delete_image));

                    // 追加してすぐに削除した画像は、Entityに追加されない
                    if ($ProductImage instanceof \Eccube\Entity\ProductImage) {
                        $Product->removeProductImage($ProductImage);
                        $this->entityManager->remove($ProductImage);

                    }
                    $this->entityManager->persist($Product);

                    // 削除
                    $fs = new Filesystem();
                    $fs->remove($this->appConfig['image_save_realdir'] . '/' . $delete_image);
                }
                $this->entityManager->persist($Product);
                $this->entityManager->flush();


                $ranks = $request->get('rank_images');
                if ($ranks) {
                    foreach ($ranks as $rank) {
                        list($filename, $rank_val) = explode('//', $rank);
                        $ProductImage = $this->productImageRepository
                            ->findOneBy(array(
                                'file_name' => $filename,
                                'Product' => $Product,
                            ));
                        $ProductImage->setRank($rank_val);
                        $this->entityManager->persist($ProductImage);
                    }
                }
                $this->entityManager->flush();

                // 商品タグの登録
                // 商品タグを一度クリア
                $ProductTags = $Product->getProductTag();
                foreach ($ProductTags as $ProductTag) {
                    $Product->removeProductTag($ProductTag);
                    $this->entityManager->remove($ProductTag);
                }

                // 商品タグの登録
                $Tags = $form->get('Tag')->getData();
                foreach ($Tags as $Tag) {
                    $ProductTag = new ProductTag();
                    $ProductTag
                        ->setProduct($Product)
                        ->setTag($Tag);
                    $Product->addProductTag($ProductTag);
                    $this->entityManager->persist($ProductTag);
                }

                $Product->setUpdateDate(new \DateTime());
                $this->entityManager->flush();

                log_info('商品登録完了', array($id));

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'Product' => $Product,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_EDIT_COMPLETE, $event);

                $app->addSuccess('admin.register.complete', 'admin');

                return $app->redirect($app->url('admin_product_product_edit', array('id' => $Product->getId())));
            } else {
                log_info('商品登録チェックエラー', array($id));
                $app->addError('admin.register.failed', 'admin');
            }
        }

        // 検索結果の保持
        $builder = $this->formFactory
            ->createBuilder(SearchProductType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Product' => $Product,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_EDIT_SEARCH, $event);

        $searchForm = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $searchForm->handleRequest($request);
        }

        return [
            'Product' => $Product,
            'form' => $form->createView(),
            'searchForm' => $searchForm->createView(),
            'has_class' => $has_class,
            'id' => $id,
        ];
    }

    /**
     * @Method("DELETE")
     * @Route("/{_admin}/product/product/{id}/delete", requirements={"id" = "\d+"}, name="admin_product_product_delete")
     */
    public function delete(Application $app, Request $request, $id = null)
    {
        $this->isTokenValid($app);
        $session = $request->getSession();
        $page_no = intval($session->get('eccube.admin.product.search.page_no'));
        $page_no = $page_no ? $page_no : Constant::ENABLED;

        if (!is_null($id)) {
            /* @var $Product \Eccube\Entity\Product */
            $Product = $this->productRepository->find($id);
            if (!$Product) {
                $app->deleteMessage();
                return $app->redirect($app->url('admin_product_page', array('page_no' => $page_no)).'?resume='.Constant::ENABLED);
            }

            if ($Product instanceof \Eccube\Entity\Product) {
                log_info('商品削除開始', array($id));

                $Product->setDelFlg(Constant::ENABLED);

                $ProductClasses = $Product->getProductClasses();
                $deleteImages = array();
                foreach ($ProductClasses as $ProductClass) {
                    $ProductClass->setDelFlg(Constant::ENABLED);
                    $Product->removeProductClass($ProductClass);

                    $ProductClasses = $Product->getProductClasses();
                    foreach ($ProductClasses as $ProductClass) {
                        $ProductClass->setDelFlg(Constant::ENABLED);
                        $Product->removeProductClass($ProductClass);

                        $ProductStock = $ProductClass->getProductStock();
                        $this->entityManager->remove($ProductStock);
                    }

                    $ProductImages = $Product->getProductImage();
                    foreach ($ProductImages as $ProductImage) {
                        $Product->removeProductImage($ProductImage);
                        $deleteImages[] = $ProductImage->getFileName();
                        $this->entityManager->remove($ProductImage);
                    }

                    $ProductCategories = $Product->getProductCategories();
                    foreach ($ProductCategories as $ProductCategory) {
                        $Product->removeProductCategory($ProductCategory);
                        $this->entityManager->remove($ProductCategory);
                    }

                }

                $this->entityManager->persist($Product);

                $this->entityManager->flush();

                $event = new EventArgs(
                    array(
                        'Product' => $Product,
                        'ProductClass' => $ProductClasses,
                        'deleteImages' => $deleteImages,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_DELETE_COMPLETE, $event);
                $deleteImages = $event->getArgument('deleteImages');

                // 画像ファイルの削除(commit後に削除させる)
                foreach ($deleteImages as $deleteImage) {
                    try {
                        $fs = new Filesystem();
                        $fs->remove($this->appConfig['image_save_realdir'] . '/' . $deleteImage);
                    } catch (\Exception $e) {
                        // エラーが発生しても無視する
                    }
                }

                log_info('商品削除完了', array($id));

                $app->addSuccess('admin.delete.complete', 'admin');
            } else {
                log_info('商品削除エラー', array($id));
                $app->addError('admin.delete.failed', 'admin');
            }
        } else {
            log_info('商品削除エラー', array($id));
            $app->addError('admin.delete.failed', 'admin');
        }

        return $app->redirect($app->url('admin_product_page', array('page_no' => $page_no)).'?resume='.Constant::ENABLED);
    }

    /**
     * @Method("POST")
     * @Route("/{_admin}/product/product/{id}/copy", requirements={"id" = "\d+"}, name="admin_product_product_copy")
     */
    public function copy(Application $app, Request $request, $id = null)
    {
        $this->isTokenValid($app);

        if (!is_null($id)) {
            $Product = $this->productRepository->find($id);
            if ($Product instanceof \Eccube\Entity\Product) {
                $CopyProduct = clone $Product;
                $CopyProduct->copy();
                $Disp = $this->dispRepository->find(\Eccube\Entity\Master\Disp::DISPLAY_HIDE);
                $CopyProduct->setStatus($Disp);

                $CopyProductCategories = $CopyProduct->getProductCategories();
                foreach ($CopyProductCategories as $Category) {
                    $this->entityManager->persist($Category);
                }

                // 規格あり商品の場合は, デフォルトの商品規格を取得し登録する.
                if ($CopyProduct->hasProductClass()) {
                    $softDeleteFilter = $this->entityManager->getFilters()->getFilter('soft_delete');
                    $softDeleteFilter->setExcludes(array(
                        'Eccube\Entity\ProductClass'
                    ));
                    $dummyClass = $this->productClassRepository->findOneBy(array(
                        'del_flg' => \Eccube\Common\Constant::ENABLED,
                        'ClassCategory1' => null,
                        'ClassCategory2' => null,
                        'Product' => $Product,
                    ));
                    $dummyClass = clone $dummyClass;
                    $dummyClass->setProduct($CopyProduct);
                    $CopyProduct->addProductClass($dummyClass);
                    $softDeleteFilter->setExcludes(array());
                }

                $CopyProductClasses = $CopyProduct->getProductClasses();
                foreach ($CopyProductClasses as $Class) {
                    $Stock = $Class->getProductStock();
                    $CopyStock = clone $Stock;
                    $CopyStock->setProductClass($Class);
                    $this->entityManager->persist($CopyStock);

                    $this->entityManager->persist($Class);
                }
                $Images = $CopyProduct->getProductImage();
                foreach ($Images as $Image) {

                    // 画像ファイルを新規作成
                    $extension = pathinfo($Image->getFileName(), PATHINFO_EXTENSION);
                    $filename = date('mdHis') . uniqid('_') . '.' . $extension;
                    try {
                        $fs = new Filesystem();
                        $fs->copy($this->appConfig['image_save_realdir'] . '/' . $Image->getFileName(), $this->appConfig['image_save_realdir'] . '/' . $filename);
                    } catch (\Exception $e) {
                        // エラーが発生しても無視する
                    }
                    $Image->setFileName($filename);

                    $this->entityManager->persist($Image);
                }
                $Tags = $CopyProduct->getProductTag();
                foreach ($Tags as $Tag) {
                    $this->entityManager->persist($Tag);
                }

                $this->entityManager->persist($CopyProduct);

                $this->entityManager->flush();

                $event = new EventArgs(
                    array(
                        'Product' => $Product,
                        'CopyProduct' => $CopyProduct,
                        'CopyProductCategories' => $CopyProductCategories,
                        'CopyProductClasses' => $CopyProductClasses,
                        'images' => $Images,
                        'Tags' => $Tags,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_COPY_COMPLETE, $event);

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

    /**
     * @Route("/{_admin}/product/product/{id}/display", requirements={"id" = "\d+"}, name="admin_product_product_display")
     */
    public function display(Application $app, Request $request, $id = null)
    {
        $event = new EventArgs(
            array(),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_DISPLAY_COMPLETE, $event);

        if (!is_null($id)) {
            return $app->redirect($app->url('product_detail', array('id' => $id, 'admin' => '1')));
        }

        return $app->redirect($app->url('admin_product'));
    }

    /**
     * 商品CSVの出力.
     *
     * @Route("export", name="admin_product_export")
     *
     * @param Application $app
     * @param Request $request
     * @return StreamedResponse
     */
    public function export(Application $app, Request $request)
    {
        // タイムアウトを無効にする.
        set_time_limit(0);

        // sql loggerを無効にする.
        $em = $this->entityManager;
        $em->getConfiguration()->setSQLLogger(null);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($app, $request) {

            // CSV種別を元に初期化.
            $this->csvExportService->initCsvType(CsvType::CSV_TYPE_PRODUCT);

            // ヘッダ行の出力.
            $this->csvExportService->exportHeader();

            // 商品データ検索用のクエリビルダを取得.
            $qb = $this->csvExportService
                ->getProductQueryBuilder($request);

            // Get stock status
            $isOutOfStock = 0;
            $session = $request->getSession();
            if ($session->has('eccube.admin.product.search')) {
                $searchData = $session->get('eccube.admin.product.search', array());
                if (isset($searchData['stock_status']) && $searchData['stock_status'] === 0) {
                    $isOutOfStock = 1;
                }
            }

            // joinする場合はiterateが使えないため, select句をdistinctする.
            // http://qiita.com/suin/items/2b1e98105fa3ef89beb7
            // distinctのmysqlとpgsqlの挙動をあわせる.
            // http://uedatakeshi.blogspot.jp/2010/04/distinct-oeder-by-postgresmysql.html
            $qb->resetDQLPart('select')
                ->resetDQLPart('orderBy')
                ->orderBy('p.update_date', 'DESC');

            if ($isOutOfStock) {
                $qb->select('p, pc')
                    ->distinct();
            } else {
                $qb->select('p')
                    ->distinct();
            }
            // データ行の出力.
            $this->csvExportService->setExportQueryBuilder($qb);

            $this->csvExportService->exportData(function ($entity, CsvExportService $csvService) use ($app, $request) {
                $Csvs = $csvService->getCsvs();

                /** @var $Product \Eccube\Entity\Product */
                $Product = $entity;

                /** @var $ProductClassess \Eccube\Entity\ProductClass[] */
                $ProductClassess = $Product->getProductClasses();

                foreach ($ProductClassess as $ProductClass) {
                    $ExportCsvRow = new \Eccube\Entity\ExportCsvRow();

                    // CSV出力項目と合致するデータを取得.
                    foreach ($Csvs as $Csv) {
                        // 商品データを検索.
                        $ExportCsvRow->setData($csvService->getData($Csv, $Product));
                        if ($ExportCsvRow->isDataNull()) {
                            // 商品規格情報を検索.
                            $ExportCsvRow->setData($csvService->getData($Csv, $ProductClass));
                        }

                        $event = new EventArgs(
                            array(
                                'csvService' => $csvService,
                                'Csv' => $Csv,
                                'ProductClass' => $ProductClass,
                                'ExportCsvRow' => $ExportCsvRow,
                            ),
                            $request
                        );
                        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_CSV_EXPORT, $event);

                        $ExportCsvRow->pushData();
                    }

                    // $row[] = number_format(memory_get_usage(true));
                    // 出力.
                    $csvService->fputcsv($ExportCsvRow->getRow());
                }
            });
        });

        $now = new \DateTime();
        $filename = 'product_' . $now->format('YmdHis') . '.csv';
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        $response->send();

        log_info('商品CSV出力ファイル名', array($filename));

        return $response;
    }
    
    /**
     * ProductCategory作成
     * @param \Eccube\Entity\Product $Product
     * @param \Eccube\Entity\Category $Category
     * @return \Eccube\Entity\ProductCategory
     */
    private function createProductCategory($Product, $Category, $count)
    {
        $ProductCategory = new \Eccube\Entity\ProductCategory();
        $ProductCategory->setProduct($Product);
        $ProductCategory->setProductId($Product->getId());
        $ProductCategory->setCategory($Category);
        $ProductCategory->setCategoryId($Category->getId());
        $ProductCategory->setRank($count);
        
        return $ProductCategory;
    }
}
