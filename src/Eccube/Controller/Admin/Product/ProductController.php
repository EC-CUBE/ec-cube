<?php

namespace Eccube\Controller\Admin\Product;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
 
class ProductController
{
    public function index(Application $app, Request $request)
    {
        $searchForm = $app['form.factory']
            ->createBuilder('admin_search_product')
            ->getForm();

        $searchForm->handleRequest($request);
        if ($searchForm->isValid()) {
            $searchData = $searchForm->getData();
        } else {
            $searchData = array();
        }

        // paginator
        $qb = $app['eccube.repository.product']->getQueryBuilderBySearchDataForAdmin($searchData);
        $pagination = $app['paginator']()->paginate(
            $qb,
            !empty($searchData['pageno']) ? $searchData['pageno'] : 1,
            10 // TODO
        );

        return $app['view']->render('Admin/Product/index.twig', array(
            'maintitle' => '商品管理',
            'subtitle' => '商品マスター',
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
        ));
    }

    public function edit(Application $app, Request $request)
    {
        if ($request->get('product_id')) {
            $Product = $app['eccube.repository.product']->find($request->get('product_id'));
            if (!$Product) {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
            }
        } else {
            $Product = new \Eccube\Entity\Product();
        }

        $searchForm = $app['form.factory']
            ->createBuilder('admin_search_product')
            ->getForm();
        if ($request->getMethod() === 'POST') {
            $searchForm->handleRequest($request);
        }

        $form = $app['form.factory']
            ->createBuilder('admin_product', $Product)
            ->getForm();
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
        }

        return $app['view']->render('Admin/Product/product.twig', array(
            'maintitle' => '商品管理',
            'subtitle' => '商品登録',
            'form' => $form->createView(),
            'searchForm' => $searchForm->createView(),
        ));
    }

}