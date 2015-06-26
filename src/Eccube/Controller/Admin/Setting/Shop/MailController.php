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


namespace Eccube\Controller\Admin\Setting\Shop;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MailController extends AbstractController
{
    public function index(Application $app, Request $request, $id = null)
    {
        $Mail = null;

        if ($id) {
            $Mail = $app['orm.em']
                ->getRepository('\Eccube\Entity\MailTemplate')
                ->find($id);
            if (is_null($Mail)) {
                throw new NotFoundHttpException();
            }
        }

        $form = $app['form.factory']
            ->createBuilder('mail', $Mail)
            ->getForm();

        $form['template']->setData($Mail);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            // 新規登録は現時点では未実装とする.
            if (is_null($Mail)) {
                $app->addError('admin.shop.mail.save.error', 'admin');

                return $app->redirect($app->url('admin_setting_shop_mail'));
            }

            if ($form->isValid()) {

                $app['orm.em']->flush();

                $app->addSuccess('admin.shop.mail.save.complete', 'admin');

                return $app->redirect($app->url('admin_setting_shop_mail_edit', array('id' => $id)));
            }
        }

        return $app->render('Setting/Shop/mail.twig', array(
            'form' => $form->createView(),
            'id' => $id,
        ));
    }
}
