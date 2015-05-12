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
    public function index(Application $app, Request $request, $id)
    {
        /** @var $Product \Eccube\Entity\Product */
        $Product = $app['orm.em']
            ->getRepository('\Eccube\Entity\Product')
            ->find($id);

        if (!$Product) {
            throw new NotFoundHttpException();
        }

        $ProductClassesOriginal = $this->getProductClassesOriginal($Product);
        $ProductClasses = $this->getProductClassesExcludeNonClass($Product);
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
            ->add('class_name1', 'entity', array(
                'class' => 'Eccube\Entity\ClassName',
                'property' => 'name',
                'empty_value' => '--',
            ))
            ->add('class_name2', 'entity', array(
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
        $form->get('class_name1')->setData($ClassName1);
        $form->get('class_name2')->setData($ClassName2);
        $form->get('product_classes')->setData($ProductClasses);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            switch ($request->get('mode')) {
                case 'edit':
                    if ($form->isValid()) {
                        $data = $form->getData();
                        // delete before insert
                        foreach ($ProductClassesOriginal as $ProductClass) {
                            if (!$data['product_classes']->contains($ProductClass)) {
                                if (!$ProductClass->hasClassCategory1() && !$ProductClass->hasClassCategory2()) {
                                    $ProductClass->setDelFlg(1);
                                    $app['orm.em']->persist($ProductClass);
                                } else {
                                    $app['orm.em']->remove($ProductClass);
                                }
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
                        return $app->redirect($app['url_generator']->generate('admin_product_product_class_edit', array('id' => $id)));
                    }
                    break;
                case 'disp':
                    $ClassName1 = $form->get('class_name1')->getData();
                    $ClassName2 = $form->get('class_name2')->getData();
                    $ProductClasses = $this->createProductClasses($app['orm.em'], $Product, $ClassName1, $ClassName2);

                    $form = $builder->getForm();
                    $form->get('class_name1')->setData($ClassName1);
                    $form->get('class_name2')->setData($ClassName2);
                    $form->get('product_classes')->setData($ProductClasses);
                    break;
                case 'delete':
                    foreach ($ProductClassesOriginal as $ProductClass) {
                        $app['orm.em']->remove($ProductClass);
                    }
                    /* @var $softDeleteFilter \Eccube\Doctrine\Filter\SoftDeleteFilter */
                    $softDeleteFilter = $app['orm.em']->getFilters()->getFilter('soft_delete');
                    $softDeleteFilter->setExcludes(array(
                        'Eccube\Entity\ProductClass'
                    ));

                    $ProductClassessDeleted = $app['orm.em']
                        ->getRepository('Eccube\Entity\ProductClass')
                        ->findBy(array(
                            'Product' => $Product,
                            'del_flg'  => 1
                        ))
                    ;
                    foreach ($ProductClassessDeleted as $ProductClass) {
                        $ProductClass->setDelFlg(0);
                        $app['orm.em']->persist($ProductClass);
                    }
                    $app['orm.em']->flush();
                    $app['session']->getFlashBag()->add('admin.success', 'admin.product.product_class.delete.complete');
                    return $app->redirect($app['url_generator']->generate('admin_product_product_class_edit', array('id' => $id)));
                    break;
                default:
                    break;
            }
        }

        return $app['view']->render('Admin/Product/product_class.twig', array(
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

        $ProductClasses = array();
        foreach ($ClassCategories1 as $ClassCategory1) {
            if ($ClassCategories2) {
                foreach ($ClassCategories2 as $ClassCategory2) {
                    $ProductClass = $this->newProductClass($em);
                    $ProductClass->setClassCategory1($ClassCategory1);
                    $ProductClass->setClassCategory2($ClassCategory2);
                    $ProductClasses[] = $ProductClass;
                }
            } else {
                $ProductClass = $this->newProductClass($em);
                $ProductClass->setClassCategory1($ClassCategory1);
                $ProductClasses[] = $ProductClass;
            }

        }
        return $ProductClasses;
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

    /**
     * 商品規格のコピーを取得.
     *
     * @see http://symfony.com/doc/current/cookbook/form/form_collections.html
     * @param Product $Product
     * @return \Eccube\Entity\ProductClass[]
     */
    protected function getProductClassesOriginal(Product $Product)
    {
        $ProductClasses = $Product->getProductClasses();
        return $ProductClasses->filter(function($ProductClass) {
            return true;
        });
    }

    /**
     * 規格なし商品を除いて商品規格を取得.
     *
     * @param Product $Product
     * @return \Eccube\Entity\ProductClass[]
     */
    protected function getProductClassesExcludeNonClass(Product $Product)
    {
        $ProductClasses = $Product->getProductClasses();
        return $ProductClasses->filter(function($ProductClass) {
            $ClassCategory1 = $ProductClass->getClassCategory1();
            $ClassCategory2 = $ProductClass->getClassCategory2();
            return ($ClassCategory1 || $ClassCategory2);
        });
    }
}
