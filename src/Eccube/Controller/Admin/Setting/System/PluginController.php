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

        $form = $app['form.factory']
            ->createBuilder('plugin_local_install')
            ->getForm();
        $service = $app['eccube.service.plugin'];
        $em = $app['orm.em'];
        $repo=$em->getRepository('Eccube\Entity\Plugin');
        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);
            $data = $form->getData();

            if($form->get('install')->isClicked()){

                $tmpdir = $service->createTempDir( ) ;
                $tmpfile = sha1(openssl_random_pseudo_bytes(20) ) ;

                $form['plugin_archive']->getData()->move( $tmpdir, $tmpfile);

                $service->install($tmpdir . '/' . $tmpfile);
            }
            if($form->get('uninstall')->isClicked()){
                $service->uninstall(  $repo->find((int)$data['plugin_id'] )     );
            }
            if($form->get('enable')->isClicked()){
                $service->enable(  $repo->find((int)$data['plugin_id'] )     );
            }
            if($form->get('disable')->isClicked()){
                $service->disable(  $repo->find((int)$data['plugin_id'] )     );
            }
            if($form->get('update')->isClicked()){

                $tmpdir = $service->createTempDir( ) ;
                $tmpfile = sha1(openssl_random_pseudo_bytes(20) ) ;

                $form['plugin_archive']->getData()->move( $tmpdir, $tmpfile);

                $service->update( $repo->find((int)$data['plugin_id'] )  , $tmpdir.'/'.$tmpfile);
            }
            
        }

        return $app['twig']->render('Setting/System/Plugin/install.twig', array(
            'form' => $form->createView(),
        ));

    }


    function handler(Application $app)
    {
        $em = $app['orm.em'];
        $handlers = $em->getRepository('Eccube\Entity\PluginEventHandler')->getHandlers();
        return $app->render('Setting/System/Plugin/handler.twig', array(
            'handlers' => $handlers
        ));

    }
    function handler_up(Application $app,$handlerId)
    {
        $em = $app['orm.em'];
        $repo = $em->getRepository('Eccube\Entity\PluginEventHandler');
        $repo->upPriority(  $repo->find( $handlerId )  );

        return $app->redirect($app['url_generator']->generate('admin_setting_system_plugin_handler'));
    }

    function handler_down(Application $app,$handlerId)
    {
        $em = $app['orm.em'];
        $repo = $em->getRepository('Eccube\Entity\PluginEventHandler');
        $repo->upPriority(  $repo->find( $handlerId ),false  );
        return $app->redirect($app['url_generator']->generate('admin_setting_system_plugin_handler'));
    }

    function disable(Application $app)
    {
    }

    function enable(Application $app)
    {
    }

    function edit(Application $app)
    {
    }
}
