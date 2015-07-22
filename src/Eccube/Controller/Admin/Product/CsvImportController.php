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
use Eccube\Util\Str;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Validator\Constraints as Assert;


class CsvImportController
{

    /**
     * 商品登録CSVアップロード
     */
    public function csvProduct(Application $app, Request $request)
    {

        $builder = $app['form.factory']->createBuilder('admin_csv_import');

        $csvService = $app['eccube.service.csv.import'];
        $headers = $csvService->getProductCsvHeader();
        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $file = $form['import_file']->getData();

                if (!empty($file)) {
                    // アップロードされたCSVファイルを一時ディレクトリに保存
                    $fileName = Str::random() . '.' . $file->guessExtension();
                    $file->move($app['config']['csv_temp_realdir'], $fileName);


                    // $rows = $csvService->loadCsv($app['config']['csv_temp_realdir'] . $fileName);
                    // $rows->setHeaderRowNumber(0);
                    // $rows->setStrict($this->strict);

                    $data = $this->convert($app['config']['csv_temp_realdir'] . '/' . $fileName);

                    $data = mb_convert_encoding($data, 'UTF-8', $app['config']['csv_encoding']);

                    $em = $app['orm.em'];

                    $em->getConfiguration()->setSQLLogger(null);

                    $size = count($data);

                    foreach ($data as $row) {

                        // Persisting the current user
                        // $em->persist($user);
                        error_log(print_r($row, true));

                    }

                    // Flushing and clear data on queue
                    $em->flush();
                    $em->clear();

                }

            }
        }

        return $app->render('Product/csv_product.twig', array(
            'form' => $form->createView(),
            'headers' => $headers,
        ));

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
                    $row = mb_convert_encoding($row, 'UTF-8', $app['config']['csv_encoding']);
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
        $response->setCallback(function () use ($app, $request) {
            // ヘッダ行の出力
            $app['eccube.service.csv.import']->exportHeader();
        });

        $filename = 'product.csv';
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        $response->send();

        return $response;
    }

}
