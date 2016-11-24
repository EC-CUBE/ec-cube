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


namespace Eccube\Controller\Admin\Setting\System;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;

class MasterdataController extends AbstractController
{
    public function index(Application $app, Request $request, $entity = null)
    {
        $data = array();

        $builder = $app['form.factory']->createBuilder('admin_system_masterdata');

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MASTERDATA_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $event = new EventArgs(
                    array(
                        'form' => $form,
                    ),
                    $request
                );
                $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MASTERDATA_INDEX_COMPLETE, $event);

                if ($event->hasResponse()) {
                    return $event->getResponse();
                }

                return $app->redirect($app->url('admin_setting_system_masterdata_view', array('entity' => $form['masterdata']->getData())));
            }
        } elseif (!is_null($entity)) {
            $form->submit(array('masterdata' => $entity));
            if ($form['masterdata']->isValid()) {
                $entityName = str_replace('-', '\\', $entity);
                try {
                    $masterdata = $app['orm.em']->getRepository($entityName)->findBy(array(), array('rank' => 'ASC'));
                    $data['data'] = array();
                    $data['masterdata_name'] = $entity;
                    foreach ($masterdata as $value) {
                        $data['data'][$value['id']]['id'] = $value['id'];
                        $data['data'][$value['id']]['name'] = $value['name'];
                    }
                    $data['data'][] = array(
                        'id' => '',
                        'name' => '',
                    );
                } catch (MappingException $e) {
                }
            }
        }

        $builder2 = $app['form.factory']->createBuilder('admin_system_masterdata_edit', $data);

        $event = new EventArgs(
            array(
                'builder' => $builder2,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MASTERDATA_INDEX_FORM2_INITIALIZE, $event);

        $form2 = $builder2->getForm();

        return $app->render('Setting/System/masterdata.twig', array(
            'form' => $form->createView(),
            'form2' => $form2->createView(),
        ));
    }

    public function edit(Application $app, Request $request)
    {
        $builder2 = $app['form.factory']->createBuilder('admin_system_masterdata_edit');

        $event = new EventArgs(
            array(
                'builder' => $builder2,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MASTERDATA_EDIT_INITIALIZE, $event);

        $form2 = $builder2->getForm();

        if ('POST' === $request->getMethod()) {
            $form2->handleRequest($request);

            if ($form2->isValid()) {
                $data = $form2->getData();

                $entityName = str_replace('-', '\\', $data['masterdata_name']);
                $entity = new $entityName();
                $rank = 0;
                $ids = array_map(function ($v) {return $v['id'];}, $data['data']);
                foreach ($data['data'] as $key => $value) {
                    if ($value['id'] !== null && $value['name'] !== null) {
                        $entity->setId($value['id']);
                        $entity->setName($value['name']);
                        $entity->setRank($rank++);
                        $app['orm.em']->merge($entity);
                    } elseif (!in_array($key, $ids)) {
                        // remove
                        $delKey = $app['orm.em']->getRepository($entityName)->find($key);
                        if ($delKey) {
                            $app['orm.em']->remove($delKey);
                        }
                    }
                }

                try {
                    $app['orm.em']->flush();

                    $event = new EventArgs(
                        array(
                            'form' => $form2,
                        ),
                        $request
                    );
                    $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MASTERDATA_EDIT_COMPLETE, $event);

                    $app->addSuccess('admin.register.complete', 'admin');
                } catch (\Exception $e) {
                    // 外部キー制約などで削除できない場合に例外エラーになる
                    $app->addError('admin.register.failed', 'admin');
                }

                return $app->redirect($app->url('admin_setting_system_masterdata_view', array('entity' => $data['masterdata_name'])));
            }
        }

        $builder = $app['form.factory']->createBuilder('admin_system_masterdata');

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MASTERDATA_EDIT_FORM_INITIALIZE, $event);

        $form = $builder->getForm();
        $parameter = array_merge($request->request->all(), array('masterdata' => $form2['masterdata_name']->getData()));
        $form->submit($parameter);

        return $app->render('Setting/System/masterdata.twig', array(
            'form' => $form->createView(),
            'form2' => $form2->createView(),
        ));
    }
}
