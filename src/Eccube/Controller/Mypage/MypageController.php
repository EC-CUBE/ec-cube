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


namespace Eccube\Controller\Mypage;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MypageController extends AbstractController
{
    private $title;

    public $form;

    public function __construct()
    {
        $this->title = 'マイページ';
    }

    public function login(Application $app, Request $request)
    {
        if ($app['security']->isGranted('ROLE_USER')) {
            return $app->redirect($app['url_generator']->generate('mypage'));
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $app['form.factory']
            ->createNamedBuilder('', 'customer_login')
            ->getForm();

        return $app['twig']->render('Mypage/login.twig', array(
            'title' => $this->title,
            'error' => $app['security.last_error']($request),
            'form' => $form->createView(),
        ));
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @return string
     */
    public function index(Application $app, Request $request)
    {
        $Customer = $app['user'];

        // paginator
        $qb = $app['eccube.repository.order']->getQueryBuilderByCustomer($Customer);
        $pagination = $app['paginator']()->paginate(
            $qb,
            $request->get('pageno', 1),
            $app['config']['search_pmax']
        );

        return $app['twig']->render('Mypage/index.twig', array(
            'title' => $this->title,
            'subtitle' => '購入履歴一覧',
            'mypageno' => 'index',
            'pagination' => $pagination,
        ));
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @return string
     */
    public function history(Application $app, Request $request, $orderId)
    {
        $Customer = $app['user'];

        $Order = $app['eccube.repository.order']->findOneBy(array(
            'id' => $orderId,
            'Customer' => $Customer,
        ));
        if (!$Order) {
            throw new NotFoundHttpException();
        }

        $BaseInfo = $app['eccube.repository.base_info']->get();

        return $app['twig']->render('Mypage/history.twig', array(
            'title' => $this->title,
            'subtitle' => '購入履歴一覧',
            'mypageno' => 'index',
            'Order' => $Order,
            'BaseInfo' => $BaseInfo,
        ));
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @return string
     */
    public function order(Application $app, Request $request)
    {
        $Customer = $app['user'];

        if ($request->getMethod() === 'POST') {
            $orderId = $request->get('order_id');
        } else {
        }

        /* @var $Order \Eccube\Entity\Order */
        $Order = $app['eccube.repository.order']->findOneBy(array(
            'id' => $orderId,
            'Customer' => $Customer,
        ));
        if (!$Order) {
            throw new NotFoundHttpException();
        }

        foreach ($Order->getOrderDetails() as $OrderDetail) {
            $app['eccube.service.cart']->addProduct($OrderDetail->getProductClass()->getId(), $OrderDetail->getQuantity());
        }
        $app['eccube.service.cart']->save();

        return $app->redirect($app['url_generator']->generate('cart'));
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @return string
     */
    public function mailView(Application $app, Request $request, $sendId)
    {
        $Customer = $app['user'];

        /* @var $MailHistory \Eccube\Entity\MailHistory */
        try {
            $MailHistory = $app['eccube.repository.mail_history']->getByCustomerAndId($Customer, $sendId);
        } catch (\Exception $e) {
            throw new NotFoundHttpException();
        }

        $BaseInfo = $app['eccube.repository.base_info']->get();

        return $app['twig']->render('Mypage/mail_view.twig', array(
            'title' => $this->title,
            'subtitle' => 'MYページ/メール履歴詳細',
            'MailHistory' => $MailHistory,
            'BaseInfo' => $BaseInfo,
        ));
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @return string
     */
    public function favorite(Application $app, Request $request)
    {
        $Customer = $app['user'];

        if ($request->getMethod() === 'POST' && $request->get('mode') === 'delete_favorite') {
            $Product = $app['eccube.repository.product']->get($request->get('product_id'));
            if ($Product) {
                $app['eccube.repository.customer_favorite_product']->deleteFavorite($Customer, $Product);
            }

            return $app->redirect($app['url_generator']->generate('mypage_favorite', array('page' => $request->get('pageno', 1))));
        }

        // paginator
        $qb = $app['eccube.repository.product']->getFavoriteProductQueryBuilderByCustomer($Customer);
        $pagination = $app['paginator']()->paginate(
            $qb,
            $request->get('pageno', 1),
            $app['config']['search_pmax']
        );

        return $app['twig']->render('Mypage/favorite.twig', array(
            'title' => $this->title,
            'subtitle' => 'お気に入り一覧',
            'mypageno' => 'favorite',
            'pagination' => $pagination,
        ));
    }
}
