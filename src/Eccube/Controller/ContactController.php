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


namespace Eccube\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class ContactController
{

    public function index(Application $app, Request $request)
    {

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('contact');

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ($app['security']->isGranted('ROLE_USER')) {
            /* @var $user \Eccube\Entity\Customer */
            $user = $app['user'];
            $form->setData(array(
                'name01' => $user->getName01(),
                'name02' => $user->getName02(),
                'kana01' => $user->getKana01(),
                'kana02' => $user->getKana02(),
                'zip01' => $user->getZip01(),
                'zip02' => $user->getZip02(),
                'pref' => $user->getPref(),
                'addr01' => $user->getAddr01(),
                'addr02' => $user->getAddr02(),
                'tel01' => $user->getTel01(),
                'tel02' => $user->getTel02(),
                'tel03' => $user->getTel03(),
                'email' => $user->getEmail(),
            ));
        }

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                switch ($request->get('mode')) {
                    case 'confirm':
                        $builder->setAttribute('freeze', true);
                        $form = $builder->getForm();
                        $form->handleRequest($request);

                        return $app->render('Contact/confirm.twig', array(
                            'form' => $form->createView(),
                        ));

                    case 'complete':
                        // メール送信
                        $app['eccube.service.mail']->sendrContactMail($form->getData());

                        return $app->redirect($app->url('contact_complete'));
                        break;
                }
            }
        }

        return $app->render('Contact/index.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function complete(Application $app)
    {
        return $app->render('Contact/complete.twig');
    }
}
