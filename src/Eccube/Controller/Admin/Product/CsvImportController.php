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
use Eccube\Entity\Product;
use Eccube\Entity\ProductCategory;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductStock;
use Eccube\Exception\CsvImportException;
use Eccube\Service\CsvImportService;
use Eccube\Util\Str;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;


class CsvImportController
{

    private $errors = array();

    private $fileName;

    private $em;


    /**
     * 商品登録CSVアップロード
     */
    public function csvProduct(Application $app, Request $request)
    {

        $builder = $app['form.factory']->createBuilder('admin_csv_import');

        $headers = $this->getProductCsvHeader();
        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $formFile = $form['import_file']->getData();

                if (!empty($formFile)) {

                    // アップロードされたCSVファイルを一時ディレクトリに保存
                    $this->fileName = 'upload_' . Str::random() . '.' . $formFile->guessExtension();
                    $formFile->move($app['config']['csv_temp_realdir'], $this->fileName);

                    $file = file_get_contents($app['config']['csv_temp_realdir'] . '/' . $this->fileName);
                    // アップロードされたファイルがUTF-8以外は文字コード変換を行う
                    $encode = Str::characterEncoding(substr($file, 0, 6));
                    if ($encode != 'UTF-8') {
                        $file = mb_convert_encoding($file, 'UTF-8', $encode);
                    }
                    $file = Str::convertLineFeed($file);

                    $tmp = tmpfile();
                    fwrite($tmp, $file);
                    rewind($tmp);
                    $meta = stream_get_meta_data($tmp);
                    $file = new \SplFileObject($meta['uri']);

                    set_time_limit(0);

                    // $data = new CsvImportService($app, $file, $app['config']['csv_delimiter'], $app['config']['csv_enclosure'], $app['config']['csv_escape']);
                    $data = new CsvImportService($file, $app['config']['csv_delimiter'], $app['config']['csv_enclosure']);

                    $data->setHeaderRowNumber(0);

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

                    $this->em = $app['orm.em'];
                    $this->em->getConfiguration()->setSQLLogger(null);

                    $this->em->getConnection()->beginTransaction();

                    // CSVファイルの登録処理
                    foreach ($data as $row) {

                        if ($headerSize != count($row)) {
                            $this->addErrors(($data->key() + 1) . '行目のCSVフォーマットが一致しません。');
                            return $this->render($app, $form, $headers);
                        }

                        if (empty($row['商品ID'])) {
                            $Product = new Product();
                            //    $this->em->persist($Product);
                        } else {
                            $Product = $app['eccube.repository.product']->find($row['商品ID']);
                            if (!$Product) {
                                $this->addErrors(($data->key() + 1) . '行目の商品IDが存在しません。');
                                return $this->render($app, $form, $headers);
                            }
                        }

                        if (empty($row['公開ステータス(ID)'])) {
                            $this->addErrors(($data->key() + 1) . '行目の公開ステータス(ID)が設定されていません。');
                        } else {
                            $Disp = $app['eccube.repository.master.disp']->find($row['公開ステータス(ID)']);
                            if (!$Disp) {
                                $this->addErrors(($data->key() + 1) . '行目の公開ステータス(ID)が存在しません。');
                            } else {
                                $Product->setStatus($Disp);
                            }
                        }

                        if (empty($row['商品名'])) {
                            $this->addErrors(($data->key() + 1) . '行目の商品名が設定されていません。');
                        } else {
                            $Product->setName($row['商品名']);
                        }

                        if (!empty($row['ショップ用メモ欄'])) {
                            $Product->setNote($row['ショップ用メモ欄']);
                        }
                        if (!empty($row['商品説明(一覧)'])) {
                            $Product->setDescriptionList($row['商品説明(一覧)']);
                        }
                        if (!empty($row['商品説明(詳細)'])) {
                            $Product->setDescriptionDetail($row['商品説明(詳細)']);
                        }
                        if (!empty($row['検索ワード'])) {
                            $Product->setSearchWord($row['検索ワード']);
                        }
                        if (!empty($row['フリーエリア'])) {
                            $Product->setFreeArea($row['フリーエリア']);
                        }
                        if (empty($row['商品削除フラグ'])) {
                            $Product->setDelFlg(Constant::DISABLED);
                        } else {
                            if ($row['商品削除フラグ'] == Constant::DISABLED || $row['商品削除フラグ'] == Constant::ENABLED) {
                                $Product->setDelFlg($row['商品削除フラグ']);
                            } else {
                                $this->addErrors(($data->key() + 1) . '行目の商品削除フラグが設定されていません。');
                            }
                        }

                        // 商品画像登録
                        $this->createProductImage($row, $Product);

                        // 商品カテゴリ登録
                        // $this->createProductCategory($row, $Product, $app, $data);


                        // 商品規格が存在しなければ新規登録
                        $ProductClasses = $Product->getProductClasses();
                        if ($ProductClasses->count() < 1) {
                            // 規格分類1(ID)がセットされていると規格なし商品、規格あり商品を作成
                            $ProductClassOrg = $this->createProductClass($row, $Product, $app, $data);

                            if (!empty($row['規格分類1(ID)'])) {

                                if ($row['規格分類1(ID)'] == $row['規格分類2(ID)']) {
                                    $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)と規格分類2(ID)には同じ値を使用できません。');
                                } else {
                                    // 商品規格あり
                                    // 企画分類あり商品を作成
                                    $ProductClass = clone $ProductClassOrg;
                                    $ProductStock = clone $ProductClassOrg->getProductStock();

                                    // 規格分類1、規格分類2がnullであるデータの削除フラグを1にセット
                                    $ProductClassOrg->setDelFlg(Constant::ENABLED);

                                    // 規格分類1、2をそれぞれセットし作成
                                    $ClassCategory1 = $app['eccube.repository.class_category']->find($row['規格分類1(ID)']);
                                    if (!$ClassCategory1) {
                                        $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)が存在しません。');
                                    } else {
                                        $ProductClass->setClassCategory1($ClassCategory1);
                                    }

                                    if (!empty($row['規格分類2(ID)'])) {
                                        $ClassCategory2 = $app['eccube.repository.class_category']->find($row['規格分類2(ID)']);
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
                                    }
                                    $ProductClass->setProductStock($ProductStock);
                                    $ProductStock->setProductClass($ProductClass);

                                    $this->em->persist($ProductClass);
                                    $this->em->persist($ProductStock);
                                }

                            } else {
                                if (!empty($row['規格分類2(ID)'])) {
                                    $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)が存在しません。');
                                }
                            }

                        } else {
                            // 商品規格の更新

                            $flag = false;
                            $classCategoryId1 = empty($row['規格分類1(ID)']) ? null : $row['規格分類1(ID)'];
                            $classCategoryId2 = empty($row['規格分類2(ID)']) ? null : $row['規格分類2(ID)'];

                            foreach ($ProductClasses as $pc) {

                                $classCategory1 = is_null($pc->getClassCategory1()) ? null : $pc->getClassCategory1()->getId();
                                $classCategory2 = is_null($pc->getClassCategory2()) ? null : $pc->getClassCategory2()->getId();

                                // 登録されている商品規格を更新
                                if ($classCategory1 == $classCategoryId1 &&
                                    $classCategory2 == $classCategoryId2
                                ) {
                                    $this->updateProductClass($row, $Product, $pc, $app, $data);
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

                                    // 規格分類1、規格分類2がnullであるデータの削除フラグを1にセット
                                    $pc->setDelFlg(Constant::ENABLED);
                                }

                                if ($row['規格分類1(ID)'] == $row['規格分類2(ID)']) {
                                    $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)と規格分類2(ID)には同じ値を使用できません。');
                                } else {

                                    // 必ず規格分類1がセットされている
                                    // 規格分類1、2をそれぞれセットし作成
                                    $ClassCategory1 = $app['eccube.repository.class_category']->find($classCategoryId1);
                                    if (!$ClassCategory1) {
                                        $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)が存在しません。');
                                    }

                                    $ClassCategory2 = null;
                                    if (!empty($row['規格分類2(ID)'])) {
                                        if ($pc->getClassCategory1() != null && $pc->getClassCategory2() == null) {
                                            $this->addErrors(($data->key() + 1) . '行目の規格分類2(ID)は設定できません。');
                                        } else {
                                            $ClassCategory2 = $app['eccube.repository.class_category']->find($classCategoryId2);
                                            if (!$ClassCategory2) {
                                                $this->addErrors(($data->key() + 1) . '行目の規格分類2(ID)が存在しません。');
                                            } else {
                                                if ($ClassCategory1 &&
                                                    ($ClassCategory1->getClassName()->getId() == $ClassCategory2->getClassName()->getId())
                                                ) {
                                                    $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)と規格分類2(ID)の規格名が同じです。');
                                                }
                                            }

                                        }
                                    } else {
                                        if ($pc->getClassCategory1() != null && $pc->getClassCategory2() != null) {
                                            $this->addErrors(($data->key() + 1) . '行目の規格分類2(ID)に値を設定してください。');
                                        }
                                    }
                                    $ProductClass = $this->createProductClass($row, $Product, $app, $data, $ClassCategory1, $ClassCategory2);

