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


namespace Eccube\Controller\Admin\Basis;

use Eccube\Application;
use Eccube\Controller\AbstractController;

class MailController extends AbstractController
{
    private $main_title;
    private $sub_title;

    public $form;

    public function __construct()
    {
        $this->main_title = '基本情報管理';
        $this->sub_title = 'メール管理';

        $this->tpl_mainno = 'basis';
        $this->tpl_subno = 'mail';
    }

    public function index(Application $app, $mailId = 0)
    {
        $Mail = $app['orm.em']
            ->getRepository('\Eccube\Entity\Mailtemplate')
            ->findOrCreate($mailId);
        $form = $app['form.factory']
            ->createBuilder('mail', $Mail)
            ->getForm();
        if ($mailId) {
            $form->get('template')->setData($Mail);
        }
        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);

            if ($form->isValid()) {
                $Mail = $form->getData();
                $app['orm.em']->persist($Mail);
                $app['orm.em']->flush();

                $app['session']->getFlashBag()->add('admin.mail.complete', 'admin.register.complete');

                return $app->redirect($app['url_generator']->generate('admin_basis_mail'));
            }
        }

        return $app['twig']->render('Admin/Basis/mail.twig', array(
            'tpl_maintitle' => $this->main_title,
            'tpl_subtitle' => $this->sub_title,
            'Mail' => $Mail,
            'mail_id' => $mailId,
            'form' => $form->createView(),
        ));
    }
}
