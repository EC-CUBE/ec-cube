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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\ProductClass;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
use Eccube\Form\Type\AddCartType;
use Eccube\Form\Type\Master\ProductListMaxType;
use Eccube\Form\Type\Master\ProductListOrderByType;
use Eccube\Form\Type\SearchProductType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CustomerFavoriteProductRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Service\CartService;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Component
 * @Route(service=ProductController::class)
 */
class ProductController
{
    /**
     * @Inject("eccube.purchase.flow.cart")
     * @var PurchaseFlow
     */
    protected $purchaseFlow;

    /**
     * @Inject("session")
     * @var Session
     */
    protected $session;

    /**
     * @Inject(CustomerFavoriteProductRepository::class)
     * @var CustomerFavoriteProductRepository
     */
    protected $customerFavoriteProductRepository;

    /**
     * @Inject(CartService::class)
     * @var CartService
     */
    protected $cartService;

    /**
     * @Inject(ProductRepository::class)
     * @var ProductRepository
     */
    protected $productRepository;

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
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject(BaseInfoRepository::class)
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    private $title;

    public function __construct()
    {
        $this->title = '';
    }

    /**
     * 商品一覧画面.
     *
     * @Route("/products/list", name="product_list")
     * @Template("Product/list.twig")
     */
    public function index(Application $app, Request $request)
    {
        $BaseInfo = $this->baseInfoRepository->get();

        // Doctrine SQLFilter
        if ($BaseInfo->getNostockHidden() === Constant::ENABLED) {
            $this->entityManager->getFilters()->enable('nostock_hidden');
        }

        // handleRequestは空のqueryの場合は無視するため
        if ($request->getMethod() === 'GET') {
            $request->query->set('pageno', $request->query->get('pageno', ''));
        }

        // searchForm
        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $this->formFactory->createNamedBuilder('', SearchProductType::class);
        $builder->setAttribute('freeze', true);
        $builder->setAttribute('freeze_display_text', false);
        if ($request->getMethod() === 'GET') {
            $builder->setMethod('GET');
        }

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_INDEX_INITIALIZE, $event);

        /* @var $searchForm \Symfony\Component\Form\FormInterface */
        $searchForm = $builder->getForm();

        $searchForm->handleRequest($request);

        // paginator
        $searchData = $searchForm->getData();
        $qb = $this->productRepository->getQueryBuilderBySearchData($searchData);

        $event = new EventArgs(
            array(
                'searchData' => $searchData,
                'qb' => $qb,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_INDEX_SEARCH, $event);
        $searchData = $event->getArgument('searchData');

        $pagination = $app['paginator']()->paginate(
            $qb,
            !empty($searchData['pageno']) ? $searchData['pageno'] : 1,
            $searchData['disp_number']->getId()
        );

        // addCart form
        $forms = array();
        foreach ($pagination as $Product) {
            /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
            $builder = $this->formFactory->createNamedBuilder(
                '',
                AddCartType::class,
                null,
                array(
                    'product' => $Product,
                    'allow_extra_fields' => true,
                )
            );
            $addCartForm = $builder->getForm();

            if ($request->getMethod() === 'POST' && (string)$Product->getId() === $request->get('product_id')) {
                $addCartForm->handleRequest($request);

                if ($addCartForm->isValid()) {
                    $addCartData = $addCartForm->getData();

                    try {
                        $this->cartService->addProduct(
                            $addCartData['product_class_id'],
                            $addCartData['quantity']
                        )->save();
                    } catch (CartException $e) {
                        $app->addRequestError($e->getMessage());
                    }

                    $event = new EventArgs(
                        array(
                            'form' => $addCartForm,
                            'Product' => $Product,
                        ),
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_INDEX_COMPLETE, $event);

                    if ($event->getResponse() !== null) {
                        return $event->getResponse();
                    }

                    return $app->redirect($app->url('cart'));
                }
            }

            $forms[$Product->getId()] = $addCartForm->createView();
        }

        // 表示件数
        $builder = $this->formFactory->createNamedBuilder(
            'disp_number',
            ProductListMaxType::class,
            null,
            array(
                'required' => false,
                'label' => '表示件数',
                'allow_extra_fields' => true,
            )
        );
        if ($request->getMethod() === 'GET') {
            $builder->setMethod('GET');
        }

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_INDEX_DISP, $event);

        $dispNumberForm = $builder->getForm();

        $dispNumberForm->handleRequest($request);

