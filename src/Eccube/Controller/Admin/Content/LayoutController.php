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


namespace Eccube\Controller\Admin\Content;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class LayoutController
{
    public $title;

    public $device_type_id;

    public function __construct()
    {
    }

    public function index(Application $app, $id = 1)
    {
        $this->device_type_id = $app['config']['device_type_pc'];

        // 一覧表示用
        $PageLayouts = $app['orm.em']->getRepository('\Eccube\Entity\PageLayout')
            ->findBy(array(
                'device_type_id' => $this->device_type_id,
            ));

        $query = $app['orm.em']->createQueryBuilder()
            ->select('t')
            ->from('\Eccube\Entity\Master\Target', 't', 't.id')
            ->getQuery();
        $Target = $query->getResult();

        // 編集ページ情報の取得
        // bloc_rowでソートしたいため、find()ではなくQueryBuilderを使う
        $query = $app['orm.em']->createQueryBuilder()
            ->select('pl', 'bp', 'b')
            ->from('\Eccube\Entity\PageLayout', 'pl')
            ->leftJoin('pl.BlocPositions', 'bp')
            ->leftJoin('bp.Bloc', 'b')
            ->orderby('bp.bloc_row', 'ASC')
            ->andWhere('pl.device_type_id = :device_type_id')
            ->setParameter('device_type_id', $this->device_type_id)
            ->andWhere('pl.page_id = :page_id')
            ->setParameter('page_id', $id)
            ->getQuery();
        $Layout = $query->getSingleResult();

        // 全ページ適用データの取得
        $allPageBlocPositions = $app['orm.em']->getRepository('\Eccube\Entity\BlocPosition')
            ->findBy(array(
                'device_type_id' => $this->device_type_id,
                'anywhere' => 1,
            ));
        foreach ($allPageBlocPositions as $allPageBlocPosition) {
            if ($allPageBlocPosition->getPageId() != $id) {
                $Layout->addBlocPosition($allPageBlocPosition);
            }
        }
        // 未使用ブロックの取得
        $Blocs = $app['orm.em']->getRepository('\Eccube\Entity\Bloc')
            ->findBy(array(
                'device_type_id' => $this->device_type_id,
            ));
        $BlocPositions = $Layout->getBlocPositions();
        $usedBlocIds = array();
        foreach ($BlocPositions as $BlocPosition) {
            $usedBlocIds[] = $BlocPosition->getBlocId();
        }
        foreach ($Blocs as $Bloc) {
            if (!in_array($Bloc->getBlocId(), $usedBlocIds)) {
                $UnuseBlocPositions = new \Eccube\Entity\BlocPosition();
                $UnuseBlocPositions
                    ->setDeviceTypeId($this->device_type_id)
                    ->setPageId($id)
                    ->setTargetId($app['config']['target_id_unused'])
                    ->setAnywhere(0)
                    ->setBlocRow(0)
                    ->setBlocId($Bloc->getBlocId())
                    ->setBloc($Bloc)
                    ->setPageLayout($Layout);
                $Layout->addBlocPosition($UnuseBlocPositions);
            }
        }

        $form = $app['form.factory']
            ->createBuilder()
            ->getForm();

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);

            if ($form->isValid()) {
                // 消す
                $blocCount = count($BlocPositions);

                foreach ($BlocPositions as $BlocPosition) {
                    if ($BlocPosition->getPageId() == $id || $BlocPosition->getAnywhere() == 0) {
                        $Layout->removeBlocPosition($BlocPosition);
                        $app['orm.em']->remove($BlocPosition);
                    }
                }
                $app['orm.em']->flush();

                $TargetHash = $this->getTragetHash($Target);

                // TODO: collection を利用
                $data = $app['request']->request->all();
                for ($i = 1; $i <= $blocCount; $i++) {
                    // bloc_id が取得できない場合は INSERT しない
                    if (!isset($data['id_' . $i])) {
                        continue;
                    }
                    // 未使用は INSERT しない
                    if ($TargetHash[$data['target_id_' . $i]] === $app['config']['target_id_unused']) {
                        continue;
                    }
                    // 他のページに anywhere が存在する場合は INSERT しない
                    $anywhere = (isset($data['anywhere_' . $i]) && $data['anywhere_' . $i] == 1) ? 1 : 0;
                    if (isset($data['anywhere_' . $i]) && $data['anywhere_' . $i] == 1) {
                        $Other = $app['orm.em']->getRepository('\Eccube\Entity\BlocPosition')
                            ->findBy(array(
                                'anywhere' => 1,
                                'bloc_id' => $data['id_' . $i],
                                'device_type_id' => $this->device_type_id,
                            ));
                        if (count($Other) > 0) {
                            continue;
                        }
                    }

                    $BlocPosition = new \Eccube\Entity\BlocPosition();
                    $Bloc = $app['orm.em']->getRepository('\Eccube\Entity\Bloc')
                        ->findOneBy(array(
                            'bloc_id' => $data['id_' . $i],
                            'device_type_id' => $this->device_type_id,
                        ));
                    $BlocPosition
                        ->setDeviceTypeId($this->device_type_id)
                        ->setPageId($id)
                        ->setBlocId($data['id_' . $i])
                        ->setBlocRow($data['top_' . $i])
                        ->setTargetId($TargetHash[$data['target_id_' . $i]])
                        ->setBloc($Bloc)
                        ->setPageLayout($Layout)
                        ->setAnywhere($anywhere);
                    if ($id == 0) {
                        $BlocPosition->setAnywhere(0);
                    }
                    $Layout->addBlocPosition($BlocPosition);
                }

                $app['orm.em']->persist($Layout);
                $app['orm.em']->flush();

                $app['session']->getFlashBag()->add('design.complete', 'admin.register.complete');

                return $app->redirect($app['url_generator']->generate('admin_content_layout_edit', array('id' => $id)));
            }

        }

        return $app['view']->render('Admin/Content/layout.twig', array(
            'form' => $form->createView(),
            'PageLayouts' => $PageLayouts,
            'Layout' => $Layout,
            'Target' => $Target,
        ));
    }

    public function getTragetHash($Targets)
    {
        $TargetHash = array();
        foreach ($Targets as $key => $Target) {
            $TargetHash[$Target->getName()] = $key;
        }

        return $TargetHash;
    }
}
