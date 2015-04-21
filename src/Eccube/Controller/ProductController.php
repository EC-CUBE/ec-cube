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
        if ($app['config']['nostock_hidden']) {
            $app['orm.em']->getFilters()->enable('nostock_hidden');
        }

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createNamedBuilder('', 'search_product');
        $builder->setAttribute('freeze', true);
        $builder->setAttribute('freeze_display_text', false);
        /* @var $searchForm \Symfony\Component\Form\FormInterface */
        $searchForm = $builder->getForm();
        $searchForm->handleRequest($request);
        $searchData = $searchForm->getData();
        $qb = $app['eccube.repository.product']->getQueryBuilderBySearchData($searchData);

        $paginator = new \Knp\Component\Pager\Paginator;
        $pagination = $paginator->paginate(
            $qb,
            !empty($searchData['pageno']) ? $searchData['pageno'] : 1,
            !empty($searchData['disp_num']) ? $searchData['disp_num'] : 15
        );

        $forms = array();
        foreach ($pagination as $Product) {
            /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
            $builder = $app['form.factory']->createNamedBuilder('', 'add_cart', null, array(
                'product' => $Product,
            ));
            /* @var $searchForm \Symfony\Component\Form\FormInterface */
            $forms[$Product->getId()] = $builder->getForm()->createView();
        }

        return $app['twig']->render('Product/list.twig', array(
            'subtitle' => $this->getPageTitle($searchData),
            'pagination' => $pagination,
            'search_form' => $searchForm->createView(),
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
        ));
        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                
            }
        }

        return $app['twig']->render('Product/detail.twig', array(
            'title' => $this->title,
            'form' => $form->createView(),
            'Product' => $Product,
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
