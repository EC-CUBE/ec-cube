<?php

namespace Eccube\Controller\Admin\Product;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductClassController
{
    public function index(Application $app, Request $request, $product_id)
    {
        /** @var $Product \Eccube\Entity\Product */
        $Product = $app['orm.em']
            ->getRepository('\Eccube\Entity\Product')
            ->find($product_id);

        if (!$Product) {
            throw new NotFoundHttpException();
        }

        /** @var $ProductClasses \Eccube\Entity\ProductClass[] */
        $ProductClasses = $Product->getProductClasses();
        $ClassName1 = null;
        $ClassName2 = null;
        // 規格名を取得
        if (isset($ProductClasses[0])) {
            $ClassCategory1 = $ProductClasses[0]->getClassCategory1();
            if ($ClassCategory1) {
                $ClassName1 = $ClassCategory1->getClassName();
            }
            $ClassCategory2 = $ProductClasses[0]->getClassCategory2();
            if ($ClassCategory2) {
                $ClassName2 = $ClassCategory2->getClassName();
            }
        }

        $builder = $app['form.factory']->createBuilder();
        $builder
            ->add('class1', 'entity', array(
                'class' => 'Eccube\Entity\ClassName',
                'property' => 'name',
                'empty_value' => '--',
                'data' => $ClassName1,
            ))
            ->add('class2', 'entity', array(
                'class' => 'Eccube\Entity\ClassName',
                'property' => 'name',
                'empty_value' => '--',
                'data' => $ClassName2,
            ))
            ->add('product_classess', 'collection', array(
                'type' => 'admin_product_class',
                'data' => $ProductClasses,
            ))
        ;

        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                foreach ($data['product_classess'] as $ProdcutClass) {
                    $app['orm.em']->persist($ProdcutClass);
                }
                $app['orm.em']->flush();
                $app['session']->getFlashBag()->add('admin.success', 'admin.product.product_class.save.complete');

                return $app->redirect($app['url_generator']->generate('admin_product_product_class', array('product_id' => $product_id)));
            }
        }

        return $app['view']->render('Admin/Product/product_class.twig', array(
            'maintitle' => '商品管理',
            'subtitle' => '商品マスター',
            'form' => $form->createView(),
            'Product' => $Product
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
