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
use Symfony\Component\HttpFoundation\Request;

class CustomerController
{
    public $title;

    public function __construct()
    {
    }

    public function index(Application $app)
    {

        $Customers = array();

        $form = $app['form.factory']
            ->createBuilder('admin_search_customer')
            ->getForm();

        $showResult = false;

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);

            if ($form->isValid()) {
                $showResult = true;

                $qb = $app['orm.em']
                    ->getRepository('Eccube\Entity\Customer')
                    ->getQueryBuilderBySearchData($form->getData());
                $query = $qb->getQuery();
                $Customers = $query->getResult();
            }

        }

        return $app['view']->render('Admin/Customer/index.twig', array(
            'form' => $form->createView(),
            'showResult' => $showResult,
            'Customers' => $Customers,
        ));
    }

    public function resend(Application $app, $id)
    {
        $Customer = $app['orm.em']->getRepository('Eccube\Entity\Customer')
            ->find($id);

        if ($Customer) {
            $message = $app['mail.message']
                ->setFrom(array('sample@example.com'))
                ->setTo(array($Customer->getEmail()))
                ->setBcc($app['config']['mail_cc'])
                ->setSubject('[EC-CUBE3] 会員登録のご確認')
                ->setBody($app['view']->render('Mail/entry_confirm.twig', array(
                    'customer' => $Customer
                )));
            $app['mailer']->send($message);

            $app['session']->getFlashBag()->add('admin.customer.complete', 'admin.customer.resend.complete');
        }

        return $this->index($app);
    }

    public function delete(Application $app, $id)
    {
        $Customer = $app['orm.em']->getRepository('Eccube\Entity\Customer')
            ->find($id);

        if ($Customer) {
            $Customer->setDelFlg(1);
            $app['orm.em']->persist($Customer);
            $app['orm.em']->flush();

            $app['session']->getFlashBag()->add('admin.customer.complete', 'admin.customer.delete.complete');
        }

        $url = $app['url_generator']->generate('admin_customer');

        return $this->index($app);
    }
}
