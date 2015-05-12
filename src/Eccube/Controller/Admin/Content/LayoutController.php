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
    public function index(Application $app, Request $request, $id = 1)
    {
        $device_type_id = $app['config']['device_type_pc'];

        // 一覧表示用
        $PageLayouts = $app['eccube.repository.page_layout']
            ->findBy(array(
                'device_type_id' => $device_type_id,
            ));

        $Targets = $app['eccube.repository.master.target']->getAll();

        // 編集対象ページ
        /* @var $TargetPageLayout \Eccube\Entity\PageLayout */
        $TargetPageLayout = $app['eccube.repository.page_layout']->get($device_type_id, $id);

        // 未使用ブロックの取得
        $Blocs = $app['orm.em']->getRepository('Eccube\Entity\Bloc')
            ->findBy(array(
                'device_type_id' => $device_type_id,
            ));
        $BlocPositions = $TargetPageLayout->getBlocPositions();
        foreach ($Blocs as $Bloc) {
            if (!$BlocPositions->containsKey($Bloc->getBlocId())) {
                $UnuseBlocPosition = new \Eccube\Entity\BlocPosition();
                $UnuseBlocPosition
                    ->setDeviceTypeId($device_type_id)
                    ->setPageId($id)
                    ->setTargetId($app['config']['target_id_unused'])
                    ->setAnywhere(0)
                    ->setBlocRow(0)
                    ->setBlocId($Bloc->getBlocId())
                    ->setBloc($Bloc)
                    ->setPageLayout($TargetPageLayout);
                $TargetPageLayout->addBlocPosition($UnuseBlocPosition);
            }
        }

        $form = $app['form.factory']
            ->createBuilder()
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                // 消す
                $blocCount = count($BlocPositions);

                foreach ($BlocPositions as $BlocPosition) {
                    if ($BlocPosition->getPageId() == $id || $BlocPosition->getAnywhere() == 0) {
                        $TargetPageLayout->removeBlocPosition($BlocPosition);
                        $app['orm.em']->remove($BlocPosition);
                    }
                }
                $app['orm.em']->flush();

                $TargetHash = $this->getTragetHash($Targets);

                // TODO: collection を利用
                $data = $request->request->all();
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
                        $Other = $app['orm.em']->getRepository('Eccube\Entity\BlocPosition')
                            ->findBy(array(
                                'anywhere' => 1,
                                'bloc_id' => $data['id_' . $i],
                                'device_type_id' => $device_type_id,
                            ));
                        if (count($Other) > 0) {
                            continue;
                        }
                    }

                    $BlocPosition = new \Eccube\Entity\BlocPosition();
                    $Bloc = $app['orm.em']->getRepository('Eccube\Entity\Bloc')
                        ->findOneBy(array(
                            'bloc_id' => $data['id_' . $i],
                            'device_type_id' => $device_type_id,
                        ));
                    $BlocPosition
                        ->setDeviceTypeId($device_type_id)
                        ->setPageId($id)
                        ->setBlocId($data['id_' . $i])
                        ->setBlocRow($data['top_' . $i])
                        ->setTargetId($TargetHash[$data['target_id_' . $i]])
                        ->setBloc($Bloc)
                        ->setPageLayout($TargetPageLayout)
                        ->setAnywhere($anywhere);
                    if ($id == 0) {
                        $BlocPosition->setAnywhere(0);
                    }
                    $TargetPageLayout->addBlocPosition($BlocPosition);
                }

                $app['orm.em']->persist($TargetPageLayout);
                $app['orm.em']->flush();

                $app['session']->getFlashBag()->add('admin.success', 'admin.register.complete');

                return $app->redirect($app['url_generator']->generate('admin_content_layout_edit', array('id' => $id)));
            }

        }

        return $app['view']->render('Content/layout.twig', array(
            'form' => $form->createView(),
            'TargetPageLayout' => $TargetPageLayout,
            'Targets' => $Targets,
            'PageLayouts' => $PageLayouts,
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
