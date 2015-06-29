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

class PluginController extends AbstractController
{
    public function index(Application $app)
    {
        $repo = $app['eccube.repository.plugin'];

        $pluginForms = array();
        $Plugins = $repo->findBy(array(), array('id' => 'ASC'));
        foreach ($repo->findAll() as $Plugin) {
            $builder = $app['form.factory']->createNamedBuilder('form' . $Plugin->getId(), 'plugin_management', null, array(
                'plugin_id' => $Plugin->getId(),
                'enable' => $Plugin->getEnable()
            ));
            $pluginForms[$Plugin->getId()] = $builder->getForm()->createView();
        }
        return $app->render('Setting/System/Plugin/index.twig', array(
            'plugin_forms' => $pluginForms,
            'Plugins' => $Plugins
        ));
    }

    public function install(Application $app)
    {
        $form = $app['form.factory']
            ->createBuilder('plugin_local_install')
            ->getForm();
        $service = $app['eccube.service.plugin'];

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);
            $tmpDir = $service->createTempDir();
            $tmpFile = sha1(openssl_random_pseudo_bytes(20)) . ".tar";

            $form['plugin_archive']->getData()->move($tmpDir, $tmpFile);

            $service->install($tmpDir . '/' . $tmpFile);
        }

        return $app->render('Setting/System/Plugin/install.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function update(Application $app, $id)
    {
        $Plugin = $app['eccube.repository.plugin']
            ->find($id);

        $form = $app['form.factory']
            ->createNamedBuilder('form' . $id, 'plugin_management', null, array(
                'plugin_id' => null, // placeHolder
                'enable' => null,
            ))
            ->getForm();

        $form->handleRequest($app['request']);

        $tmpDir = $app['eccube.service.plugin']->createTempDir();
        $tmpFile = sha1(openssl_random_pseudo_bytes(20)) . ".tar";

        $form['plugin_archive']->getData()->move($tmpDir, $tmpFile);
        $app['eccube.service.plugin']->update($Plugin, $tmpDir . '/' . $tmpFile);
        $app->addSuccess('admin.plugin.update.complete', 'admin');

        return $app->redirect($app->url('admin_setting_system_plugin_index'));
    }

    public function enable(Application $app, $id)
    {
        $Plugin = $app['eccube.repository.plugin']
            ->find($id);
        if ($Plugin->getEnable() == 1) {
            $app->addError('admin.plugin.already.enable', 'admin');
        } else {
            $app['eccube.service.plugin']->enable($Plugin);
            $app->addSuccess('admin.plugin.enable.complete');
        }

        return $app->redirect($app->url('admin_setting_system_plugin_index'));
    }

    public function disable(Application $app, $id)
    {
        $Plugin = $app['eccube.repository.plugin']
            ->find($id);
        if ($Plugin->getEnable() == 1) {
            $app['eccube.service.plugin']->disable($Plugin);
            $app->addSuccess('admin.plugin.disable.complete');
        } else {
            $app->addError('admin.plugin.already.disable', 'admin');
        }

        return $app->redirect($app->url('admin_setting_system_plugin_index'));
    }

    public function uninstall(Application $app, $id)
    {
        $Plugin = $app['eccube.repository.plugin']
            ->find($id);
        $app['eccube.service.plugin']->uninstall($Plugin);

        return $app->redirect($app->url('admin_setting_system_plugin_index'));
    }

    function handler(Application $app)
    {
        $handlers = $app['eccube.repository.plugin_event_handler']->getHandlers();

        // 一次元配列からイベント毎の二次元配列に変換する 
        $HandlersPerEvent = array();
        foreach ($handlers as $handler) {
            $HandlersPerEvent[$handler->getEvent()][$handler->getHandlerType()][] = $handler;
        }

        return $app->render('Setting/System/Plugin/handler.twig', array(
            'handlersPerEvent' => $HandlersPerEvent
        ));

    }

    function handler_up(Application $app, $handlerId)
    {
        $repo = $app['eccube.repository.plugin_event_handler'];
        $repo->upPriority($repo->find($handlerId));

        return $app->redirect($app->url('admin_setting_system_plugin_handler'));
    }

    function handler_down(Application $app, $handlerId)
    {
        $repo = $app['eccube.repository.plugin_event_handler'];
        $repo->upPriority($repo->find($handlerId), false);

        return $app->redirect($app->url('admin_setting_system_plugin_handler'));
    }

}