                                    $Product->addProductClass($ProductClass);
                                }

                            }

                        }


                        if ($this->hasErrors()) {
                            return $this->render($app, $form, $headers);
                        }

                        $this->em->persist($Product);
                        $this->em->detach($Product);
                        $this->em->flush();
                        $this->em->clear();

                    }

                    $this->em->getConnection()->commit();
                    $this->em->flush();
                    $this->em->close();

                    $app->addSuccess('admin.product.csv_upload.save.complete', 'admin');
                }

            }
        }

        return $this->render($app, $form, $headers);
    }


    /**
     * アップロード用CSV雛形ファイルダウンロード
     */
    public function csvTemplate(Application $app, Request $request)
    {
        set_time_limit(0);

        $response = new StreamedResponse();
        $headers = $this->getProductCsvHeader();

        $response->setCallback(function () use ($app, $request, $headers) {
            // ヘッダ行の出力
            // $app['eccube.service.csv.import']->exportHeader();

            $row = array();
            foreach ($headers as $key => $value) {
                $row[] = mb_convert_encoding($key, $app['config']['csv_export_encoding'], 'UTF-8');
            }

            $fp = fopen('php://output', 'w');
            fputcsv($fp, $row, $app['config']['csv_export_separator']);
            fclose($fp);

        });

        $filename = 'product.csv';
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
            $fs = new Filesystem();
            $fs->remove($app['config']['csv_temp_realdir'] . '/' . $this->fileName);
            $this->em->getConnection()->rollback();
            $this->em->close();
        }

        return $app->render('Product/csv_product.twig', array(
            'form' => $form->createView(),
            'headers' => $headers,
            'errors' => $this->errors,
        ));
    }


    /**
     * 商品画像の削除、登録
     */
    protected function createProductImage($row, Product $Product)
    {
        if (!empty($row['商品画像'])) {

            // 画像の削除
            $ProductImages = $Product->getProductImage();
            foreach ($ProductImages as $ProductImage) {
                $Product->removeProductImage($ProductImage);
                $this->em->remove($ProductImage);
            }

            // 画像の登録
            $images = explode(',', $row['商品画像']);
            $rank = 1;
            foreach ($images as $image) {

                $ProductImage = new ProductImage();
                $ProductImage->setFileName($image);
                $ProductImage->setProduct($Product);
                $ProductImage->setRank($rank);

                $Product->addProductImage($ProductImage);
                $rank++;
                $this->em->persist($ProductImage);
            }
        }
    }


    /**
     * 商品カテゴリの削除、登録
     */
    protected function createProductCategory($row, Product $Product, $app, $data)
    {
        if (!empty($row['商品カテゴリ(ID)'])) {
            // カテゴリの削除
            $ProductCategories = $Product->getProductCategories();
            foreach ($ProductCategories as $ProductCategory) {
                $Product->removeProductCategory($ProductCategory);
                $this->em->remove($ProductCategory);
                $this->em->flush($ProductCategory);
            }

            // カテゴリの登録
            $categories = explode(',', $row['商品カテゴリ(ID)']);
            $rank = 1;
            foreach ($categories as $category) {

                $Category = $app['eccube.repository.category']->find($category);
                if (!$Category) {
                    $this->addErrors(($data->key() + 1) . '行目の商品カテゴリ(ID)「' . $category . '」が存在しません。');
                } else {
                    $ProductCategory = new ProductCategory();
                    $ProductCategory->setProductId($Product->getId());
                    $ProductCategory->setCategoryId($Category->getId());
                    $ProductCategory->setProduct($Product);
                    $ProductCategory->setCategory($Category);
                    $ProductCategory->setRank($rank);
                    $Product->addProductCategory($ProductCategory);
                    $rank++;
                    $this->em->persist($ProductCategory);
                }
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


        if (empty($row['商品種別(ID)'])) {
            $this->addErrors(($data->key() + 1) . '行目の商品種別(ID)が設定されていません。');
        } else {
            $ProductType = $app['eccube.repository.master.product_type']->find($row['商品種別(ID)']);
            if (!$ProductType) {
                $this->addErrors(($data->key() + 1) . '行目の商品種別(ID)が存在しません。');
            } else {
                $ProductClass->setProductType($ProductType);
            }
        }

        $ProductClass->setClassCategory1($ClassCategory1);
        $ProductClass->setClassCategory2($ClassCategory2);

        if (!empty($row['発送日目安(ID)'])) {
            $DeliveryDate = $app['eccube.repository.delivery_date']->find($row['発送日目安(ID)']);
            if (!$DeliveryDate) {
                $this->addErrors(($data->key() + 1) . '行目の発送日目安(ID)が存在しません。');
            } else {
                $ProductClass->setDeliveryDate($DeliveryDate);
            }
        }

        if (empty($row['商品コード'])) {
            $this->addErrors(($data->key() + 1) . '行目の商品コードが設定されていません。');
        } else {
            $ProductClass->setCode($row['商品コード']);
        }

        if (empty($row['在庫数無制限フラグ'])) {
            $this->addErrors(($data->key() + 1) . '行目の在庫数無制限フラグが設定されていません。');
        } else {
            if ($row['在庫数無制限フラグ'] == Constant::DISABLED) {
                $ProductClass->setStockUnlimited(Constant::DISABLED);
                // 在庫数が設定されていなければエラー
                if (empty($row['在庫数'])) {
                    $this->addErrors(($data->key() + 1) . '行目の在庫数が設定されていません。');
                } else {
                    $ProductClass->setStock($row['在庫数']);
                }

            } else if ($row['在庫数無制限フラグ'] == Constant::ENABLED) {
                $ProductClass->setStockUnlimited(Constant::ENABLED);
                $ProductClass->setStock(null);
            } else {
                $this->addErrors(($data->key() + 1) . '行目の在庫数無制限フラグが設定されていません。');
            }
        }

        if (!empty($row['販売数制限数'])) {
            $ProductClass->setSaleLimit($row['販売数制限数']);
        }

        if (!empty($row['通常価格'])) {
            $ProductClass->setPrice01($row['通常価格']);
        }

        if (empty($row['販売価格'])) {
            $this->addErrors(($data->key() + 1) . '行目の販売価格が設定されていません。');
        } else {
            $ProductClass->setPrice02($row['販売価格']);
        }
        if (empty($row['商品規格削除フラグ'])) {
            $ProductClass->setDelFlg(Constant::DISABLED);
        } else {
            if ($row['商品規格削除フラグ'] == Constant::DISABLED || $row['商品規格削除フラグ'] == Constant::ENABLED) {
                $ProductClass->setDelFlg($row['商品規格削除フラグ']);
            } else {
                $this->addErrors(($data->key() + 1) . '行目の商品規格削除フラグが設定されていません。');
            }
        }

        $Product->addProductClass($ProductClass);
        $ProductStock = new ProductStock();
        $ProductClass->setProductStock($ProductStock);
        $ProductStock->setProductClass($ProductClass);

        if (!$ProductClass->getStockUnlimited()) {
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

        if (empty($row['商品種別(ID)'])) {
            $this->addErrors(($data->key() + 1) . '行目の商品種別(ID)が設定されていません。');
        } else {
            $ProductType = $app['eccube.repository.master.product_type']->find($row['商品種別(ID)']);
            if (!$ProductType) {
                $this->addErrors(($data->key() + 1) . '行目の商品種別(ID)が存在しません。');
            } else {
                $ProductClass->setProductType($ProductType);
            }
        }

        // 規格分類1、2をそれぞれセットし作成
        if (!empty($row['規格分類1(ID)'])) {
            $ClassCategory = $app['eccube.repository.class_category']->find($row['規格分類1(ID)']);
            if (!$ClassCategory) {
                $this->addErrors(($data->key() + 1) . '行目の規格分類1(ID)が存在しません。');
            } else {
                $ProductClass->setClassCategory1($ClassCategory);
            }
        }

        if (!empty($row['規格分類2(ID)'])) {
            $ClassCategory = $app['eccube.repository.class_category']->find($row['規格分類2(ID)']);
            if (!$ClassCategory) {
                $this->addErrors(($data->key() + 1) . '行目の規格分類2(ID)が存在しません。');
            } else {
                $ProductClass->setClassCategory2($ClassCategory);
            }
        }

        if (!empty($row['発送日目安(ID)'])) {
            $DeliveryDate = $app['eccube.repository.delivery_date']->find($row['発送日目安(ID)']);
            if (!$DeliveryDate) {
                $this->addErrors(($data->key() + 1) . '行目の発送日目安(ID)が存在しません。');
            } else {
                $ProductClass->setDeliveryDate($DeliveryDate);
            }
        }

        if (empty($row['商品コード'])) {
            $this->addErrors(($data->key() + 1) . '行目の商品コードが設定されていません。');
        } else {
            $ProductClass->setCode($row['商品コード']);
        }

        if (empty($row['在庫数無制限フラグ'])) {
            $this->addErrors(($data->key() + 1) . '行目の在庫数無制限フラグが設定されていません。');
        } else {
            if ($row['在庫数無制限フラグ'] == Constant::DISABLED) {
                $ProductClass->setStockUnlimited(Constant::DISABLED);
                // 在庫数が設定されていなければエラー
                if (empty($row['在庫数'])) {
                    $this->addErrors(($data->key() + 1) . '行目の在庫数が設定されていません。');
                } else {
                    $ProductClass->setStock($row['在庫数']);
                }

            } else if ($row['在庫数無制限フラグ'] == Constant::ENABLED) {
                $ProductClass->setStockUnlimited(Constant::ENABLED);
                $ProductClass->setStock(null);
            } else {
                $this->addErrors(($data->key() + 1) . '行目の在庫数無制限フラグが設定されていません。');
            }
        }

        if (!empty($row['販売数制限数'])) {
            $ProductClass->setSaleLimit($row['販売数制限数']);
        }

        if (!empty($row['通常価格'])) {
            $ProductClass->setPrice01($row['通常価格']);
        }

        if (empty($row['販売価格'])) {
            $this->addErrors(($data->key() + 1) . '行目の販売価格が設定されていません。');
        } else {
            $ProductClass->setPrice02($row['販売価格']);
        }
        if (empty($row['商品規格削除フラグ'])) {
            $ProductClass->setDelFlg(Constant::DISABLED);
        } else {
            if ($row['商品規格削除フラグ'] == Constant::DISABLED || $row['商品規格削除フラグ'] == Constant::ENABLED) {
                $ProductClass->setDelFlg($row['商品規格削除フラグ']);
            } else {
                $this->addErrors(($data->key() + 1) . '行目の商品規格削除フラグが設定されていません。');
            }
        }

        $ProductStock = $ProductClass->getProductStock();

        if (!$ProductClass->getStockUnlimited()) {
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
     * CSVヘッダー定義
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
            '商品種別(ID)' => 'product_type',
            '規格分類1(ID)' => 'class_category1',
            '規格分類2(ID)' => 'class_category2',
            '発送日目安(ID)' => 'deliveryFee',
            '商品コード' => 'product_code',
            '在庫数' => 'stock',
            '在庫数無制限フラグ' => 'stock_unlimited',
            '販売数制限数' => 'sale_limit',
            '通常価格' => 'price01',
            '販売価格' => 'price02',
            '送料' => 'delivery_fee',
            '商品規格削除フラグ' => 'product_class_del_flg',
        );
    }

}
