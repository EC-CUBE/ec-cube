<?php

namespace Eccube\Controller;

use Eccube\Application;
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
                    $app['eccube.service.cart']->addProduct($addCartData['product_class_id'], $addCartData['quantity']);

                    return $app->redirect($app['url_generator']->generate('cart'));
                }
            }

            $forms[$Product->getId()] = $addCartForm->createView();
        }

        // 
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

        return $app['twig']->render('Product/list.twig', array(
            'subtitle' => $this->getPageTitle($searchData),
            'pagination' => $pagination,
            'search_form' => $searchForm->createView(),
            'disp_number_form' => $dispNumberForm->createView(),
            'forms' => $forms,
        ));
    }

    public function detail(Application $app, Request $request, $productId)
    {
        if ($app['config']['nostock_hidden']) {
            $app['orm.em']->getFilters()->enable('nostock_hidden');
        }

        /* @var $product \Eccube\Entity\Product */
        $Product = $app['eccube.repository.product']->get($productId);
        if ($Product->getStatus() !== 1) {
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
                    $app['eccube.repository.customer_favorite_product']->addFavorite($Product);
                    $app['session']->getFlashBag()->set('just_added_favorite', $Product->getId());

                    return $app->redirect($app['url_generator']->generate('product_detail', array('productId' => $Product->getId())));
                } else {
                    $app['eccube.service.cart']->addProduct($addCartData['product_class_id'], $addCartData['quantity'])->save();

                    return $app->redirect($app['url_generator']->generate('cart'));
                }
            }
        }

        return $app['twig']->render('Product/detail.twig', array(
            'title' => $this->title,
            'form' => $form->createView(),
            'Product' => $Product,
            'is_favorite' => $app['eccube.repository.customer_favorite_product']->isFavorite($Product),
        ));
    }

    /**
     * ページタイトルの設定
     *
     * @param null|array $searchData
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
