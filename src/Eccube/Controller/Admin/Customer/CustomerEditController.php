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
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\HttpKernel\Exception as HttpException;

class CustomerEditController extends AbstractController
{
    private $tpl_maintitle;
    private $tpl_subtitle;

    public $form;

    public function __construct()
    {
        $this->tpl_maintitle = '会員管理';
        $this->tpl_subtitle = '会員登録';
    }

    public function index(Application $app, $customerId = null)
    {

        $Customer = $this->getCustomer($app, $customerId);
        if ( $Customer === null ) {
            throw new HttpException\NotFoundHttpException("※ 会員ID：$customerId が見つかりません。");
        }

        //TODO: 購入処理ができてから実装する
        $Order = $this->getOrder($app, $Customer);


        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('customer', $Customer);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                if ( $Customer->getPassword() <> $app['config']['default_password']) {
                    if (!$Customer->getSalt()) {
                        $generator = new SecureRandom();
                        $Customer->setSalt(bin2hex($generator->nextBytes(5)));
                        // TODO: 冗長なので見直す
                        $Customer->setSecretKey($this->getUniqueSecretKey($app));
                    }

                    // secure password
                    $encoder = $app['security.encoder_factory']->getEncoder($Customer);
                    $encoded_password = $encoder->encodePassword($Customer->getPassword(), $Customer->getSalt());
                    $Customer->setPassword($encoded_password);
                }
                $app['orm.em']->persist($Customer);
                $app['orm.em']->flush();
                $app['session']->getFlashBag()->add('customer.complete', 'admin.register.complete');
            }
        }

        return $app['view']->render('Admin/Customer/Edit.twig', array(
            'title' => $this->tpl_maintitle,
            'subtitle'  => $this->tpl_subtitle,
            'customerId' => $customerId,
            'Order' => $Order,
            'form' => $form->createView(),
        ));
    }

    /**
     * getCustomer
     *
     * 新規か編集かにあわせてCustomerObjectを返す
     *
     * @param Application $app
     * @param $customerId
     * @return mixed
     */
    private function getCustomer(Application $app, $customerId) {

        if ( $customerId ) {
            $customer = $app['orm.em']->getRepository('Eccube\\Entity\\Customer')
                ->findOneBy(array(
                        'id' => $customerId,
                        'del_flg' => 0,
                    )
                );
            $customer->setPassword($app['config']['default_password']);

            return $customer;

        } else {
            return $app['eccube.repository.customer']->newCustomer();
        }
    }

    /**
     * 購入履歴を取得する
     * TODO: 購入が実装できてからちゃんと実装する
     *
     * @param Application $app
     * @param \Eccube\Entity\Customer $Customer
     * @return mixed
     */
    private function getOrder(Application $app, \Eccube\Entity\Customer $Customer) {

        if ($Customer->getId() > 0 ) {

            $Order = $app['orm.em']->getRepository('Eccube\\Entity\\Order')
                ->findBy(array(
                        'Customer' => $Customer,
                        'del_flg' => 0,
                    )
                );
            return $Order;
        } else {
            return null;
        }
    }

    private function getUniqueSecretKey($app)
    {
        $unique = md5(uniqid(rand(), 1));
        $customer = $app['eccube.repository.customer']->findBy(array(
            'secret_key' => $unique,
        ));
        if (count($customer) == 0) {
            return $unique;
        } else {
            return $this->getUniqueSecretKey($app);
        }
    }
}