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

use Eccube\Controller\AbstractController;
use Eccube\Application;

class PluginController extends AbstractController
{
    public function install(Application $app)
    {

        $installForm = $app['form.factory']
            ->createBuilder('plugin_local_install')
            ->getForm();
        $service = $app['eccube.service.plugin'];
        if ('POST' === $app['request']->getMethod()) {
            $installForm->handleRequest($app['request']);
            $data = $installForm->getData();
            if($installForm->get('install')->isClicked()){

                $tmpdir = $service->createTempDir() ;
                $tmpfile = sha1(openssl_random_pseudo_bytes(20) ) ;

                $installForm['plugin_archive']->getData()->move( $tmpdir, $tmpfile);

                $service->install($tmpdir . '/' . $tmpfile);
            }
            
        }
        return $app->render('Setting/System/Plugin/install.twig', array(
            'install_form' => $installForm->createView(),
        ));

    }

    public function manage(Application $app)
    {

        $builder = $app['form.factory']->createNamedBuilder('', 'plugin_management', null, array(
            'plugin_id' => null, // placeHolder
            'enable' => null,
        ));

        $form = $builder->getForm();

        $service = $app['eccube.service.plugin'];
        $repo = $app['eccube.repository.plugin'];
        if ('POST' === $app['request']->getMethod()) {

            $form->handleRequest($app['request']);
            $data = $form->getData();

            $plugin = $repo->find((int)$data['plugin_id'] ) ; 

            if($form->get('uninstall')->isClicked()){
                $service->uninstall($plugin);
            }
            if($form->get('enable')->isClicked()){
                $service->enable($plugin);
            }
            if($form->get('disable')->isClicked()){
                $service->disable($plugin);
            }
            if($form->get('update')->isClicked()){

                $tmpdir = $service->createTempDir() ;
                $tmpfile = sha1(openssl_random_pseudo_bytes(20) ) ;

                $form['plugin_archive']->getData()->move( $tmpdir, $tmpfile);

                $service->update($plugin,$tmpdir.'/'.$tmpfile);
            }
        }
        return $app->redirect($app->url('admin_setting_system_plugin_index'));
    }

    public function index(Application $app){
        $repo = $app['eccube.repository.plugin'];

        $pluginForms=array();
        foreach($repo->findAll() as $plugin ){

            $builder = $app['form.factory']->createNamedBuilder('', 'plugin_management', null, array(
                'plugin_id' => $plugin->getId(),
                'enable' => $plugin->getEnable() 
            ));
            $pluginForms[$plugin->getId()] = $builder->getForm()->createView();
        }
        return $app->render('Setting/System/Plugin/index.twig', array(
            'plugin_forms' => $pluginForms,
            'plugins' => $repo->findBy(array(),array('id'=>'ASC')) 
        ));
    }

    function handler(Application $app)
    {
        $handlers = $app['eccube.repository.plugin_event_handler']->getHandlers();

        // 一次元配列からイベント毎の二次元配列に変換する 
        $HanlersPerEvent=array();
        foreach($handlers as $handler){
            $HanlersPerEvent[$handler->getEvent()][$handler->getHandlerType()][] = $handler;
        }

        return $app->render('Setting/System/Plugin/handler.twig', array(
            'handlersPerEvent' => $HanlersPerEvent 
        ));

    }
    function handler_up(Application $app,$handlerId)
    {
        $repo = $app['eccube.repository.plugin_event_handler'];
        $repo->upPriority(  $repo->find( $handlerId )  );

        return $app->redirect($app->url('admin_setting_system_plugin_handler'));
    }

    function handler_down(Application $app,$handlerId)
    {
        $repo = $app['eccube.repository.plugin_event_handler'];
        $repo->upPriority(  $repo->find( $handlerId ),false  );

        return $app->redirect($app->url('admin_setting_system_plugin_handler'));
    }

}
