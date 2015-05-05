<?php

namespace Eccube\Controller\Admin\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Application;
use Eccube\Entity\ClassName;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
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
        $ProductClasses = $this->getProductClasses($Product);
        $ClassName1 = null;
        $ClassName2 = null;

        // 規格を取得
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
            ))
            ->add('class2', 'entity', array(
                'class' => 'Eccube\Entity\ClassName',
                'property' => 'name',
                'empty_value' => '--',
            ))
            ->add('product_classes', 'collection', array(
                'type' => 'admin_product_class',
                'allow_add' => true,
                'allow_delete' => true,
            ))
        ;

        $form = $builder->getForm();
        $form->get('class1')->setData($ClassName1);
        $form->get('class2')->setData($ClassName2);
        $form->get('product_classes')->setData($ProductClasses);

        $ProductClassesOriginal = new ArrayCollection();
        foreach ($ProductClasses as $ProductClass) {
            $ProductClassesOriginal->add($ProductClass);
        }

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            switch ($request->get('mode')) {
                case 'edit':
                    if ($form->isValid()) {
                        $data = $form->getData();
                        // delete before insert
                        foreach ($ProductClassesOriginal as $ProductClass) {
                            if (!$data['product_classes']->contains($ProductClass)) {
                                $app['orm.em']->remove($ProductClass);
                            }
                        }
                        // persist
                        foreach ($data['product_classes'] as $ProductClass) {
                            $ProductClass->setDelFlg(0);
                            $ProductClass->setProduct($Product);
                            $app['orm.em']->persist($ProductClass);
                        }
                        $app['orm.em']->flush();
                        $app['session']->getFlashBag()->add('admin.success', 'admin.product.product_class.save.complete');
                        return $app->redirect($app['url_generator']->generate('admin_product_product_class', array('product_id' => $product_id)));
                    }
                    break;
                case 'disp':
                    $ClassName1 = $form->get('class1')->getData();
                    $ClassName2 = $form->get('class2')->getData();
                    $ProductClasses = $this->createProductClasses($app['orm.em'], $Product, $ClassName1, $ClassName2);

                    $form = $builder->getForm();
                    $form->get('class1')->setData($ClassName1);
                    $form->get('class2')->setData($ClassName2);
                    $form->get('product_classes')->setData($ProductClasses);
                case 'delete':

                default:
                    break;
            }
        }

        return $app['view']->render('Admin/Product/product_class.twig', array(
            'maintitle' => '商品管理',
            'subtitle' => '商品マスター',
            'form' => $form->createView(),
            'Product' => $Product
        ));
    }

    protected function createProductClasses(EntityManagerInterface $em, Product $Product, ClassName $ClassName1 = null, ClassName $ClassName2 = null)
    {
        $ClassCategories1 = array();
        if ($ClassName1) {
            $ClassCategories1 = $em->getRepository('\Eccube\Entity\ClassCategory')
                ->findBy(array('ClassName' => $ClassName1));
        }

        $ClassCategories2 = array();
        if ($ClassName2) {
            $ClassCategories2 = $em->getRepository('\Eccube\Entity\ClassCategory')
                ->findBy(array('ClassName' => $ClassName2));
        }

        $ProductClassess = array();
        foreach ($ClassCategories1 as $ClassCategory1) {
            if ($ClassCategories2) {
                foreach ($ClassCategories2 as $ClassCategory2) {
                    $ProductClass = $this->newProductClass($em);
                    $ProductClass->setClassCategory1($ClassCategory1);
                    $ProductClass->setClassCategory2($ClassCategory2);
                    $ProductClassess[] = $ProductClass;
                }
            } else {
                $ProductClass = $this->newProductClass($em);
                $ProductClass->setClassCategory1($ClassCategory1);
                $ProductClassess[] = $ProductClass;
            }

        }
        return $ProductClassess;
    }

    protected function newProductClass(EntityManagerInterface $em)
    {
        $ProductType = $em
            ->getRepository('\Eccube\Entity\Master\ProductType')
            ->find(1)
        ;
        $ProductClass = new ProductClass();
        $ProductClass
            ->setProductType($ProductType)
        ;
        return $ProductClass;
    }

    protected function getProductClasses(Product $Product)
    {
        /** @var $ProductClasses \Eccube\Entity\ProductClass[] */
        $ProductClasses = $Product->getProductClasses();
        foreach ($ProductClasses as $ProductClass) {
            $ClassCategory1 = $ProductClass->getClassCategory1();
            $ClassCategory2 = $ProductClass->getClassCategory2();
            if ( !$ClassCategory1 && !$ClassCategory2 ) {
                $ProductClasses->removeElement($ProductClass);
            }
        }
        return $ProductClasses;
    }
}
