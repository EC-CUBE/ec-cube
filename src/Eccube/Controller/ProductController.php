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

use Eccube\Application;
use Eccube\Exception\CartException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController
{

    private $title;

    public function __construct()
    {
        $this->title = '';
    }

    public function index(Application $app, Request $request)
    {
        // Doctrine SQLFilter
        if ($app['config']['nostock_hidden']) {
            $app['orm.em']->getFilters()->enable('nostock_hidden');
        }

        // handleRequestは空のqueryの場合は無視するため
        if ($request->getMethod() === 'GET') {
            $request->query->set('pageno', $request->query->get('pageno', ''));
        }

        // searchForm
        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createNamedBuilder('', 'search_product');
        $builder->setAttribute('freeze', true);
        $builder->setAttribute('freeze_display_text', false);
        if ($request->getMethod() === 'GET') {
            $builder->setMethod('GET');
        }
        /* @var $searchForm \Symfony\Component\Form\FormInterface */
        $searchForm = $builder->getForm();
        $searchForm->handleRequest($request);

        // paginator
        $searchData = $searchForm->getData();
        $qb = $app['eccube.repository.product']->getQueryBuilderBySearchData($searchData);
        $pagination = $app['paginator']()->paginate(
            $qb,
            !empty($searchData['pageno']) ? $searchData['pageno'] : 1,
            $searchData['disp_number']->getId()
        );

        // addCart form
        $forms = array();
        foreach ($pagination as $Product) {
            /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
            $builder = $app['form.factory']->createNamedBuilder('', 'add_cart', null, array(
                'product' => $Product,
                'allow_extra_fields' => true,
            ));
            $addCartForm = $builder->getForm();

            if ($request->getMethod() === 'POST' && (string) $Product->getId() === $request->get('product_id')) {
                $addCartForm->handleRequest($request);

                if ($addCartForm->isValid()) {
                    $addCartData = $addCartForm->getData();
                    $app['eccube.service.cart']->addProduct($addCartData['product_class_id'], $addCartData['quantity'])->save();

                    return $app->redirect($app['url_generator']->generate('cart'));
                }
            }

            $forms[$Product->getId()] = $addCartForm->createView();
        }

        // 表示件数
        $builder = $app['form.factory']->createNamedBuilder('disp_number', 'product_list_max', null, array(
            'empty_data' => null,
            'required' => false,
            'label' => '表示件数',
            'allow_extra_fields' => true,
        ));
        if ($request->getMethod() === 'GET') {
            $builder->setMethod('GET');
        }
        $dispNumberForm = $builder->getForm();
        $dispNumberForm->handleRequest($request);

        // ソート順
        $builder = $app['form.factory']->createNamedBuilder('orderby', 'product_list_order_by', null, array(
            'empty_data' => null,
            'required' => false,
            'label' => '表示順',
            'allow_extra_fields' => true,
        ));
        if ($request->getMethod() === 'GET') {
            $builder->setMethod('GET');
        }
        $orderByForm = $builder->getForm();
        $orderByForm->handleRequest($request);

        $Category = $searchForm->get('category_id')->getData();

        return $app['twig']->render('Product/list.twig', array(
            'subtitle' => $this->getPageTitle($searchData),
            'pagination' => $pagination,
            'search_form' => $searchForm->createView(),
            'disp_number_form' => $dispNumberForm->createView(),
            'order_by_form' => $orderByForm->createView(),
            'forms' => $forms,
            'Category' => $Category,
        ));
    }

    public function detail(Application $app, Request $request, $id)
    {
        if ($app['config']['nostock_hidden']) {
            $app['orm.em']->getFilters()->enable('nostock_hidden');
        }

        /* @var $Product \Eccube\Entity\Product */
        $Product = $app['eccube.repository.product']->get($id);
        if (!$request->getSession()->has('_security_admin') && $Product->getStatus()->getId() !== 1) {
            throw new NotFoundHttpException();
        }

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createNamedBuilder('', 'add_cart', null, array(
            'product' => $Product,
            'id_add_product_id' => false,
        ));
        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $addCartData = $form->getData();
                if ($addCartData['mode'] === 'add_favorite') {
                    if ($app['security']->isGranted('ROLE_USER')) {
                        $Customer = $app['security']->getToken()->getUser();
                        $app['eccube.repository.customer_favorite_product']->addFavorite($Customer, $Product);
                        $app['session']->getFlashBag()->set('product_detail.just_added_favorite', $Product->getId());
                    }

                    return $app->redirect($app->url('product_detail', array('productId' => $Product->getId())));
                } else {
                    try {
                        $app['eccube.service.cart']->addProduct($addCartData['product_class_id'], $addCartData['quantity'])->save();
                    } catch (CartException $e) {
                        $app->addRequestError($e->getMessage());
                    }

                    return $app->redirect($app->url('cart'));
                }
            }
        }

        if ($app['security']->isGranted('ROLE_USER')) {
            $Customer = $app['security']->getToken()->getUser();
            $is_favorite = $app['eccube.repository.customer_favorite_product']->isFavorite($Customer, $Product);
        } else {
            $is_favorite = false;
        }

        return $app->render('Product/detail.twig', array(
            'title' => $this->title,
            'subtitle' => $Product->getName(),
            'form' => $form->createView(),
            'Product' => $Product,
            'is_favorite' => $is_favorite,
        ));
    }

    /**
     * ページタイトルの設定
     *
     * @param  null|array $searchData
     * @return str
     */
    private function getPageTitle($searchData)
    {
        if (isset($searchData['mode']) && $searchData['mode'] === 'search') {
            return '検索結果';
        } elseif (isset($searchData['category_id']) && $searchData['category_id']) {
            return $searchData['category_id']->getName();
        } else {
            return '全商品';
        }
    }
}
