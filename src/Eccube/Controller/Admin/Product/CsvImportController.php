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
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Category;
use Eccube\Entity\Product;
use Eccube\Entity\ProductCategory;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductStock;
use Eccube\Entity\ProductTag;
use Eccube\Exception\CsvImportException;
use Eccube\Form\Type\Admin\CsvImportType;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\DeliveryDurationRepository;
use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Repository\Master\SaleTypeRepository;
use Eccube\Repository\Master\TagRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Service\CsvImportService;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route(service=CsvImportController::class)
 */
class CsvImportController
{
    /**
     * @Inject(DeliveryDurationRepository::class)
     * @var DeliveryDurationRepository
     */
    protected $deliveryDurationRepository;

    /**
     * @Inject(SaleTypeRepository::class)
     * @var SaleTypeRepository
     */
    protected $saleTypeRepository;

    /**
     * @Inject(TagRepository::class)
     * @var TagRepository
     */
    protected $tagRepository;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject(CategoryRepository::class)
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @Inject(ClassCategoryRepository::class)
     * @var ClassCategoryRepository
     */
    protected $classCategoryRepository;

    /**
     * @Inject(ProductStatusRepository::class)
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;

    /**
     * @Inject(ProductRepository::class)
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @Inject(BaseInfo::class)
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;


    private $errors = array();

    private $fileName;

    private $em;


    /**
     * 商品登録CSVアップロード
     *
     * @Route("/{_admin}/product/product_csv_upload", name="admin_product_csv_import")
     * @Template("Product/csv_product.twig")
     */
    public function csvProduct(Application $app, Request $request)
    {
        $form = $this->formFactory->createBuilder(CsvImportType::class)->getForm();

        $headers = $this->getProductCsvHeader();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $formFile = $form['import_file']->getData();

                if (!empty($formFile)) {

                    log_info('商品CSV登録開始');

                    $data = $this->getImportData($app, $formFile);
                    if ($data === false) {
                        $this->addErrors('CSVのフォーマットが一致しません。');
                        return $this->render($app, $form, $headers);
                    }

                    $keys = array_keys($headers);
                    $columnHeaders = $data->getColumnHeaders();
                    if ($keys !== $columnHeaders) {
                        $this->addErrors('CSVのフォーマットが一致しません。');
                        return $this->render($app, $form, $headers);
                    }

                    $size = count($data);
                    if ($size < 1) {
                        $this->addErrors('CSVデータが存在しません。');
                        return $this->render($app, $form, $headers);
                    }

                    $headerSize = count($keys);

                    $this->em = $this->entityManager;
                    $this->em->getConfiguration()->setSQLLogger(null);

                    $this->em->getConnection()->beginTransaction();

                    // CSVファイルの登録処理
                    foreach ($data as $row) {

                        if ($headerSize != count($row)) {
                            $this->addErrors(($data->key() + 1) . '行目のCSVフォーマットが一致しません。');
                            return $this->render($app, $form, $headers);
                        }

                        if ($row['商品ID'] == '') {
                            $Product = new Product();
                            $this->em->persist($Product);
                        } else {
                            if (preg_match('/^\d+$/', $row['商品ID'])) {
                                $Product = $this->productRepository->find($row['商品ID']);
                                if (!$Product) {
                                    $this->addErrors(($data->key() + 1) . '行目の商品IDが存在しません。');
                                    return $this->render($app, $form, $headers);
                                }
                            } else {
                                $this->addErrors(($data->key() + 1) . '行目の商品IDが存在しません。');
                                return $this->render($app, $form, $headers);
                            }

                        }

                        if ($row['公開ステータス(ID)'] == '') {
                            $this->addErrors(($data->key() + 1) . '行目の公開ステータス(ID)が設定されていません。');
                        } else {
                            if (preg_match('/^\d+$/', $row['公開ステータス(ID)'])) {
                                $ProductStatus = $this->productStatusRepository->find($row['公開ステータス(ID)']);
                                if (!$ProductStatus) {
                                    $this->addErrors(($data->key() + 1) . '行目の公開ステータス(ID)が存在しません。');
                                } else {
                                    $Product->setStatus($ProductStatus);
                                }
                            } else {
                                $this->addErrors(($data->key() + 1) . '行目の公開ステータス(ID)が存在しません。');
                            }
                        }

                        if (StringUtil::isBlank($row['商品名'])) {
                            $this->addErrors(($data->key() + 1) . '行目の商品名が設定されていません。');
                            return $this->render($app, $form, $headers);
                        } else {
                            $Product->setName(StringUtil::trimAll($row['商品名']));
                        }

                        if (StringUtil::isNotBlank($row['ショップ用メモ欄'])) {
                            $Product->setNote(StringUtil::trimAll($row['ショップ用メモ欄']));
                        } else {
                            $Product->setNote(null);
                        }

                        if (StringUtil::isNotBlank($row['商品説明(一覧)'])) {
                            $Product->setDescriptionList(StringUtil::trimAll($row['商品説明(一覧)']));
                        } else {
                            $Product->setDescriptionList(null);
                        }

                        if (StringUtil::isNotBlank($row['商品説明(詳細)'])) {
                            $Product->setDescriptionDetail(StringUtil::trimAll($row['商品説明(詳細)']));
                        } else {
                            $Product->setDescriptionDetail(null);
                        }

                        if (StringUtil::isNotBlank($row['検索ワード'])) {
                            $Product->setSearchWord(StringUtil::trimAll($row['検索ワード']));
                        } else {
                            $Product->setSearchWord(null);
                        }

                        if (StringUtil::isNotBlank($row['フリーエリア'])) {
                            $Product->setFreeArea(StringUtil::trimAll($row['フリーエリア']));
                        } else {
                            $Product->setFreeArea(null);
                        }

                        // 商品画像登録
                        $this->createProductImage($row, $Product);

                        $this->em->flush($Product);

                        // 商品カテゴリ登録
                        $this->createProductCategory($row, $Product, $app, $data);

                        //タグ登録
                        $this->createProductTag($row, $Product, $app, $data);

                        // 商品規格が存在しなければ新規登録
                        /** @var ProductClass[] $ProductClasses */
                        $ProductClasses = $Product->getProductClasses();
                        if ($ProductClasses->count() < 1) {
                            // 規格分類1(ID)がセットされていると規格なし商品、規格あり商品を作成
                            $ProductClassOrg = $this->createProductClass($row, $Product, $app, $data);
                            if ($this->BaseInfo->isOptionProductDeliveryFee()) {
                                if ($row['送料'] != '') {
                                    $deliveryFee = str_replace(',', '', $row['送料']);
                                    if (preg_match('/^\d+$/', $deliveryFee) && $deliveryFee >= 0) {
                                        $ProductClassOrg->setDeliveryFee($deliveryFee);
                                    } else {
                                        $this->addErrors(($data->key() + 1) . '行目の送料は0以上の数値を設定してください。');
                                    }
                                }
                            }

                            if ($row['規格分類1(ID)'] != '') {

                                if ($row['規格分類1(ID)'] == $row['規格分類2(ID)']) {
                                    $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)と規格分類2(ID)には同じ値を使用できません。');
                                } else {
                                    // 商品規格あり
                                    // 企画分類あり商品を作成
                                    $ProductClass = clone $ProductClassOrg;
                                    $ProductStock = clone $ProductClassOrg->getProductStock();

                                    // 規格分類1、規格分類2がnullであるデータを非表示
                                    $ProductClassOrg->setVisible(false);

                                    // 規格分類1、2をそれぞれセットし作成
                                    $ClassCategory1 = null;
                                    if (preg_match('/^\d+$/', $row['規格分類1(ID)'])) {
                                        $ClassCategory1 = $this->classCategoryRepository->find($row['規格分類1(ID)']);
                                        if (!$ClassCategory1) {
                                            $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)が存在しません。');
                                        } else {
                                            $ProductClass->setClassCategory1($ClassCategory1);
                                        }
                                    } else {
                                        $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)が存在しません。');
                                    }

                                    if ($row['規格分類2(ID)'] != '') {
                                        if (preg_match('/^\d+$/', $row['規格分類2(ID)'])) {
                                            $ClassCategory2 = $this->classCategoryRepository->find($row['規格分類2(ID)']);
                                            if (!$ClassCategory2) {
                                                $this->addErrors(($data->key() + 1) . '行目の規格分類2(ID)が存在しません。');
                                            } else {
                                                if ($ClassCategory1 &&
                                                    ($ClassCategory1->getClassName()->getId() == $ClassCategory2->getClassName()->getId())
                                                ) {
                                                    $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)と規格分類2(ID)の規格名が同じです。');
                                                } else {
                                                    $ProductClass->setClassCategory2($ClassCategory2);
                                                }
                                            }
                                        } else {
                                            $this->addErrors(($data->key() + 1) . '行目の規格分類2(ID)が存在しません。');
                                        }
                                    }
                                    $ProductClass->setProductStock($ProductStock);
                                    $ProductStock->setProductClass($ProductClass);

                                    $this->em->persist($ProductClass);
                                    $this->em->persist($ProductStock);
                                }

                            } else {
                                if ($row['規格分類2(ID)'] != '') {
                                    $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)が存在しません。');
                                }
                            }

                        } else {
                            // 商品規格の更新

                            $flag = false;
                            $classCategoryId1 = $row['規格分類1(ID)'] == '' ? null : $row['規格分類1(ID)'];
                            $classCategoryId2 = $row['規格分類2(ID)'] == '' ? null : $row['規格分類2(ID)'];

                            foreach ($ProductClasses as $pc) {

                                $classCategory1 = is_null($pc->getClassCategory1()) ? null : $pc->getClassCategory1()->getId();
                                $classCategory2 = is_null($pc->getClassCategory2()) ? null : $pc->getClassCategory2()->getId();

                                // 登録されている商品規格を更新
                                if ($classCategory1 == $classCategoryId1 &&
                                    $classCategory2 == $classCategoryId2
                                ) {
                                    $this->updateProductClass($row, $Product, $pc, $app, $data);

                                    if ($this->BaseInfo->isOptionProductDeliveryFee()) {
                                        if ($row['送料'] != '') {
                                            $deliveryFee = str_replace(',', '', $row['送料']);
                                            if (preg_match('/^\d+$/', $deliveryFee) && $deliveryFee >= 0) {
                                                $pc->setDeliveryFee($deliveryFee);
                                            } else {
                                                $this->addErrors(($data->key() + 1) . '行目の送料は0以上の数値を設定してください。');
                                            }
                                        }
                                    }

                                    $flag = true;
                                    break;
                                }
                            }

                            // 商品規格を登録
                            if (!$flag) {
                                $pc = $ProductClasses[0];
                                if ($pc->getClassCategory1() == null &&
                                    $pc->getClassCategory2() == null
                                ) {

                                    // 規格分類1、規格分類2がnullであるデータを非表示
                                    $pc->setVisible(false);
                                }

                                if ($row['規格分類1(ID)'] == $row['規格分類2(ID)']) {
                                    $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)と規格分類2(ID)には同じ値を使用できません。');
                                } else {

                                    // 必ず規格分類1がセットされている
                                    // 規格分類1、2をそれぞれセットし作成
                                    $ClassCategory1 = null;
                                    if (preg_match('/^\d+$/', $classCategoryId1)) {
                                        $ClassCategory1 = $this->classCategoryRepository->find($classCategoryId1);
                                        if (!$ClassCategory1) {
                                            $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)が存在しません。');
                                        }
                                    } else {
                                        $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)が存在しません。');
                                    }

                                    $ClassCategory2 = null;
                                    if ($row['規格分類2(ID)'] != '') {
                                        if ($pc->getClassCategory1() != null && $pc->getClassCategory2() == null) {
                                            $this->addErrors(($data->key() + 1) . '行目の規格分類2(ID)は設定できません。');
                                        } else {
                                            if (preg_match('/^\d+$/', $classCategoryId2)) {
                                                $ClassCategory2 = $this->classCategoryRepository->find($classCategoryId2);
                                                if (!$ClassCategory2) {
                                                    $this->addErrors(($data->key() + 1) . '行目の規格分類2(ID)が存在しません。');
                                                } else {
                                                    if ($ClassCategory1 &&
                                                        ($ClassCategory1->getClassName()->getId() == $ClassCategory2->getClassName()->getId())
                                                    ) {
                                                        $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)と規格分類2(ID)の規格名が同じです。');
                                                    }
                                                }
                                            } else {
                                                $this->addErrors(($data->key() + 1) . '行目の規格分類2(ID)が存在しません。');
                                            }

                                        }
                                    } else {
                                        if ($pc->getClassCategory1() != null && $pc->getClassCategory2() != null) {
                                            $this->addErrors(($data->key() + 1) . '行目の規格分類2(ID)に値を設定してください。');
                                        }
                                    }
                                    $ProductClass = $this->createProductClass($row, $Product, $app, $data, $ClassCategory1, $ClassCategory2);

                                    if ($this->BaseInfo->isOptionProductDeliveryFee()) {
                                        if ($row['送料'] != '') {
                                            $deliveryFee = str_replace(',', '', $row['送料']);
                                            if (preg_match('/^\d+$/', $deliveryFee) && $deliveryFee >= 0) {
                                                $ProductClass->setDeliveryFee($deliveryFee);
                                            } else {
                                                $this->addErrors(($data->key() + 1) . '行目の送料は0以上の数値を設定してください。');
                                            }
                                        }
                                    }

                                    $Product->addProductClass($ProductClass);
                                }

                            }

                        }


                        if ($this->hasErrors()) {
                            return $this->render($app, $form, $headers);
                        }

                        $this->em->persist($Product);

                    }

                    $this->em->flush();
                    $this->em->getConnection()->commit();

                    log_info('商品CSV登録完了');

                    $app->addSuccess('admin.product.csv_import.save.complete', 'admin');
                }

            }
        }

        return $this->render($app, $form, $headers);
    }

    /**
     * カテゴリ登録CSVアップロード
     *
     * @Route("/{_admin}/product/category_csv_upload", name="admin_product_category_csv_import")
     * @Template("Product/csv_category.twig")
     */
    public function csvCategory(Application $app, Request $request)
    {

        $form = $this->formFactory->createBuilder(CsvImportType::class)->getForm();

        $headers = $this->getCategoryCsvHeader();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $formFile = $form['import_file']->getData();

                if (!empty($formFile)) {

                    log_info('カテゴリCSV登録開始');

                    $data = $this->getImportData($app, $formFile);
                    if ($data === false) {
                        $this->addErrors('CSVのフォーマットが一致しません。');
                        return $this->render($app, $form, $headers);
                    }

                    $keys = array_keys($headers);
                    $columnHeaders = $data->getColumnHeaders();
                    if ($keys !== $columnHeaders) {
                        $this->addErrors('CSVのフォーマットが一致しません。');
                        return $this->render($app, $form, $headers);
                    }

                    $size = count($data);
                    if ($size < 1) {
                        $this->addErrors('CSVデータが存在しません。');
                        return $this->render($app, $form, $headers);
                    }

                    $headerSize = count($keys);

                    $this->em = $this->entityManager;
                    $this->em->getConfiguration()->setSQLLogger(null);

                    $this->em->getConnection()->beginTransaction();

                    // CSVファイルの登録処理
                    foreach ($data as $row) {

                        if ($headerSize != count($row)) {
                            $this->addErrors(($data->key() + 1) . '行目のCSVフォーマットが一致しません。');
                            return $this->render($app, $form, $headers);
                        }

                        if ($row['カテゴリID'] == '') {
                            $Category = new Category();
                        } else {
                            if (!preg_match('/^\d+$/', $row['カテゴリID'])) {
                                $this->addErrors(($data->key() + 1) . '行目のカテゴリIDが存在しません。');
                                return $this->render($app, $form, $headers);
                            }
                            $Category = $this->categoryRepository->find($row['カテゴリID']);
                            if (!$Category) {
                                $this->addErrors(($data->key() + 1) . '行目のカテゴリIDが存在しません。');
                                return $this->render($app, $form, $headers);
                            }
                            if ($row['カテゴリID'] == $row['親カテゴリID']) {
                                $this->addErrors(($data->key() + 1) . '行目のカテゴリIDと親カテゴリIDが同じです。');
                                return $this->render($app, $form, $headers);
                            }

                        }

                        if (StringUtil::isBlank($row['カテゴリ名'])) {
                            $this->addErrors(($data->key() + 1) . '行目のカテゴリ名が設定されていません。');
                            return $this->render($app, $form, $headers);
                        } else {
                            $Category->setName(StringUtil::trimAll($row['カテゴリ名']));
                        }

                        if ($row['親カテゴリID'] != '') {

                            if (!preg_match('/^\d+$/', $row['親カテゴリID'])) {
                                $this->addErrors(($data->key() + 1) . '行目の親カテゴリIDが存在しません。');
                                return $this->render($app, $form, $headers);
                            }

                            $ParentCategory = $this->categoryRepository->find($row['親カテゴリID']);
                            if (!$ParentCategory) {
                                $this->addErrors(($data->key() + 1) . '行目の親カテゴリIDが存在しません。');
                                return $this->render($app, $form, $headers);
                            }

                        } else {
                            $ParentCategory = null;
                        }

                        $Category->setParent($ParentCategory);
                        if ($ParentCategory) {
                            $Category->setHierarchy($ParentCategory->getHierarchy() + 1);
                        } else {
                            $Category->setHierarchy(1);
                        }

                        if ($this->appConfig['category_nest_level'] < $Category->getHierarchy()) {
                            $this->addErrors(($data->key() + 1) . '行目のカテゴリが最大レベルを超えているため設定できません。');
                            return $this->render($app, $form, $headers);
                        }

                        $this->categoryRepository->save($Category);

                        if ($this->hasErrors()) {
                            return $this->render($app, $form, $headers);
                        }

                        $this->em->persist($Category);

                    }

                    $this->em->flush();
                    $this->em->getConnection()->commit();

                    log_info('カテゴリCSV登録完了');

                    $app->addSuccess('admin.category.csv_import.save.complete', 'admin');
                }

            }
        }

        return $this->render($app, $form, $headers);
    }


    /**
     * アップロード用CSV雛形ファイルダウンロード
     *
     * @Route("/{_admin}/product/csv_template/{type}", requirements={"type" = "\w+"}, name="admin_product_csv_template")
     */
    public function csvTemplate(Application $app, Request $request, $type)
    {
        set_time_limit(0);

        $response = new StreamedResponse();

        if ($type == 'product') {
            $headers = $this->getProductCsvHeader();
            $filename = 'product.csv';
        } else if ($type == 'category') {
            $headers = $this->getCategoryCsvHeader();
            $filename = 'category.csv';
        } else {
            throw new NotFoundHttpException();
        }

        $response->setCallback(function () use ($app, $request, $headers) {

            // ヘッダ行の出力
            $row = array();
            foreach ($headers as $key => $value) {
                $row[] = mb_convert_encoding($key, $this->appConfig['csv_export_encoding'], 'UTF-8');
            }

            $fp = fopen('php://output', 'w');
            fputcsv($fp, $row, $this->appConfig['csv_export_separator']);
            fclose($fp);

        });

        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        $response->send();

        return $response;
    }


    /**
     * 登録、更新時のエラー画面表示
     *
     */
    protected function render($app, $form, $headers)
    {

        if ($this->hasErrors()) {
            if ($this->em) {
                $this->em->getConnection()->rollback();
            }
        }

        if (!empty($this->fileName)) {
            try {
                $fs = new Filesystem();
                $fs->remove($this->appConfig['csv_temp_realdir'] . '/' . $this->fileName);
            } catch (\Exception $e) {
                // エラーが発生しても無視する
            }
        }

        return [
            'form' => $form->createView(),
            'headers' => $headers,
            'errors' => $this->errors,
        ];
    }


    /**
     * アップロードされたCSVファイルの行ごとの処理
     *
     * @param $formFile
     * @return CsvImportService
     */
    protected function getImportData($app, $formFile)
    {
        // アップロードされたCSVファイルを一時ディレクトリに保存
        $this->fileName = 'upload_' . StringUtil::random() . '.' . $formFile->getClientOriginalExtension();
        $formFile->move($this->appConfig['csv_temp_realdir'], $this->fileName);

        $file = file_get_contents($this->appConfig['csv_temp_realdir'] . '/' . $this->fileName);

        if ('\\' === DIRECTORY_SEPARATOR && PHP_VERSION_ID >= 70000) {
            // Windows 環境の PHP7 の場合はファイルエンコーディングを CP932 に合わせる
            // see https://github.com/EC-CUBE/ec-cube/issues/1780
            setlocale(LC_ALL, ''); // 既定のロケールに設定
            if (mb_detect_encoding($file) === 'UTF-8') { // UTF-8 を検出したら SJIS-win に変換
                $file = mb_convert_encoding($file, 'SJIS-win', 'UTF-8');
            }
        } else {
            // アップロードされたファイルがUTF-8以外は文字コード変換を行う
            $encode = StringUtil::characterEncoding(substr($file, 0, 6));
            if ($encode != 'UTF-8') {
                $file = mb_convert_encoding($file, 'UTF-8', $encode);
            }
        }
        $file = StringUtil::convertLineFeed($file);

        $tmp = tmpfile();
        fwrite($tmp, $file);
        rewind($tmp);
        $meta = stream_get_meta_data($tmp);
        $file = new \SplFileObject($meta['uri']);

        set_time_limit(0);

        // アップロードされたCSVファイルを行ごとに取得
        $data = new CsvImportService($file, $this->appConfig['csv_import_delimiter'], $this->appConfig['csv_import_enclosure']);

        $ret = $data->setHeaderRowNumber(0);

        return ($ret !== false) ? $data : false;
    }


    /**
     * 商品画像の削除、登録
     */
    protected function createProductImage($row, Product $Product)
    {
        if ($row['商品画像'] != '') {

            // 画像の削除
            $ProductImages = $Product->getProductImage();
            foreach ($ProductImages as $ProductImage) {
                $Product->removeProductImage($ProductImage);
                $this->em->remove($ProductImage);
            }

            // 画像の登録
            $images = explode(',', $row['商品画像']);
            $sortNo = 1;
            foreach ($images as $image) {

                $ProductImage = new ProductImage();
                $ProductImage->setFileName(StringUtil::trimAll($image));
                $ProductImage->setProduct($Product);
                $ProductImage->setSortNo($sortNo);

                $Product->addProductImage($ProductImage);
                $sortNo++;
                $this->em->persist($ProductImage);
            }
        }
    }


    /**
     * 商品カテゴリの削除、登録
     */
    protected function createProductCategory($row, Product $Product, $app, $data)
    {
        // カテゴリの削除
        $ProductCategories = $Product->getProductCategories();
        foreach ($ProductCategories as $ProductCategory) {
            $Product->removeProductCategory($ProductCategory);
            $this->em->remove($ProductCategory);
            $this->em->flush($ProductCategory);
        }

        if ($row['商品カテゴリ(ID)'] == '') {
            // 入力されていなければ削除のみ
            return;
        }

        // カテゴリの登録
        $categories = explode(',', $row['商品カテゴリ(ID)']);
        $sortNo = 1;
        $categoriesIdList = array();
        foreach ($categories as $category) {

            if (preg_match('/^\d+$/', $category)) {
                $Category = $this->categoryRepository->find($category);
                if (!$Category) {
                    $this->addErrors(($data->key() + 1).'行目の商品カテゴリ(ID)「'.$category.'」が存在しません。');
                } else {
                    foreach($Category->getPath() as $ParentCategory){
                        if (!isset($categoriesIdList[$ParentCategory->getId()])){
                            $ProductCategory = $this->makeProductCategory($Product, $ParentCategory, $sortNo);
                            $this->entityManager->persist($ProductCategory);
                            $sortNo++;
                            $Product->addProductCategory($ProductCategory);
                            $categoriesIdList[$ParentCategory->getId()] = true;
                        }
                    }
                    if (!isset($categoriesIdList[$Category->getId()])){
                        $ProductCategory = $this->makeProductCategory($Product, $Category, $sortNo);
                        $sortNo++;
                        $this->em->persist($ProductCategory);
                        $Product->addProductCategory($ProductCategory);
                        $categoriesIdList[$Category->getId()] = true;
                    }
                }
            } else {
                $this->addErrors(($data->key() + 1).'行目の商品カテゴリ(ID)「'.$category.'」が存在しません。');
            }
        }

    }


    /**
     * タグの登録
     *
     * @param array $row
     * @param Product $Product
     * @param Application $app
     * @param CsvImportService $data
     */
    protected function createProductTag($row, Product $Product, $app, $data)
    {
        // タグの削除
        $ProductTags = $Product->getProductTag();
        foreach ($ProductTags as $ProductTags) {
            $Product->removeProductTag($ProductTags);
            $this->em->remove($ProductTags);
        }

        if ($row['タグ(ID)'] == '') {
            return;
        }

        // タグの登録
        $tags = explode(',', $row['タグ(ID)']);
        foreach ($tags as $tag_id) {
            $Tag = null;
            if (preg_match('/^\d+$/', $tag_id)) {
                $Tag = $this->tagRepository->find($tag_id);
                if ($Tag) {
                    $ProductTags = new ProductTag();
                    $ProductTags
                        ->setProduct($Product)
                        ->setTag($Tag);

                    $Product->addProductTag($ProductTags);

                    $this->em->persist($ProductTags);
                }
            }
            if (!$Tag) {
                $this->addErrors(($data->key() + 1) . '行目のタグ(ID)「' . $tag_id . '」が存在しません。');
            }
        }
    }


    /**
     * 商品規格分類1、商品規格分類2がnullとなる商品規格情報を作成
     */
    protected function createProductClass($row, Product $Product, $app, $data, $ClassCategory1 = null, $ClassCategory2 = null)
    {
        // 規格分類1、規格分類2がnullとなる商品を作成

        $ProductClass = new ProductClass();
        $ProductClass->setProduct($Product);
        $ProductClass->setVisible(true);


        if ($row['販売種別(ID)'] == '') {
            $this->addErrors(($data->key() + 1) . '行目の販売種別(ID)が設定されていません。');
        } else {
            if (preg_match('/^\d+$/', $row['販売種別(ID)'])) {
                $SaleType = $this->saleTypeRepository->find($row['販売種別(ID)']);
                if (!$SaleType) {
                    $this->addErrors(($data->key() + 1) . '行目の販売種別(ID)が存在しません。');
                } else {
                    $ProductClass->setSaleType($SaleType);
                }
            } else {
                $this->addErrors(($data->key() + 1) . '行目の販売種別(ID)が存在しません。');
            }
        }

        $ProductClass->setClassCategory1($ClassCategory1);
        $ProductClass->setClassCategory2($ClassCategory2);

        if ($row['発送日目安(ID)'] != '') {
            if (preg_match('/^\d+$/', $row['発送日目安(ID)'])) {
                $DeliveryDuration = $this->deliveryDurationRepository->find($row['発送日目安(ID)']);
                if (!$DeliveryDuration) {
                    $this->addErrors(($data->key() + 1) . '行目の発送日目安(ID)が存在しません。');
                } else {
                    $ProductClass->setDeliveryDuration($DeliveryDuration);
                }
            } else {
                $this->addErrors(($data->key() + 1) . '行目の発送日目安(ID)が存在しません。');
            }
        }

        if (StringUtil::isNotBlank($row['商品コード'])) {
            $ProductClass->setCode(StringUtil::trimAll($row['商品コード']));
        } else {
            $ProductClass->setCode(null);
        }

        if ($row['在庫数無制限フラグ'] == '') {
            $this->addErrors(($data->key() + 1) . '行目の在庫数無制限フラグが設定されていません。');
        } else {
            if ($row['在庫数無制限フラグ'] == (string) Constant::DISABLED) {
                $ProductClass->setStockUnlimited(false);
                // 在庫数が設定されていなければエラー
                if ($row['在庫数'] == '') {
                    $this->addErrors(($data->key() + 1) . '行目の在庫数が設定されていません。');
                } else {
                    $stock = str_replace(',', '', $row['在庫数']);
                    if (preg_match('/^\d+$/', $stock) && $stock >= 0) {
                        $ProductClass->setStock($stock);
                    } else {
                        $this->addErrors(($data->key() + 1) . '行目の在庫数は0以上の数値を設定してください。');
                    }
                }

            } else if ($row['在庫数無制限フラグ'] == (string) Constant::ENABLED) {
                $ProductClass->setStockUnlimited(true);
                $ProductClass->setStock(null);
            } else {
                $this->addErrors(($data->key() + 1) . '行目の在庫数無制限フラグが設定されていません。');
            }
        }

        if ($row['販売制限数'] != '') {
            $saleLimit = str_replace(',', '', $row['販売制限数']);
            if (preg_match('/^\d+$/', $saleLimit) && $saleLimit >= 0) {
                $ProductClass->setSaleLimit($saleLimit);
            } else {
                $this->addErrors(($data->key() + 1) . '行目の販売制限数は0以上の数値を設定してください。');
            }
        }

        if ($row['通常価格'] != '') {
            $price01 = str_replace(',', '', $row['通常価格']);
            if (preg_match('/^\d+$/', $price01) && $price01 >= 0) {
                $ProductClass->setPrice01($price01);
            } else {
                $this->addErrors(($data->key() + 1) . '行目の通常価格は0以上の数値を設定してください。');
            }
        }

        if ($row['販売価格'] == '') {
            $this->addErrors(($data->key() + 1) . '行目の販売価格が設定されていません。');
        } else {
            $price02 = str_replace(',', '', $row['販売価格']);
            if (preg_match('/^\d+$/', $price02) && $price02 >= 0) {
                $ProductClass->setPrice02($price02);
            } else {
                $this->addErrors(($data->key() + 1) . '行目の販売価格は0以上の数値を設定してください。');
            }
        }

        if ($row['送料'] != '') {
            $delivery_fee = str_replace(',', '', $row['送料']);
            if (preg_match('/^\d+$/', $delivery_fee) && $delivery_fee >= 0) {
                $ProductClass->setDeliveryFee($delivery_fee);
            } else {
                $this->addErrors(($data->key() + 1) . '行目の送料は0以上の数値を設定してください。');
            }
        }

        $Product->addProductClass($ProductClass);
        $ProductStock = new ProductStock();
        $ProductClass->setProductStock($ProductStock);
        $ProductStock->setProductClass($ProductClass);

        if (!$ProductClass->isStockUnlimited()) {
            $ProductStock->setStock($ProductClass->getStock());
        } else {
            // 在庫無制限時はnullを設定
            $ProductStock->setStock(null);
        }

        $this->em->persist($ProductClass);
        $this->em->persist($ProductStock);

        return $ProductClass;

    }


    /**
     * 商品規格情報を更新
     */
    protected function updateProductClass($row, Product $Product, ProductClass $ProductClass, $app, $data)
    {

        $ProductClass->setProduct($Product);

        if ($row['販売種別(ID)'] == '') {
            $this->addErrors(($data->key() + 1) . '行目の販売種別(ID)が設定されていません。');
        } else {
            if (preg_match('/^\d+$/', $row['販売種別(ID)'])) {
                $SaleType = $this->saleTypeRepository->find($row['販売種別(ID)']);
                if (!$SaleType) {
                    $this->addErrors(($data->key() + 1) . '行目の販売種別(ID)が存在しません。');
                } else {
                    $ProductClass->setSaleType($SaleType);
                }
            } else {
                $this->addErrors(($data->key() + 1) . '行目の販売種別(ID)が存在しません。');
            }
        }

        // 規格分類1、2をそれぞれセットし作成
        if ($row['規格分類1(ID)'] != '') {
            if (preg_match('/^\d+$/', $row['規格分類1(ID)'])) {
                $ClassCategory = $this->classCategoryRepository->find($row['規格分類1(ID)']);
                if (!$ClassCategory) {
                    $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)が存在しません。');
                } else {
                    $ProductClass->setClassCategory1($ClassCategory);
                }
            } else {
                $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)が存在しません。');
            }
        }

        if ($row['規格分類2(ID)'] != '') {
            if (preg_match('/^\d+$/', $row['規格分類2(ID)'])) {
                $ClassCategory = $this->classCategoryRepository->find($row['規格分類2(ID)']);
                if (!$ClassCategory) {
                    $this->addErrors(($data->key() + 1) . '行目の規格分類2(ID)が存在しません。');
                } else {
                    $ProductClass->setClassCategory2($ClassCategory);
                }
            } else {
                $this->addErrors(($data->key() + 1) . '行目の規格分類2(ID)が存在しません。');
            }
        }

        if ($row['発送日目安(ID)'] != '') {
            if (preg_match('/^\d+$/', $row['発送日目安(ID)'])) {
                $DeliveryDuration = $this->deliveryDurationRepository->find($row['発送日目安(ID)']);
                if (!$DeliveryDuration) {
                    $this->addErrors(($data->key() + 1) . '行目の発送日目安(ID)が存在しません。');
                } else {
                    $ProductClass->setDeliveryDuration($DeliveryDuration);
                }
            } else {
                $this->addErrors(($data->key() + 1) . '行目の発送日目安(ID)が存在しません。');
            }
        }

        if (StringUtil::isNotBlank($row['商品コード'])) {
            $ProductClass->setCode(StringUtil::trimAll($row['商品コード']));
        } else {
            $ProductClass->setCode(null);
        }

        if ($row['在庫数無制限フラグ'] == '') {
            $this->addErrors(($data->key() + 1) . '行目の在庫数無制限フラグが設定されていません。');
        } else {
            if ($row['在庫数無制限フラグ'] == (string) Constant::DISABLED) {
                $ProductClass->setStockUnlimited(false);
                // 在庫数が設定されていなければエラー
                if ($row['在庫数'] == '') {
                    $this->addErrors(($data->key() + 1) . '行目の在庫数が設定されていません。');
                } else {
                    $stock = str_replace(',', '', $row['在庫数']);
                    if (preg_match('/^\d+$/', $stock) && $stock >= 0) {
                        $ProductClass->setStock($row['在庫数']);
                    } else {
                        $this->addErrors(($data->key() + 1) . '行目の在庫数は0以上の数値を設定してください。');
                    }
                }

            } else if ($row['在庫数無制限フラグ'] == (string) Constant::ENABLED) {
                $ProductClass->setStockUnlimited(true);
                $ProductClass->setStock(null);
            } else {
                $this->addErrors(($data->key() + 1) . '行目の在庫数無制限フラグが設定されていません。');
            }
        }

        if ($row['販売制限数'] != '') {
            $saleLimit = str_replace(',', '', $row['販売制限数']);
            if (preg_match('/^\d+$/', $saleLimit) && $saleLimit >= 0) {
                $ProductClass->setSaleLimit($saleLimit);
            } else {
                $this->addErrors(($data->key() + 1) . '行目の販売制限数は0以上の数値を設定してください。');
            }
        }

        if ($row['通常価格'] != '') {
            $price01 = str_replace(',', '', $row['通常価格']);
            if (preg_match('/^\d+$/', $price01) && $price01 >= 0) {
                $ProductClass->setPrice01($price01);
            } else {
                $this->addErrors(($data->key() + 1) . '行目の通常価格は0以上の数値を設定してください。');
            }
        }

        if ($row['販売価格'] == '') {
            $this->addErrors(($data->key() + 1) . '行目の販売価格が設定されていません。');
        } else {
            $price02 = str_replace(',', '', $row['販売価格']);
            if (preg_match('/^\d+$/', $price02) && $price02 >= 0) {
                $ProductClass->setPrice02($price02);
            } else {
                $this->addErrors(($data->key() + 1) . '行目の販売価格は0以上の数値を設定してください。');
            }
        }

        $ProductStock = $ProductClass->getProductStock();

        if (!$ProductClass->isStockUnlimited()) {
            $ProductStock->setStock($ProductClass->getStock());
        } else {
            // 在庫無制限時はnullを設定
            $ProductStock->setStock(null);
        }

        return $ProductClass;
    }

    /**
     * 登録、更新時のエラー画面表示
     *
     */
    protected function addErrors($message)
    {
        $e = new CsvImportException($message);
        $this->errors[] = $e;
    }

    /**
     * @return array
     */
    protected function getErrors()
    {
        return $this->errors;
    }

    /**
     *
     * @return boolean
     */
    protected function hasErrors()
    {
        return count($this->getErrors()) > 0;
    }

    /**
     * 商品登録CSVヘッダー定義
     */
    private function getProductCsvHeader()
    {
        return array(
            '商品ID' => 'id',
            '公開ステータス(ID)' => 'status',
            '商品名' => 'name',
            'ショップ用メモ欄' => 'note',
            '商品説明(一覧)' => 'description_list',
            '商品説明(詳細)' => 'description_detail',
            '検索ワード' => 'search_word',
            'フリーエリア' => 'free_area',
            '商品削除フラグ' => 'product_del_flg',
            '商品画像' => 'product_image',
            '商品カテゴリ(ID)' => 'product_category',
            'タグ(ID)' => 'product_tag',
            '販売種別(ID)' => 'sale_type',
            '規格分類1(ID)' => 'class_category1',
            '規格分類2(ID)' => 'class_category2',
            '発送日目安(ID)' => 'deliveryFee',
            '商品コード' => 'product_code',
            '在庫数' => 'stock',
            '在庫数無制限フラグ' => 'stock_unlimited',
            '販売制限数' => 'sale_limit',
            '通常価格' => 'price01',
            '販売価格' => 'price02',
            '送料' => 'delivery_fee',
        );
    }


    /**
     * カテゴリCSVヘッダー定義
     */
    private function getCategoryCsvHeader()
    {
        return array(
            'カテゴリID' => 'id',
            'カテゴリ名' => 'category_name',
            '親カテゴリID' => 'parent_category_id',
        );
    }
    
        /**
     * ProductCategory作成
     * @param \Eccube\Entity\Product $Product
     * @param \Eccube\Entity\Category $Category
     * @return ProductCategory
     */
    private function makeProductCategory($Product, $Category, $sortNo)
    {
        $ProductCategory = new ProductCategory();
        $ProductCategory->setProduct($Product);
        $ProductCategory->setProductId($Product->getId());
        $ProductCategory->setCategory($Category);
        $ProductCategory->setCategoryId($Category->getId());
        $ProductCategory->setSortNo($sortNo);
        
        return $ProductCategory;
    }
}
