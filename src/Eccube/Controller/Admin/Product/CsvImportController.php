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

use Doctrine\Common\Util\Debug;
use Eccube\Application;
use Symfony\Component\Form\FormError;
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

        $builder = $app['form.factory']->createBuilder('admin_csv_upload');

        $form = $builder->getForm();
        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            switch ($request->get('mode')) {
                case 'upload':

                    if ($form->isValid()) {

                        $data = $form->getData();
                        $ClassName1 = $data['class_name1'];
                        $ClassName2 = $data['class_name2'];

                        if (!is_null($ClassName2) && $ClassName1->getId() == $ClassName2->getId()) {
                            // 規格1と規格2が同じ値はエラー

                            $form['class_name2']->addError(new FormError('規格1と規格2は、同じ値を使用できません。'));

                        } else {

                            // 規格分類が設定されていない商品規格を取得
                            $orgProductClasses = $Product->getProductClasses();
                            $sourceProduct = $orgProductClasses[0];

                            // 規格分類が組み合わされた商品規格を取得
                            $ProductClasses = $this->createProductClasses($app, $Product, $ClassName1, $ClassName2);

                            // 組み合わされた商品規格にデフォルト値をセット
                            foreach ($ProductClasses as $productClass) {
                                $this->setDefualtProductClass($productClass, $sourceProduct);
                            }

                            $productClassForm = $app->form()
                                ->add('product_classes', 'collection', array(
                                    'type' => 'admin_product_class',
                                    'allow_add' => true,
                                    'allow_delete' => true,
                                    'data' => $ProductClasses,
                                ))
                                ->getForm()
                                ->createView();
                        }
                    }
                    break;

                default:
                    break;
            }
        }

        return $app->render('Product/csv_product.twig', array(
            'form' => $form->createView(),
        ));

    }


    /**
     * アップロード用CSV雛形ファイルダウンロード
     */
    public function csvTemplate(Application $app, Request $request)
    {
        set_time_limit(0);

        // sql loggerを無効にする.
        $em = $app['orm.em'];
        $em->getConfiguration()->setSQLLogger(null);

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
