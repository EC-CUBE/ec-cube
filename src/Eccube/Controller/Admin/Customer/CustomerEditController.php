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

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception as HttpException;

class CustomerEditController extends AbstractController
{
    private $tpl_maintitle;
    private $tpl_subtitle;

    public $form;

    public function __construct()
    {
    }

    public function index(Application $app, $id = null)
    {

        if ($id) {
            $Customer = $app['orm.em']->getRepository('Eccube\\Entity\\Customer')
                ->findOneBy(array(
                        'id' => $id,
                        'del_flg' => 0,
                    ));

            if ($Customer === null) {
                throw new HttpException\NotFoundHttpException("※ 会員ID：$id が見つかりません。");
            }

            if ($app['request']->getMethod() === 'POST') {
                $previous_password = $Customer->getPassword();
            } else {
                // 編集用にデフォルトパスワードをセット
                $Customer->setPassword($app['config']['default_password']);
            }

        } else {
            $Customer =  $app['eccube.repository.customer']->newCustomer();
        }

        //TODO: 購入処理ができてからちゃんと実装する
        $Order = $this->getOrder($app, $Customer);

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('customer', $Customer);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
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
                $app['session']->getFlashBag()->add('customer.complete', 'admin.register.complete');

                return $app->redirect($app['url_generator']->generate('admin_customer_edit', array(
                    'id' => $Customer->getId(),
                )));
            }
        }

        return $app['view']->render('Admin/Customer/edit.twig', array(
            'customerId' => $id,
            'Order' => $Order,
            'form' => $form->createView(),
        ));
    }

    /**
     * getCustomer
     *
     * 新規か編集かにあわせてCustomerObjectを返す
     *
     * @param  Application $app
     * @param $id
     * @return mixed
     */
    private function getCustomer(Application $app, $id)
    {
    }

    /**
     * 購入履歴を取得する
     * TODO: 購入が実装できてからちゃんと実装する
     *
     * @param  Application             $app
     * @param  \Eccube\Entity\Customer $Customer
     * @return mixed
     */
    private function getOrder(Application $app, \Eccube\Entity\Customer $Customer)
    {
        if ($Customer->getId() > 0) {
            $Order = $app['orm.em']->getRepository('Eccube\\Entity\\Order')
                ->findBy(array(
                        'Customer' => $Customer,
                        'del_flg' => 0,
                    ));

            return $Order;
        } else {
            return null;
        }
    }
}
