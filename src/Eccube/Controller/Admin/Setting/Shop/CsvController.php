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


namespace Eccube\Controller\Admin\Setting\Shop;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CsvType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints as Assert;

class CsvController extends AbstractController
{
    public function index(Application $app, Request $request, $id = CsvType::CSV_TYPE_PRODUCT)
    {

        $CsvType = $app['eccube.repository.master.csv_type']->find($id);
        if (is_null($CsvType)) {
            throw new NotFoundHttpException();
        }

        $options = array('CsvType' => $CsvType);
        $form = $app['form.factory']->createBuilder('csv', null, $options)->getForm();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $data = $form->getData();

                $Csvs = $data['csv_not_output'];
                foreach ($Csvs as $csv) {
                    // $csv->setRank();
                    error_log($csv->getDispName());
                    $csv->setEnableFlg(Constant::DISABLED);
                }

                $Csvs = $data['csv_output'];
                foreach ($Csvs as $csv) {
                    // $csv->setRank();
           //         error_log($csv->getDispName());
                    $csv->setEnableFlg(Constant::ENABLED);
                }

                $app->addSuccess('admin.shop.csv.save.complete', 'admin');

                //return $app->redirect($app->url('admin_setting_shop_csv', array('id' => $id)));
            }
        }

        return $app->render('Setting/Shop/csv.twig', array(
            'form' => $form->createView(),
            'id' => $id,
        ));

    }

}