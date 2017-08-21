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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
use Eccube\Form\Type\Front\CustomerLoginType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CustomerFavoriteProductRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Service\CartService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Component
 * @Route(service=MypageController::class)
 */
class MypageController extends AbstractController
{
    /**
     * @Inject(ProductRepository::class)
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @Inject(CustomerFavoriteProductRepository::class)
     * @var CustomerFavoriteProductRepository
     */
    protected $customerFavoriteProductRepository;

    /**
     * @Inject(BaseInfoRepository::class)
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    /**
     * @Inject(CartService::class)
     * @var CartService
     */
    protected $cartService;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject(OrderRepository::class)
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * ログイン画面.
     *
     * @Route("/mypage/login", name="mypage_login")
     * @Template("Mypage/login.twig")
     */
    public function login(Application $app, Request $request)
    {
        if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
            log_info('認証済のためログイン処理をスキップ');

            return $app->redirect($app->url('mypage'));
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $builder = $this->formFactory
            ->createNamedBuilder('', CustomerLoginType::class);

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
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_LOGIN_INITIALIZE, $event);

        $form = $builder->getForm();

        return [
            'error' => $app['security.last_error']($request),
            'form' => $form->createView(),
        ];
    }

    /**
     * マイページ.
     *
     * @Route("/mypage", name="mypage")
     * @Template("Mypage/index.twig")
     */
    public function index(Application $app, Request $request)
    {
        $Customer = $app['user'];

        /* @var $softDeleteFilter \Eccube\Doctrine\Filter\SoftDeleteFilter */
        $softDeleteFilter = $this->entityManager->getFilters()->getFilter('soft_delete');
        $softDeleteFilter->setExcludes(
            array(
                'Eccube\Entity\ProductClass',
            )
        );

        // 購入処理中/決済処理中ステータスの受注を非表示にする.
        $this->entityManager
            ->getFilters()
            ->enable('incomplete_order_status_hidden');

        // paginator
        $qb = $this->orderRepository->getQueryBuilderByCustomer($Customer);

        $event = new EventArgs(
            array(
                'qb' => $qb,
                'Customer' => $Customer,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_INDEX_SEARCH, $event);

        $pagination = $app['paginator']()->paginate(
            $qb,
            $request->get('pageno', 1),
            $this->appConfig['search_pmax']
        );

        return [
            'pagination' => $pagination,
        ];
    }

    /**
     * 購入履歴詳細を表示する.
     *
     * @Route("/mypage/history/{id}", name="mypage_history", requirements={"id":"\d+"})
     * @Template("Mypage/history.twig")
     */
    public function history(Application $app, Request $request, $id)
    {
        /* @var $softDeleteFilter \Eccube\Doctrine\Filter\SoftDeleteFilter */
        $softDeleteFilter = $this->entityManager->getFilters()->getFilter('soft_delete');
        $softDeleteFilter->setExcludes(
            array(
                'Eccube\Entity\ProductClass',
            )
        );

        $this->entityManager->getFilters()->enable('incomplete_order_status_hidden');
        $Order = $this->orderRepository->findOneBy(
            array(
                'id' => $id,
                'Customer' => $app->user(),
            )
        );

        $event = new EventArgs(
            array(
                'Order' => $Order,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_HISTORY_INITIALIZE, $event);

        $Order = $event->getArgument('Order');

        if (!$Order) {
            throw new NotFoundHttpException();
        }

        return [
            'Order' => $Order,
        ];
    }

    /**
     * 再購入を行う.
     *
     * @Route("/mypage/order/{id}", name="mypage_order", requirements={"id":"\d+"})
     * @Method("PUT")
     */
    public function order(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        log_info('再注文開始', array($id));

        $Customer = $app->user();

        /* @var $Order \Eccube\Entity\Order */
        $Order = $this->orderRepository->findOneBy(
            array(
                'id' => $id,
                'Customer' => $Customer,
            )
        );

        $event = new EventArgs(
            array(
                'Order' => $Order,
                'Customer' => $Customer,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_ORDER_INITIALIZE, $event);

        if (!$Order) {
            log_info('対象の注文が見つかりません', array($id));
            throw new NotFoundHttpException();
        }

        foreach ($Order->getOrderDetails() as $OrderDetail) {
            try {
                if ($OrderDetail->getProduct() &&
                    $OrderDetail->getProductClass()
                ) {
                    $this->cartService->addProduct(
                        $OrderDetail->getProductClass()->getId(),
                        $OrderDetail->getQuantity()
                    )->save();
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
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_ORDER_COMPLETE, $event);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        log_info('再注文完了', array($id));

        return $app->redirect($app->url('cart'));
    }

    /**
     * お気に入り商品を表示する.
     *
     * @Route("/mypage/favorite", name="mypage_favorite")
     * @Template("Mypage/favorite.twig")
     */
    public function favorite(Application $app, Request $request)
    {
        $BaseInfo = $this->baseInfoRepository->get();

        if ($BaseInfo->getOptionFavoriteProduct() == Constant::DISABLED) {
            throw new NotFoundHttpException();
        }
        $Customer = $app->user();

        // paginator
        $qb = $this->customerFavoriteProductRepository->getQueryBuilderByCustomer($Customer);

        $event = new EventArgs(
            array(
                'qb' => $qb,
                'Customer' => $Customer,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_FAVORITE_SEARCH, $event);

        $pagination = $app['paginator']()->paginate(
            $qb,
            $request->get('pageno', 1),
            $this->appConfig['search_pmax'],
            array('wrap-queries' => true)
        );

        return [
            'pagination' => $pagination,
        ];
    }

    /**
     * お気に入り商品を削除する.
     *
     * @Route("/mypage/favorite/{id}/delete", name="mypage_favorite_delete", requirements={"id":"\d+"})
     * @Method("DELETE")
     */
    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $Customer = $app->user();

        $Product = $this->productRepository->find($id);

        $event = new EventArgs(
            array(
                'Customer' => $Customer,
                'Product' => $Product,
            ), $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_DELETE_INITIALIZE, $event);

        if ($Product) {
            log_info('お気に入り商品削除開始');

            $this->customerFavoriteProductRepository->deleteFavorite($Customer, $Product);

            $event = new EventArgs(
                array(
                    'Customer' => $Customer,
                    'Product' => $Product,
                ), $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_DELETE_COMPLETE, $event);

            log_info('お気に入り商品削除完了');
        }

        return $app->redirect($app->url('mypage_favorite'));
    }
}
