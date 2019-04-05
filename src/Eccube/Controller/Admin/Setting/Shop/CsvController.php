<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints as Assert;

class CsvController extends AbstractController
{
    public function index(Application $app, Request $request, $id = CsvType::CSV_TYPE_ORDER)
    {

        $CsvType = $app['eccube.repository.master.csv_type']->find($id);
        if (is_null($CsvType)) {
            throw new NotFoundHttpException();
        }

        $builder = $app->form();

        $builder->add('csv_type', 'csv_type', array(
            'label' => 'CSV出力項目',
            'required' => true,
            'constraints' => array(
                new Assert\NotBlank(),
            ),
            'data' => $CsvType,
        ));

        $CsvNotOutput = $app['eccube.repository.csv']->findBy(array('CsvType' => $CsvType, 'enable_flg' => Constant::DISABLED), array('rank' => 'ASC'));

        $builder->add('csv_not_output', 'entity', array(
            'class' => 'Eccube\Entity\Csv',
            'property' => 'disp_name',
            'required' => false,
            'expanded' => false,
            'multiple' => true,
            'choices' => $CsvNotOutput,
        ));

        $CsvOutput = $app['eccube.repository.csv']->findBy(array('CsvType' => $CsvType, 'enable_flg' => Constant::ENABLED), array('rank' => 'ASC'));

        $builder->add('csv_output', 'entity', array(
            'class' => 'Eccube\Entity\Csv',
            'property' => 'disp_name',
            'required' => false,
            'expanded' => false,
            'multiple' => true,
            'choices' => $CsvOutput,
        ));

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'CsvOutput' => $CsvOutput,
                'CsvType' => $CsvType,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_CSV_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {

            $data = $request->get('form');
            if (isset($data['csv_not_output'])) {
                $Csvs = $data['csv_not_output'];
                $rank = 1;
                foreach ($Csvs as $csv) {
                    $c = $app['eccube.repository.csv']->find($csv);
                    $c->setRank($rank);
                    $c->setEnableFlg(Constant::DISABLED);
                    $rank++;
                }
            }

            if (isset($data['csv_output'])) {
                $Csvs = $data['csv_output'];
                $rank = 1;
                foreach ($Csvs as $csv) {
                    $c = $app['eccube.repository.csv']->find($csv);
                    $c->setRank($rank);
                    $c->setEnableFlg(Constant::ENABLED);
                    $rank++;
                }
            }

            $app['orm.em']->flush();

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'CsvOutput' => $CsvOutput,
                    'CsvType' => $CsvType,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_CSV_INDEX_COMPLETE, $event);

            $app->addSuccess('admin.shop.csv.save.complete', 'admin');

            return $app->redirect($app->url('admin_setting_shop_csv', array('id' => $id)));
        }


        return $app->render('Setting/Shop/csv.twig', array(
            'form' => $form->createView(),
            'id' => $id,
        ));

    }

}