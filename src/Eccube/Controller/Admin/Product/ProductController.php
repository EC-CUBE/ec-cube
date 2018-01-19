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

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Master\CsvType;
use Eccube\Entity\ProductTag;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\ProductType;
use Eccube\Form\Type\Admin\SearchProductType;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Repository\ProductImageRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Service\CsvExportService;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\ProductStock;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductCategory;
use Eccube\Entity\ExportCsvRow;

/**
 * @Route(service=ProductController::class)
 */
class ProductController extends AbstractController
{
    /**
     * @var CsvExportService
     */
    protected $csvExportService;

    /**
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * @var ProductImageRepository
     */
    protected $productImageRepository;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var PageMaxRepository
     */
    protected $pageMaxRepository;

    /**
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;

    /**
     * ProductController constructor.
     *
     * @param CsvExportService $csvExportService
     * @param ProductClassRepository $productClassRepository
     * @param ProductImageRepository $productImageRepository
     * @param TaxRuleRepository $taxRuleRepository
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     * @param BaseInfo $baseInfo
     * @param PageMaxRepository $pageMaxRepository
     * @param ProductStatusRepository $productStatusRepository
     */
    public function __construct(
        CsvExportService $csvExportService,
        ProductClassRepository $productClassRepository,
        ProductImageRepository $productImageRepository,
        TaxRuleRepository $taxRuleRepository,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        BaseInfo $baseInfo,
        PageMaxRepository $pageMaxRepository,
        ProductStatusRepository $productStatusRepository
    ) {
        $this->csvExportService = $csvExportService;
        $this->productClassRepository = $productClassRepository;
        $this->productImageRepository = $productImageRepository;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->BaseInfo = $baseInfo;
        $this->pageMaxRepository = $pageMaxRepository;
        $this->productStatusRepository = $productStatusRepository;
    }

