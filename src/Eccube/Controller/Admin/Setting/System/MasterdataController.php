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

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Form\Type\Admin\MasterdataDataType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MasterdataController extends AbstractController
{
    public function index(Application $app, Request $request)
    {
        $builder = $app['form.factory']->createBuilder('admin_system_masterdata');
        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();

                if ($data['masterdata']) {
                    $masterdata = $app['orm.em']->getRepository($data['masterdata'])->findBy(array(), array('rank' => 'ASC'));

                    foreach ($masterdata as $key => $value) {
                        $data['data'][$value['rank']]['id'] = $value['id'];
                        $data['data'][$value['rank']]['name'] = $value['name'];
                        $data['data'][$value['rank']]['rank'] = $value['rank'];
                    }

                    $data['data'][$value['rank']+1]['id'] = '';
                    $data['data'][$value['rank']+1]['name'] = '';
                    $data['data'][$value['rank']+1]['rank'] = '';

                    $data['masterdata_name'] = $data['masterdata'];
                }
            }
        } else {
            $data = array();
        }

        $builder2 = $app['form.factory']->createBuilder('admin_system_masterdata_edit', $data);
        $form2 = $builder2->getForm();

        return $app->render('Setting/System/masterdata.twig', array(
            'form' => $form->createView(),
            'form2' => $form2->createView(),
        ));
    }

    public function edit(Application $app, Request $request)
    {
        $builder2 = $app['form.factory']->createBuilder('admin_system_masterdata_edit');
        $form2 = $builder2->getForm();

        if ('POST' === $request->getMethod()) {
            $form2->handleRequest($request);

            if ($form2->isValid()) {
                $data = $form2->getData();

                $entity = new $data['masterdata_name']();
                foreach ($data['data'] as $key => $value) {
                    if (!is_null($value['id']) && !is_null($value['name'])) {
                        $entity->setId($value['id']);
                        $entity->setName($value['name']);
                        $entity->setRank($key);
                        $app['orm.em']->merge($entity);
                    } else {
                        // remove
                        $rank = $app['orm.em']->getRepository($data['masterdata_name'])->findOneBy(array('rank' => $key));
                        if ($rank) {
                            $app['orm.em']->remove($rank);
                        }
                    }
                }
                $app['orm.em']->flush();

                $app->addSuccess('admin.register.complete', 'admin');
                return $app->redirect($app->url('admin_setting_system_masterdata'));
            }
        }

        $builder = $app['form.factory']->createBuilder('admin_system_masterdata');
        $form = $builder->getForm();

        return $app->render('Setting/System/masterdata.twig', array(
            'form' => $form->createView(),
            'form2' => $form2->createView(),
        ));
    }
}
