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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;


class CsvImportController
{

    private $errors = array();

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

                $file = $form['import_file']->getData();

                if (!empty($file)) {
                    // アップロードされたCSVファイルを一時ディレクトリに保存
                    $fileName = Str::random() . '.' . $file->guessExtension();
                    $file->move($app['config']['csv_temp_realdir'], $fileName);

                    $file = file_get_contents($app['config']['csv_temp_realdir'] . '/' . $fileName);
                    // アップロードされたファイルがUTF-8以外は文字コード変換を行う
                    $encode = Str::characterEncoding(substr($file, 0, 6));
                    if ($encode != 'UTF-8') {
                        $file = mb_convert_encoding($file, 'UTF-8', $encode);
                    }

                    $tmp = tmpfile();
                    fwrite($tmp, $file);
                    rewind($tmp);
                    $meta = stream_get_meta_data($tmp);
                    $file = new \SplFileObject($meta['uri']);

                    // $data = new CsvImportService($app, $file, $app['config']['csv_delimiter'], $app['config']['csv_enclosure'], $app['config']['csv_escape']);
                    $data = new CsvImportService($file, $app['config']['csv_delimiter'], $app['config']['csv_enclosure']);

                    $data->setHeaderRowNumber(0);
                    $data->setStrict(false);

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

                    $em = $app['orm.em'];
                    $em->getConfiguration()->setSQLLogger(null);


                    foreach ($data as $row) {

                        if ($headerSize != count($row)) {
                            $this->addErrors(($data->key() + 1) . '行目のCSVフォーマットが一致しません。');
                            return $this->render($app, $form, $headers);
                        }

                        if (empty($row['商品ID'])) {
                            $Product = new Product();
                        } else {
                            $Product = $app['eccube.repository.product']->find($row['商品ID']);
                            if (!$Product) {
                                $this->addErrors(($data->key() + 1) . '行目の商品が存在しません。');
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
                            $Product->setName($row['ショップ用メモ欄']);
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

                        if (!empty($row['商品画像'])) {

                            $ProductImage = new ProductImage();
                            $ProductImage->setFileName($row['商品画像']);
                            $ProductImage->setProduct($Product);

                            $Product->addProductImage($ProductImage);

                        }

                        if (!empty($row['商品カテゴリ(ID)'])) {

                            $Category = $app['eccube.repository.category']->find($row['商品カテゴリ(ID)']);
                            if (!$Category) {
                                $this->addErrors(($data->key() + 1) . '行目の商品カテゴリ(ID)が存在しません。');
                            } else {

                                $ProductCategory = new ProductCategory();
                                $ProductCategory->setProduct($Product);
                                $ProductCategory->setCategory($Category);
                                $Product->addProductCategory($ProductCategory);
                            }


                        }

                        // 商品規格が存在しなければ新規登録
                        if (!$Product->hasProductClass()) {

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

                            // 規格分類1(ID)がセットされていると規格なし商品、規格あり商品を作成
                            if (empty($row['規格分類1(ID)'])) {

                                $ProductClass->setClassCategory1(null);
                                $ProductClass->setClassCategory2(null);

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
                            } else {
                                // 商品規格あり

                            }


                        } else {
                            // 商品規格の更新

                        }


                        // Persisting the current user
                        // $em->persist($user);

                        if ($this->hasErrors()) {
                            return $this->render($app, $form, $headers);
                        }

                        $em->persist($Product);
                        $em->persist($ProductClass);

                    }

                    $em->flush();
                    $em->clear();

                    $app->addSuccess('admin.product.csv_upload.save.complete', 'admin');
                }

            }
        }

        return $this->render($app, $form, $headers);
    }

    public function convert($filename, $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return FALSE;
        }

        $header = NULL;
        $data = array();

        if (($handle = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                if (!$header) {
                    $header = $row;
                } else {
                    // $row = mb_convert_encoding($row, 'UTF-8', $app['config']['csv_encoding']);
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;
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
        return $app->render('Product/csv_product.twig', array(
            'form' => $form->createView(),
            'headers' => $headers,
            'errors' => $this->errors,
        ));
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
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     *
     * @return boolean
     */
    public function hasErrors()
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
