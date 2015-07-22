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

        $headers = $app['eccube.service.csv.import']->getProductCsvHeader();
        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $file = $form['import_file']->getData();

                error_log($file->getClientOriginalName());

                if (!empty($file)) {
                    // アップロードされたCSVファイルを一時ディレクトリに保存
                    $fileName = Str::random() . '.' . $file->guessExtension();
                    $file->move($app['config']['csv_temp_realdir'], $fileName);



                    $reader = new CsvReader($file, $this->delimiter, $this->enclosure, $this->escape);
                    if (null !== $this->headerRowNumber) {
                        $reader->setHeaderRowNumber($this->headerRowNumber);
                    }

                    $reader->setStrict($this->strict);
                }

            }
        }

        return $app->render('Product/csv_product.twig', array(
            'form' => $form->createView(),
            'headers' => $headers,
        ));

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
