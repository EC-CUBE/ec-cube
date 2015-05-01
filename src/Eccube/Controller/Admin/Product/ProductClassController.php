<?php

namespace Eccube\Controller\Admin\Product;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
 
class ProductClassController
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

        return $app['view']->render('Admin/Product/product_class.twig', array(
            'maintitle' => '商品管理',
            'subtitle' => '商品マスター',
            'searchForm' => $searchForm->createView(),
        ));
    }

    public function edit(Application $app, Request $request)
    {
        /* @var $softDeleteFilter \Eccube\Doctrine\Filter\SoftDeleteFilter */
        $softDeleteFilter = $app['orm.em']->getFilters()->getFilter('soft_delete');
        $softDeleteFilter->setExcludes(array(
            'Eccube\Entity\ProductClass'
        ));

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