        // ソート順
        $builder = $this->formFactory->createNamedBuilder(
            'orderby',
            ProductListOrderByType::class,
            null,
            array(
                'required' => false,
                'label' => '表示順',
                'allow_extra_fields' => true,
            )
        );
        if ($request->getMethod() === 'GET') {
            $builder->setMethod('GET');
        }

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_INDEX_ORDER, $event);

        $orderByForm = $builder->getForm();

        $orderByForm->handleRequest($request);

        $Category = $searchForm->get('category_id')->getData();

        return [
            'subtitle' => $this->getPageTitle($searchData),
            'pagination' => $pagination,
            'search_form' => $searchForm->createView(),
            'disp_number_form' => $dispNumberForm->createView(),
            'order_by_form' => $orderByForm->createView(),
            'forms' => $forms,
            'Category' => $Category,
        ];
    }

    /**
     * 商品詳細画面.
     *
     * @Route("/products/detail/{id}", name="product_detail", requirements={"id":"\d+"})
     * @Template("Product/detail.twig")
     */
    public function detail(Application $app, Request $request, $id)
    {
        $BaseInfo = $this->baseInfoRepository->get();
        if ($BaseInfo->getNostockHidden() === Constant::ENABLED) {
            $this->entityManager->getFilters()->enable('nostock_hidden');
        }

        /* @var $Product \Eccube\Entity\Product */
        $Product = $this->productRepository->get($id);
        if (!$request->getSession()->has('_security_admin') && $Product->getStatus()->getId() !== 1) {
            throw new NotFoundHttpException();
        }
        if (count($Product->getProductClasses()) < 1) {
            throw new NotFoundHttpException();
        }

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $this->formFactory->createNamedBuilder(
            '',
            AddCartType::class,
            null,
            array(
                'product' => $Product,
                'id_add_product_id' => false,
            )
        );

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Product' => $Product,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_DETAIL_INITIALIZE, $event);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $addCartData = $form->getData();
                if ($addCartData['mode'] === 'add_favorite') {
                    if ($app->isGranted('ROLE_USER')) {
                        $Customer = $app->user();
                        $this->customerFavoriteProductRepository->addFavorite($Customer, $Product);
                        $this->session->getFlashBag()->set('product_detail.just_added_favorite', $Product->getId());

                        $event = new EventArgs(
                            array(
                                'form' => $form,
                                'Product' => $Product,
                            ),
                            $request
                        );
                        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_DETAIL_FAVORITE, $event);

                        if ($event->getResponse() !== null) {
                            return $event->getResponse();
                        }

                        return $app->redirect($app->url('product_detail', array('id' => $Product->getId())));
                    } else {
                        // 非会員の場合、ログイン画面を表示
                        //  ログイン後の画面遷移先を設定
                        $app->setLoginTargetPath($app->url('product_detail', array('id' => $Product->getId())));
                        $this->session->getFlashBag()->set('eccube.add.favorite', true);

                        return $app->redirect($app->url('mypage_login'));
                    }
                } elseif ($addCartData['mode'] === 'add_cart') {

                    log_info(
                        'カート追加処理開始',
                        array(
                            'product_id' => $Product->getId(),
                            'product_class_id' => $addCartData['product_class_id'],
                            'quantity' => $addCartData['quantity'],
                        )
                    );

                    // カートを取得
                    $Cart = $this->cartService->getCart();

                    // カートへ追加
                    $this->cartService->addProduct($addCartData['product_class_id'], $addCartData['quantity']);

                    // 明細の正規化
                    $flow = $this->purchaseFlow;
                    $result = $flow->calculate($Cart, $app['eccube.purchase.context']());

                    // 復旧不可のエラーが発生した場合は追加した明細を削除.
                    if ($result->hasError()) {
                        $Cart->removeCartItemByIdentifier(ProductClass::class, $addCartData['product_class_id']);
                        foreach ($result->getErrors() as $error) {
                            $app->addRequestError($error->getMessage());
                        }
                    }

                    foreach ($result->getWarning() as $warning) {
                        $app->addRequestError($warning->getMessage());
                    }

                    $this->cartService->save();

                    log_info(
                        'カート追加処理完了',
                        array(
                            'product_id' => $Product->getId(),
                            'product_class_id' => $addCartData['product_class_id'],
                            'quantity' => $addCartData['quantity'],
                        )
                    );

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'Product' => $Product,
                        ),
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_DETAIL_COMPLETE, $event);

                    if ($event->getResponse() !== null) {
                        return $event->getResponse();
                    }

                    return $app->redirect($app->url('cart'));
                }
            }
        } else {
            $addFavorite = $this->session->getFlashBag()->get('eccube.add.favorite');
            if (!empty($addFavorite)) {
                // お気に入り登録時にログインされていない場合、ログイン後にお気に入り追加処理を行う
                if ($app->isGranted('ROLE_USER')) {
                    $Customer = $app->user();
                    $this->customerFavoriteProductRepository->addFavorite($Customer, $Product);
                    $this->session->getFlashBag()->set('product_detail.just_added_favorite', $Product->getId());
                }
            }
        }

        $is_favorite = false;
        if ($app->isGranted('ROLE_USER')) {
            $Customer = $app->user();
            $is_favorite = $this->customerFavoriteProductRepository->isFavorite($Customer, $Product);
        }

        return [
            'title' => $this->title,
            'subtitle' => $Product->getName(),
            'form' => $form->createView(),
            'Product' => $Product,
            'is_favorite' => $is_favorite,
        ];
    }

    /**
     * ページタイトルの設定
     *
     * @param  null|array $searchData
     * @return str
     */
    private function getPageTitle($searchData)
    {
        if (isset($searchData['name']) && !empty($searchData['name'])) {
            return '検索結果';
        } elseif (isset($searchData['category_id']) && $searchData['category_id']) {
            return $searchData['category_id']->getName();
        } else {
            return '全商品';
        }
    }
}
