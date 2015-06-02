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

namespace Eccube\Controller\Admin\Customer;

use Doctrine\Common\Util\Debug;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerEditController extends AbstractController
{
    public function index(Application $app, Request $request, $id = null)
    {
        // 編集
        if ($id) {
            $Customer = $app['orm.em']
                ->getRepository('Eccube\Entity\Customer')
                ->find($id);

            if (is_null($Customer)) {
                throw new NotFoundHttpException();
            }
            // 編集用にデフォルトパスワードをセット
            $previous_password = $Customer->getPassword();
            $Customer->setPassword($app['config']['default_password']);
        // 新規登録
        } else {
            $Customer =  $app['eccube.repository.customer']->newCustomer();
        }

        // 会員登録フォーム
        $form = $app['form.factory']
            ->createBuilder('admin_customer', $Customer)
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                if ($Customer->getId() === null) {
                    $Customer->setSalt(
                        $app['orm.em']
                            ->getRepository('Eccube\Entity\Customer')
                            ->createSalt(5)
                    );
                    $Customer->setSecretKey(
                        $app['orm.em']
                            ->getRepository('Eccube\Entity\Customer')
                            ->getUniqueSecretKey($app)
                    );
                }

                if ($Customer->getPassword() === $app['config']['default_password']) {
                    $Customer->setPassword($previous_password);
                } else {
                    $Customer->setPassword(
                        $app['orm.em']
                            ->getRepository('Eccube\Entity\Customer')
                            ->encryptPassword($app, $Customer)
                    );
                }

                $app['orm.em']->persist($Customer);
                $app['orm.em']->flush();

                $app->addSuccess('admin.customer.save.complete', 'admin');

                return $app->redirect($app->url('admin_customer_edit', array(
                    'id' => $Customer->getId(),
                )));
            }
        }

        return $app->render('Customer/edit.twig', array(
            'form' => $form->createView(),
            'Customer' => $Customer,
        ));
    }
}