    /**
     * @Route("/%admin_route%/product", name="admin_product")
     * @Route("/%admin_route%/product/page/{page_no}", requirements={"page_no" = "\d+"}, name="admin_product_page")
     * @Template("@admin/Product/index.twig")
     */
    public function index(Request $request, $page_no = null, Paginator $paginator)
    {

        $session = $this->session;

        $builder = $this->formFactory
            ->createBuilder(SearchProductType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_INDEX_INITIALIZE, $event);

        $searchForm = $builder->getForm();

        $pagination = [];

        $ProductStatuses = $this->productStatusRepository->findAll();
        $pageMaxis = $this->pageMaxRepository->findAll();
        $page_count = $this->eccubeConfig['default_page_count'];
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
                    [
                        'qb' => $qb,
                        'searchData' => $searchData,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_INDEX_SEARCH, $event);
                $searchData = $event->getArgument('searchData');

                $pagination = $paginator->paginate(
                    $qb,
                    $page_no,
                    $page_count,
                    ['wrap-queries' => true]
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
                        $searchData['link_status'] = $this->productStatusRepository->find($status);
                        $searchData['stock_status'] = null;
                        if ($status == $this->eccubeConfig['admin_product_stock_status']) {
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
                        [
                            'qb' => $qb,
                            'searchData' => $searchData,
                        ],
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_INDEX_SEARCH, $event);
                    $searchData = $event->getArgument('searchData');

                    $pagination = $paginator->paginate(
                        $qb,
                        $page_no,
                        $page_count,
                        ['wrap-queries' => true]
                    );

                    // セッションから検索条件を復元(カテゴリ)
                    if (!empty($searchData['category_id'])) {
                        $searchData['category_id'] = $this->categoryRepository->find($searchData['category_id']);
                    }

                    // セッションから検索条件を復元(スーテタス)
                    if (isset($searchData['status']) && count($searchData['status']) > 0) {
                        $status_ids = [];
                        foreach ($searchData['status'] as $Status) {
                            $status_ids[] = $Status->getId();
                        }
                        $searchData['status'] = $this->productStatusRepository->findBy(['id' => $status_ids]);
                    }
                    $searchForm->setData($searchData);
                }
            }
        }

        return [
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'productStatuses' => $ProductStatuses,
            'pageMaxis' => $pageMaxis,
            'page_no' => $page_no,
            'page_status' => $page_status,
            'page_count' => $page_count,
            'active' => $active,
        ];
    }

    /**
     * @Method("POST")
     * @Route("/%admin_route%/product/product/image/add", name="admin_product_image_add")
     */
    public function addImage(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException('リクエストが不正です');
        }

        $images = $request->files->get('admin_product');

        $files = [];
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
                    $image->move($this->eccubeConfig['image_temp_realdir'], $filename);
                    $files[] = $filename;
                }
            }
        }

        $event = new EventArgs(
            [
                'images' => $images,
                'files' => $files,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_ADD_IMAGE_COMPLETE, $event);
        $files = $event->getArgument('files');

        return $this->json(['files' => $files], 200);
    }

    /**
     * @Route("/%admin_route%/product/product/new", name="admin_product_product_new")
     * @Route("/%admin_route%/product/product/{id}/edit", requirements={"id" = "\d+"}, name="admin_product_product_edit")
     * @Template("@admin/Product/product.twig")
     */
    public function edit(Request $request, $id = null)
    {
        $has_class = false;
        if (is_null($id)) {
            $Product = new Product();
            $ProductClass = new ProductClass();
            $ProductStatus = $this->productStatusRepository->find(ProductStatus::DISPLAY_HIDE);
            $Product
                ->addProductClass($ProductClass)
                ->setStatus($ProductStatus);
            $ProductClass
                ->setVisible(true)
                ->setStockUnlimited(true)
                ->setProduct($Product);
            $ProductStock = new ProductStock();
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
                if ($this->BaseInfo->isOptionProductTaxRule() && $ProductClass->getTaxRule()) {
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
            [
                'builder' => $builder,
                'Product' => $Product,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        if (!$has_class) {
            $ProductClass->setStockUnlimited($ProductClass->isStockUnlimited());
            $form['class']->setData($ProductClass);
        }

        // ファイルの登録
        $images = [];
        $ProductImages = $Product->getProductImage();
        foreach ($ProductImages as $ProductImage) {
            $images[] = $ProductImage->getFileName();
        }
        $form['images']->setData($images);

        $categories = [];
        $ProductCategories = $Product->getProductCategories();
        foreach ($ProductCategories as $ProductCategory) {
            /* @var $ProductCategory \Eccube\Entity\ProductCategory */
            $categories[] = $ProductCategory->getCategory();
        }
        $form['Category']->setData($categories);

        $Tags = [];
        $ProductTags = $Product->getProductTag();
        foreach ($ProductTags as $ProductTag) {
            $Tags[] = $ProductTag->getTag();
        }
        $form['Tag']->setData($Tags);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                log_info('商品登録開始', [$id]);
                $Product = $form->getData();

                if (!$has_class) {
                    $ProductClass = $form['class']->getData();

                    // 個別消費税
                    if ($this->BaseInfo->isOptionProductTaxRule()) {
                        if ($ProductClass->getTaxRate() !== null) {
                            if ($ProductClass->getTaxRule()) {
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
                                $this->taxRuleRepository->delete($ProductClass->getTaxRule());
                                $ProductClass->setTaxRule(null);
                            }
                        }
                    }
                    $this->entityManager->persist($ProductClass);

                    // 在庫情報を作成
                    if (!$ProductClass->isStockUnlimited()) {
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
                $categoriesIdList = [];
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
                        ->setSortNo(1);
                    $Product->addProductImage($ProductImage);
                    $this->entityManager->persist($ProductImage);

                    // 移動
                    $file = new File($this->eccubeConfig['image_temp_realdir'] . '/' . $add_image);
                    $file->move($this->eccubeConfig['image_save_realdir']);
                }

                // 画像の削除
                $delete_images = $form->get('delete_images')->getData();
                foreach ($delete_images as $delete_image) {
                    $ProductImage = $this->productImageRepository
                        ->findOneBy(['file_name' => $delete_image]);

                    // 追加してすぐに削除した画像は、Entityに追加されない
                    if ($ProductImage instanceof ProductImage) {
                        $Product->removeProductImage($ProductImage);
                        $this->entityManager->remove($ProductImage);

                    }
                    $this->entityManager->persist($Product);

                    // 削除
                    $fs = new Filesystem();
                    $fs->remove($this->eccubeConfig['image_save_realdir'] . '/' . $delete_image);
                }
                $this->entityManager->persist($Product);
                $this->entityManager->flush();


                $sortNos = $request->get('sort_no_images');
                if ($sortNos) {
                    foreach ($sortNos as $sortNo) {
                        list($filename, $sortNo_val) = explode('//', $sortNo);
                        $ProductImage = $this->productImageRepository
                            ->findOneBy([
                                'file_name' => $filename,
                                'Product' => $Product,
                            ]);
                        $ProductImage->setSortNo($sortNo_val);
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

                log_info('商品登録完了', [$id]);

                $event = new EventArgs(
                    [
                        'form' => $form,
                        'Product' => $Product,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_EDIT_COMPLETE, $event);

                $msg = $this->translator->trans('admin.register.complete');
                $this->addSuccess($msg, 'admin');

                return $this->redirectToRoute('admin_product_product_edit', ['id' => $Product->getId()]);
            } else {
                log_info('商品登録チェックエラー', [$id]);
                $msg = $this->translator->trans('admin.register.failed');
                $this->addError($msg, 'admin');
            }
        }

        // 検索結果の保持
        $builder = $this->formFactory
            ->createBuilder(SearchProductType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Product' => $Product,
            ],
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
     * @Route("/%admin_route%/product/product/{id}/delete", requirements={"id" = "\d+"}, name="admin_product_product_delete")
     */
    public function delete(Request $request, $id = null)
    {
        $this->isTokenValid();
        $session = $request->getSession();
        $page_no = intval($session->get('eccube.admin.product.search.page_no'));
        $page_no = $page_no ? $page_no : Constant::ENABLED;

        if (!is_null($id)) {
            /* @var $Product \Eccube\Entity\Product */
            $Product = $this->productRepository->find($id);
            if (!$Product) {
                $this->deleteMessage();
                $rUrl = $this->generateUrl('admin_product_page', ['page_no' => $page_no]) . '?resume=' . Constant::ENABLED;
                return $this->redirect($rUrl);
            }

            if ($Product instanceof Product) {
                log_info('商品削除開始', [$id]);

                $deleteImages = $Product->getProductImage();
                $ProductClasses = $Product->getProductClasses();

                try {
                    $this->productRepository->delete($Product);
                    $this->entityManager->flush();

                    $event = new EventArgs(
                        [
                            'Product' => $Product,
                            'ProductClass' => $ProductClasses,
                            'deleteImages' => $deleteImages,
                        ],
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_DELETE_COMPLETE, $event);
                    $deleteImages = $event->getArgument('deleteImages');

                    // 画像ファイルの削除(commit後に削除させる)
                    foreach ($deleteImages as $deleteImage) {
                        try {
                            $fs = new Filesystem();
                            $fs->remove($this->eccubeConfig['image_save_realdir'] . '/' . $deleteImage);
                        } catch (\Exception $e) {
                            // エラーが発生しても無視する
                        }
                    }

                    log_info('商品削除完了', [$id]);

                    $message = $this->translator->trans('admin.delete.complete');
                    $this->addSuccess($message, 'admin');

                } catch (ForeignKeyConstraintViolationException $e) {
                    log_info('商品削除エラー', [$id]);
                    $message = $this->translator->trans('admin.delete.failed.foreign_key', ['%name%' => '商品']);
                    $this->addError($message, 'admin');
                }
            } else {
                log_info('商品削除エラー', [$id]);
                $message = $this->translator->trans('admin.delete.failed');
                $this->addError($message, 'admin');
            }
        } else {
            log_info('商品削除エラー', [$id]);
            $message = $this->translator->trans('admin.delete.failed');
            $this->addError($message, 'admin');
        }

        $rUrl = $this->generateUrl('admin_product_page', ['page_no' => $page_no]).'?resume='.Constant::ENABLED;

        return $this->redirect($rUrl);
    }

    /**
     * @Method("POST")
     * @Route("/%admin_route%/product/product/{id}/copy", requirements={"id" = "\d+"}, name="admin_product_product_copy")
     */
    public function copy(Request $request, $id = null)
    {
        $this->isTokenValid();

        if (!is_null($id)) {
            $Product = $this->productRepository->find($id);
            if ($Product instanceof Product) {
                $CopyProduct = clone $Product;
                $CopyProduct->copy();
                $ProductStatus = $this->productStatusRepository->find(ProductStatus::DISPLAY_HIDE);
                $CopyProduct->setStatus($ProductStatus);

                $CopyProductCategories = $CopyProduct->getProductCategories();
                foreach ($CopyProductCategories as $Category) {
                    $this->entityManager->persist($Category);
                }

                // 規格あり商品の場合は, デフォルトの商品規格を取得し登録する.
                if ($CopyProduct->hasProductClass()) {
                    $dummyClass = $this->productClassRepository->findOneBy([
                        'visible' => false,
                        'ClassCategory1' => null,
                        'ClassCategory2' => null,
                        'Product' => $Product,
                    ]);
                    $dummyClass = clone $dummyClass;
                    $dummyClass->setProduct($CopyProduct);
                    $CopyProduct->addProductClass($dummyClass);
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
                        $fs->copy($this->eccubeConfig['image_save_realdir'] . '/' . $Image->getFileName(), $this->eccubeConfig['image_save_realdir'] . '/' . $filename);
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
                    [
                        'Product' => $Product,
                        'CopyProduct' => $CopyProduct,
                        'CopyProductCategories' => $CopyProductCategories,
                        'CopyProductClasses' => $CopyProductClasses,
                        'images' => $Images,
                        'Tags' => $Tags,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_COPY_COMPLETE, $event);

                $msg = $this->translator->trans('admin.product.copy.complete');
                $this->addSuccess($msg, 'admin');

                return $this->redirectToRoute('admin_product_product_edit', ['id' => $CopyProduct->getId()]);
            } else {
                $msg = $this->translator->trans('admin.product.copy.failed');
                $this->addError($msg, 'admin');
            }
        } else {
            $msg = $this->translator->trans('admin.product.copy.failed');
            $this->addError($msg, 'admin');
        }

        return $this->redirectToRoute('admin_product');
    }

    /**
     * @Route("/%admin_route%/product/product/{id}/display", requirements={"id" = "\d+"}, name="admin_product_product_display")
     */
    public function display(Request $request, $id = null)
    {
        $event = new EventArgs(
            [],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_DISPLAY_COMPLETE, $event);

        if (!is_null($id)) {
            return $this->redirectToRoute('product_detail', ['id' => $id, 'admin' => '1']);
        }

        return $this->redirectToRoute('admin_product');
    }

    /**
     * 商品CSVの出力.
     *
     * @Route("export", name="admin_product_export")
     *
     * @param Request $request
     *
     * @return StreamedResponse
     */
    public function export(Request $request)
    {
        // タイムアウトを無効にする.
        set_time_limit(0);

        // sql loggerを無効にする.
        $em = $this->entityManager;
        $em->getConfiguration()->setSQLLogger(null);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($request) {

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
                $searchData = $session->get('eccube.admin.product.search', []);
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

            $this->csvExportService->exportData(function ($entity, CsvExportService $csvService) use ($request) {
                $Csvs = $csvService->getCsvs();

                /** @var $Product \Eccube\Entity\Product */
                $Product = $entity;

                /** @var $ProductClassess \Eccube\Entity\ProductClass[] */
                $ProductClassess = $Product->getProductClasses();

                foreach ($ProductClassess as $ProductClass) {
                    $ExportCsvRow = new ExportCsvRow();

                    // CSV出力項目と合致するデータを取得.
                    foreach ($Csvs as $Csv) {
                        // 商品データを検索.
                        $ExportCsvRow->setData($csvService->getData($Csv, $Product));
                        if ($ExportCsvRow->isDataNull()) {
                            // 商品規格情報を検索.
                            $ExportCsvRow->setData($csvService->getData($Csv, $ProductClass));
                        }

                        $event = new EventArgs(
                            [
                                'csvService' => $csvService,
                                'Csv' => $Csv,
                                'ProductClass' => $ProductClass,
                                'ExportCsvRow' => $ExportCsvRow,
                            ],
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

        log_info('商品CSV出力ファイル名', [$filename]);

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
        $ProductCategory = new ProductCategory();
        $ProductCategory->setProduct($Product);
        $ProductCategory->setProductId($Product->getId());
        $ProductCategory->setCategory($Category);
        $ProductCategory->setCategoryId($Category->getId());
        $ProductCategory->setSortNo($count);
        
        return $ProductCategory;
    }
}
