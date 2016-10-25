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
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MypageController extends AbstractController
{
    /**
     * ログイン画面.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function login(Application $app, Request $request)
    {
        if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
            log_info('認証済のためログイン処理をスキップ');

            return $app->redirect($app->url('mypage'));
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $builder = $app['form.factory']
            ->createNamedBuilder('', 'customer_login');

        if ($app->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $Customer = $app->user();
            if ($Customer) {
                $builder->get('login_email')->setData($Customer->getEmail());
            }
        }

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_LOGIN_INITIALIZE, $event);

        $form = $builder->getForm();

        return $app->render('Mypage/login.twig', array(
            'error' => $app['security.last_error']($request),
            'form' => $form->createView(),
        ));
    }

    /**
     * マイページ
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $Customer = $app['user'];

        /* @var $softDeleteFilter \Eccube\Doctrine\Filter\SoftDeleteFilter */
        $softDeleteFilter = $app['orm.em']->getFilters()->getFilter('soft_delete');
        $softDeleteFilter->setExcludes(array(
            'Eccube\Entity\ProductClass',
        ));

        // 購入処理中/決済処理中ステータスの受注を非表示にする.
        $app['orm.em']
            ->getFilters()
            ->enable('incomplete_order_status_hidden');

        // paginator
        $qb = $app['eccube.repository.order']->getQueryBuilderByCustomer($Customer);

        $event = new EventArgs(
            array(
                'qb' => $qb,
                'Customer' => $Customer,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_INDEX_SEARCH, $event);

        $pagination = $app['paginator']()->paginate(
            $qb,
            $request->get('pageno', 1),
            $app['config']['search_pmax']
        );

        return $app->render('Mypage/index.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * 購入履歴詳細を表示する.
     *
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function history(Application $app, Request $request, $id)
    {
        /* @var $softDeleteFilter \Eccube\Doctrine\Filter\SoftDeleteFilter */
        $softDeleteFilter = $app['orm.em']->getFilters()->getFilter('soft_delete');
        $softDeleteFilter->setExcludes(array(
            'Eccube\Entity\ProductClass',
        ));

        $app['orm.em']->getFilters()->enable('incomplete_order_status_hidden');
        $Order = $app['eccube.repository.order']->findOneBy(array(
            'id' => $id,
            'Customer' => $app->user(),
        ));
        
        $event = new EventArgs(
            array(
                'Order' => $Order,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_HISTORY_INITIALIZE, $event);

        $Order = $event->getArgument('Order');

        if (!$Order) {
            throw new NotFoundHttpException();
        }

        return $app->render('Mypage/history.twig', array(
            'Order' => $Order,
        ));
    }

    /**
     * 再購入を行う.
     *
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function order(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        log_info('再注文開始', array($id));

        $Customer = $app->user();

        /* @var $Order \Eccube\Entity\Order */
        $Order = $app['eccube.repository.order']->findOneBy(array(
            'id' => $id,
            'Customer' => $Customer,
        ));

        $event = new EventArgs(
            array(
                'Order' => $Order,
                'Customer' => $Customer,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_ORDER_INITIALIZE, $event);

        if (!$Order) {
            log_info('対象の注文が見つかりません', array($id));
            throw new NotFoundHttpException();
        }

        foreach ($Order->getOrderDetails() as $OrderDetail) {
            try {
                if ($OrderDetail->getProduct() &&
                    $OrderDetail->getProductClass()) {
                    $app['eccube.service.cart']->addProduct($OrderDetail->getProductClass()->getId(), $OrderDetail->getQuantity())->save();
                } else {
                    log_info($app->trans('cart.product.delete'), array($id));
                    $app->addRequestError('cart.product.delete');
                }
            } catch (CartException $e) {
                log_info($e->getMessage(), array($id));
                $app->addRequestError($e->getMessage());
            }
        }

        $event = new EventArgs(
            array(
                'Order' => $Order,
                'Customer' => $Customer,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_ORDER_COMPLETE, $event);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        log_info('再注文完了', array($id));

        return $app->redirect($app->url('cart'));
    }

    /**
     * お気に入り商品を表示する.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function favorite(Application $app, Request $request)
    {
        $BaseInfo = $app['eccube.repository.base_info']->get();

        if ($BaseInfo->getOptionFavoriteProduct() == Constant::ENABLED) {
            $Customer = $app->user();

            // paginator
            $qb = $app['eccube.repository.customer_favorite_product']->getQueryBuilderByCustomer($Customer);

            $event = new EventArgs(
                array(
                    'qb' => $qb,
                    'Customer' => $Customer,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_FAVORITE_SEARCH, $event);

            $pagination = $app['paginator']()->paginate(
                $qb,
                $request->get('pageno', 1),
                $app['config']['search_pmax'],
                array('wrap-queries' => true)
            );

            return $app->render('Mypage/favorite.twig', array(
                'pagination' => $pagination,
            ));
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * お気に入り商品を削除する.
     *
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $Customer = $app->user();

        $Product = $app['eccube.repository.product']->find($id);

        $event = new EventArgs(
            array(
                'Customer' => $Customer,
                'Product' => $Product,
            ), $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_DELETE_INITIALIZE, $event);

        if ($Product) {
            log_info('お気に入り商品削除開始');

            $app['eccube.repository.customer_favorite_product']->deleteFavorite($Customer, $Product);

            $event = new EventArgs(
                array(
                    'Customer' => $Customer,
                    'Product' => $Product,
                ), $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_DELETE_COMPLETE, $event);

            log_info('お気に入り商品削除完了');
        }

        return $app->redirect($app->url('mypage_favorite'));
    }
}
