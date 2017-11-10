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
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\ClassName;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductStock;
use Eccube\Entity\TaxRule;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints as Assert;

class ProductClassController
{
    /**
     * 商品規格が登録されていなければ新規登録、登録されていれば更新画面を表示する
     */
    public function index(Application $app, Request $request, $id)
    {
        /** @var $Product \Eccube\Entity\Product */
        $Product = $app['eccube.repository.product']->find($id);
        $hasClassCategoryFlg = false;

        if (!$Product) {
            throw new NotFoundHttpException('商品が存在しません');
        }

        // 商品規格情報が存在しなければ新規登録させる
        if (!$Product->hasProductClass()) {
            // 登録画面を表示

            log_info('商品規格新規登録表示', array($id));

            $builder = $app['form.factory']->createBuilder();

            $builder
                ->add('class_name1', 'entity', array(
                    'class' => 'Eccube\Entity\ClassName',
                    'property' => 'name',
                    'empty_value' => '規格1を選択',
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ))
                ->add('class_name2', 'entity', array(
                    'class' => 'Eccube\Entity\ClassName',
                    'property' => 'name',
                    'empty_value' => '規格2を選択',
                    'required' => false,
                ));

            $event = new EventArgs(
                array(
                    'builder' => $builder,
                    'Product' => $Product,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PRODUCT_PRODUCT_CLASS_INDEX_INITIALIZE, $event);

            $form = $builder->getForm();

            $productClassForm = null;

            if ('POST' === $request->getMethod()) {

                $form->handleRequest($request);

                if ($form->isValid()) {

                    $data = $form->getData();

                    $ClassName1 = $data['class_name1'];
                    $ClassName2 = $data['class_name2'];

                    log_info('選択された商品規格', array($ClassName1, $ClassName2));

                    // 各規格が選択されている際に、分類を保有しているか確認
                    $class1Valied = $this->isValiedCategory($ClassName1);
                    $class2Valied = $this->isValiedCategory($ClassName2);

                    // 規格が選択されていないか、選択された状態で分類が保有されていれば、画面表示
                    if($class1Valied && $class2Valied){
                        $hasClassCategoryFlg = true;
                    }

                    if (!is_null($ClassName2) && $ClassName1->getId() == $ClassName2->getId()) {
                        // 規格1と規格2が同じ値はエラー
                        $form['class_name2']->addError(new FormError('規格1と規格2は、同じ値を使用できません。'));
                    } else {
                        // 規格分類が設定されていない商品規格を取得
                        $orgProductClasses = $Product->getProductClasses();
                        $sourceProduct = $orgProductClasses[0];

                        // 規格分類が組み合わされた商品規格を取得
                        $ProductClasses = $this->createProductClasses($app, $Product, $ClassName1, $ClassName2);

                        // 組み合わされた商品規格にデフォルト値をセット
                        foreach ($ProductClasses as $productClass) {
                            $this->setDefualtProductClass($app, $productClass, $sourceProduct);
                        }

                        $builder = $app['form.factory']->createBuilder();

                        $builder
                            ->add('product_classes', 'collection', array(
                                'type' => 'admin_product_class',
                                'allow_add' => true,
                                'allow_delete' => true,
                                'data' => $ProductClasses,
                            ));

                        $event = new EventArgs(
                            array(
                                'builder' => $builder,
                                'Product' => $Product,
                                'ProductClasses' => $ProductClasses,
                            ),
                            $request
                        );
                        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PRODUCT_PRODUCT_CLASS_INDEX_CLASSES, $event);

                        $productClassForm = $builder->getForm()->createView();

                    }

                }
            }

            return $app->render('Product/product_class.twig', array(
                'form' => $form->createView(),
                'classForm' => $productClassForm,
                'Product' => $Product,
                'not_product_class' => true,
                'error' => null,
                'has_class_category_flg' => $hasClassCategoryFlg,
            ));
        } else {
            // 既に商品規格が登録されている場合、商品規格画面を表示する

            log_info('商品規格登録済表示', array($id));

            // 既に登録されている商品規格を取得
            $ProductClasses = $this->getProductClassesExcludeNonClass($Product);

            // 設定されている規格分類1、2を取得(商品規格の規格分類には必ず同じ値がセットされている)
            $ProductClass = $ProductClasses[0];
            $ClassName1 = $ProductClass->getClassCategory1()->getClassName();
            $ClassName2 = null;
            if (!is_null($ProductClass->getClassCategory2())) {
                $ClassName2 = $ProductClass->getClassCategory2()->getClassName();
            }

            // 規格分類が組み合わされた空の商品規格を取得
            $createProductClasses = $this->createProductClasses($app, $Product, $ClassName1, $ClassName2);

            $mergeProductClasses = array();
            $sortProductClasses = new ArrayCollection();

            // 商品税率が設定されている場合、商品税率を項目に設定
            $BaseInfo = $app['eccube.repository.base_info']->get();
            if ($BaseInfo->getOptionProductTaxRule() == Constant::ENABLED) {
                foreach ($ProductClasses as $class) {
                    if ($class->getTaxRule() && !$class->getTaxRule()->getDelFlg()) {
                        $class->setTaxRate($class->getTaxRule()->getTaxRate());
                    }
                }
            }

            // 登録済み商品規格と空の商品規格をマージ
            $flag = false;

            // 削除されてる商品規格情報を取得のためにフィルターから「ProductClass」を外す
            /* @var $softDeleteFilter \Eccube\Doctrine\Filter\SoftDeleteFilter */
            $softDeleteFilter = $app['orm.em']->getFilters()->getFilter('soft_delete');
            $oldExcludes = $softDeleteFilter->getExcludes();
            $softDeleteFilter->setExcludes(array(
                'Eccube\Entity\ProductClass',
            ));
            foreach ($createProductClasses as $index => $createProductClass) {
                // 既に登録済みの商品規格にチェックボックスを設定
                foreach ($ProductClasses as $productClass) {
                    if ($productClass->getClassCategory1() == $createProductClass->getClassCategory1() &&
                        $productClass->getClassCategory2() == $createProductClass->getClassCategory2()) {
                        // チェックボックスを追加
                        $productClass->setAdd(true);
                        $flag = true;
                        $sortProductClasses->add($productClass);
                        break;
                    }
                }

                if (!$flag) {
                    // 商品の中に規格ない場合は削除されている規格を取得する
                    $condition = array();
                    $condition['Product'] = $Product;
                    $condition['ClassCategory1'] = $createProductClass->getClassCategory1();
                    $condition['ClassCategory2'] = $createProductClass->getClassCategory2();
                    $condition['del_flg'] = Constant::ENABLED;

                    $delProductClass = $app['eccube.repository.product_class']->findOneBy($condition);
                    if ($delProductClass){
                        // 削除されている規格あった場合はその規格を使う
                        $mergeProductClasses[] = $delProductClass;
                    } else {
                        // 取得できない場合はデフォルトの規格を使う
                        $mergeProductClasses[] = $createProductClass;
                    }
                }

                $flag = false;
            }

            // 上で変わったフィルター情報を戻す
            $softDeleteFilter->setExcludes($oldExcludes);

            // 登録済み商品規格と空の商品規格をマージ
            foreach ($mergeProductClasses as $mergeProductClass) {
                // 空の商品規格にデフォルト値を設定
                $this->setDefualtProductClass($app, $mergeProductClass, $sortProductClasses[0]);
                $sortProductClasses->add($mergeProductClass);
            }

            $builder = $app['form.factory']->createBuilder();

            $builder
                ->add('product_classes', 'collection', array(
                    'type' => 'admin_product_class',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'data' => $sortProductClasses,
                ));

            $event = new EventArgs(
                array(
                    'builder' => $builder,
                    'Product' => $Product,
                    'ProductClasses' => $sortProductClasses,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PRODUCT_PRODUCT_CLASS_INDEX_CLASSES, $event);

            $productClassForm = $builder->getForm()->createView();

            return $app->render('Product/product_class.twig', array(
                'classForm' => $productClassForm,
                'Product' => $Product,
                'class_name1' => $ClassName1,
                'class_name2' => $ClassName2,
                'not_product_class' => false,
                'error' => null,
                'has_class_category_flg' => true,
            ));
        }
    }

    /**
     * 商品規格の登録、更新、削除を行う
     *
     * @param Application $app
     * @param Request     $request
     * @param int         $id
     * @return RedirectResponse
     */
    public function edit(Application $app, Request $request, $id)
    {

        /* @var $softDeleteFilter \Eccube\Doctrine\Filter\SoftDeleteFilter */
        $softDeleteFilter = $app['orm.em']->getFilters()->getFilter('soft_delete');
        $softDeleteFilter->setExcludes(array(
            'Eccube\Entity\TaxRule',
        ));

        /** @var $Product \Eccube\Entity\Product */
        $Product = $app['eccube.repository.product']->find($id);

        if (!$Product) {
            throw new NotFoundHttpException('商品が存在しません');
        }

        /* @var FormBuilder $builder */
        $builder = $app['form.factory']->createBuilder();
        $builder->add('product_classes', 'collection', array(
            'type' => 'admin_product_class',
            'allow_add' => true,
            'allow_delete' => true,
        ));

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Product' => $Product,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PRODUCT_PRODUCT_CLASS_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        $ProductClasses = $this->getProductClassesExcludeNonClass($Product);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            switch ($request->get('mode')) {
                case 'edit':
                    // 新規登録
                    log_info('商品規格新規登録開始', array($id));

                    if (count($ProductClasses) > 0) {
                        // 既に登録されていれば最初の画面に戻す
                        log_info('商品規格登録済', array($id));
                        return $app->redirect($app->url('admin_product_product_class', array('id' => $id)));
                    }

                    $addProductClasses = array();

                    $tmpProductClass = null;
                    foreach ($form->get('product_classes') as $formData) {
                        // 追加対象の行をvalidate
                        $ProductClass = $formData->getData();

                        if ($ProductClass->getAdd()) {
                            if ($formData->isValid()) {
                                $addProductClasses[] = $ProductClass;
                            } else {
                                // 対象行のエラー
                                return $this->render($app, $Product, $ProductClass, true, $form);
                            }
                        }
                        $tmpProductClass = $ProductClass;
                    }

                    if (count($addProductClasses) == 0) {
                        // 対象がなければエラー
                        log_info('商品規格が未選択', array($id));
                        $error = array('message' => '商品規格が選択されていません。');
                        return $this->render($app, $Product, $tmpProductClass, true, $form, $error);
                    }

                    // 選択された商品規格を登録
                    $this->insertProductClass($app, $Product, $addProductClasses);

                    // デフォルトの商品規格を更新
                    $defaultProductClass = $app['eccube.repository.product_class']
                        ->findOneBy(array('Product' => $Product, 'ClassCategory1' => null, 'ClassCategory2' => null));

                    $defaultProductClass->setDelFlg(Constant::ENABLED);

                    $app['orm.em']->flush();

                    log_info('商品規格新規登録完了', array($id));

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'Product' => $Product,
                            'defaultProductClass' => $defaultProductClass,
                        ),
                        $request
                    );
                    $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PRODUCT_PRODUCT_CLASS_EDIT_COMPLETE, $event);

                    $app->addSuccess('admin.product.product_class.save.complete', 'admin');

                    break;
                case 'update':
                    // 更新
                    log_info('商品規格更新開始', array($id));

                    if (count($ProductClasses) == 0) {
                        // 商品規格が0件であれば最初の画面に戻す
                        log_info('商品規格が存在しません', array($id));
                        return $app->redirect($app->url('admin_product_product_class', array('id' => $id)));
                    }

                    $checkProductClasses = array();
                    $removeProductClasses = array();

                    $tempProductClass = null;
                    foreach ($form->get('product_classes') as $formData) {
                        // 追加対象の行をvalidate
                        $ProductClass = $formData->getData();

                        if ($ProductClass->getAdd()) {
                            if ($formData->isValid()) {
                                $checkProductClasses[] = $ProductClass;
                            } else {
                                return $this->render($app, $Product, $ProductClass, false, $form);
                            }
                        } else {
                            // 削除対象の行
                            $removeProductClasses[] = $ProductClass;
                        }
                        $tempProductClass = $ProductClass;
                    }

                    if (count($checkProductClasses) == 0) {
                        // 対象がなければエラー
                        log_info('商品規格が存在しません', array($id));
                        $error = array('message' => '商品規格が選択されていません。');
                        return $this->render($app, $Product, $tempProductClass, false, $form, $error);
                    }

                    // 削除されてる商品規格情報を取得のためにフィルターから「ProductClass」を外す
                    /* @var $softDeleteFilter \Eccube\Doctrine\Filter\SoftDeleteFilter */
                    $softDeleteFilter = $app['orm.em']->getFilters()->getFilter('soft_delete');
                    $oldExcludes =  $softDeleteFilter->getExcludes();
                    $softDeleteFilter->setExcludes(array(
                        'Eccube\Entity\ProductClass',
                    ));

                    // 登録対象と更新対象の行か判断する
                    $addProductClasses = array();
                    $updateProductClasses = array();
                    foreach ($checkProductClasses as $cp) {
                        $flag = false;

                        // 既に登録済みの商品規格か確認
                        foreach ($ProductClasses as $productClass) {
                            if ($productClass->getProduct()->getId() == $id &&
                                $productClass->getClassCategory1() == $cp->getClassCategory1() &&
                                $productClass->getClassCategory2() == $cp->getClassCategory2()) {
                                $updateProductClasses[] = $cp;

                                // 商品情報
                                $cp->setProduct($Product);
                                // 商品在庫
                                $productStock = $productClass->getProductStock();
                                if (!$cp->getStockUnlimited()) {
                                    $productStock->setStock($cp->getStock());
                                } else {
                                    $productStock->setStock(null);
                                }
                                $this->setDefualtProductClass($app, $productClass, $cp);
                                $flag = true;
                                break;
                            }
                        }

                        if (!$flag) {
                            // 削除されている規格取得する
                            $condition = array();
                            $condition['Product'] = $Product;
                            $condition['ClassCategory1'] = $cp->getClassCategory1();
                            $condition['ClassCategory2'] = $cp->getClassCategory2();
                            $condition['del_flg'] = Constant::ENABLED;

                            $delProductClass = $app['eccube.repository.product_class']->findOneBy($condition);
                            if ($delProductClass){
                                // 削除されている商品を見つけた場合は使います
                                // 商品情報
                                $cp->setProduct($Product);
                                // 商品在庫
                                $productStock = $productClass->getProductStock();
                                if (!$cp->getStockUnlimited()) {
                                    $productStock->setStock($cp->getStock());
                                } else {
                                    $productStock->setStock(null);
                                }
                                $this->setDefualtProductClass($app, $delProductClass, $cp);
                                // 削除されている規格を復活する（del_flgを更新）
                                $delProductClass->setDelFlg(Constant::DISABLED);
                                $ProductClasses[] = $delProductClass;
                            } else {
                                $addProductClasses[] = $cp;
                            }
                        }
                    }

                    // 上で変わったフィルター情報を戻す
                    $softDeleteFilter->setExcludes($oldExcludes);

                    foreach ($removeProductClasses as $rc) {
                        // 登録されている商品規格に削除フラグをセット
                        foreach ($ProductClasses as $productClass) {
                            if ($productClass->getProduct()->getId() == $id &&
                                $productClass->getClassCategory1() == $rc->getClassCategory1() &&
                                $productClass->getClassCategory2() == $rc->getClassCategory2()) {

                                $productClass->setDelFlg(Constant::ENABLED);
                                break;
                            }
                        }
                    }

                    // 選択された商品規格を登録
                    $this->insertProductClass($app, $Product, $addProductClasses);

                    $app['orm.em']->flush();

                    log_info('商品規格更新完了', array($id));

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'Product' => $Product,
                            'updateProductClasses' => $updateProductClasses,
                            'addProductClasses' => $addProductClasses,
                        ),
                        $request
                    );
                    $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PRODUCT_PRODUCT_CLASS_EDIT_UPDATE, $event);

                    $app->addSuccess('admin.product.product_class.update.complete', 'admin');

                    break;

                case 'delete':
                    // 削除
                    log_info('商品規格削除開始', array($id));

                    if (count($ProductClasses) == 0) {
                        // 既に商品が削除されていれば元の画面に戻す
                        log_info('商品規格が存在しません', array($id));
                        return $app->redirect($app->url('admin_product_product_class', array('id' => $id)));
                    }

                    foreach ($ProductClasses as $ProductClass) {
                        // 登録されている商品規格に削除フラグをセット
                        $ProductClass->setDelFlg(Constant::ENABLED);
                    }

                    /* @var $softDeleteFilter \Eccube\Doctrine\Filter\SoftDeleteFilter */
                    $softDeleteFilter = $app['orm.em']->getFilters()->getFilter('soft_delete');
                    $softDeleteFilter->setExcludes(array(
                        'Eccube\Entity\ProductClass'
                    ));

                    // デフォルトの商品規格を更新
                    $defaultProductClass = $app['eccube.repository.product_class']
                        ->findOneBy(array('Product' => $Product, 'ClassCategory1' => null, 'ClassCategory2' => null, 'del_flg' => Constant::ENABLED));

                    $defaultProductClass->setDelFlg(Constant::DISABLED);

                    $app['orm.em']->flush();
                    log_info('商品規格削除完了', array($id));

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'Product' => $Product,
                            'defaultProductClass' => $defaultProductClass,
                        ),
                        $request
                    );
                    $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PRODUCT_PRODUCT_CLASS_EDIT_DELETE, $event);

                    $app->addSuccess('admin.product.product_class.delete.complete', 'admin');

                    break;
                default:
                    break;
            }

        }

        return $app->redirect($app->url('admin_product_product_class', array('id' => $id)));
    }

    /**
     * 登録、更新時のエラー画面表示
     *
     */
    protected function render($app, $Product, $ProductClass, $not_product_class, $classForm, $error = null)
    {

        $ClassName1 = null;
        $ClassName2 = null;
        // 規格を取得
        if (isset($ProductClass)) {
            $ClassCategory1 = $ProductClass->getClassCategory1();
            if ($ClassCategory1) {
                $ClassName1 = $ClassCategory1->getClassName();
            }
            $ClassCategory2 = $ProductClass->getClassCategory2();
            if ($ClassCategory2) {
                $ClassName2 = $ClassCategory2->getClassName();
            }
        }

        $form = $app->form()
            ->add('class_name1', 'entity', array(
                'class' => 'Eccube\Entity\ClassName',
                'property' => 'name',
                'empty_value' => '規格1を選択',
                'data' => $ClassName1,
            ))
            ->add('class_name2', 'entity', array(
                'class' => 'Eccube\Entity\ClassName',
                'property' => 'name',
                'empty_value' => '規格2を選択',
                'data' => $ClassName2,
            ))
            ->getForm();

        log_info('商品規格登録エラー');


        return $app->render('Product/product_class.twig', array(
            'form' => $form->createView(),
            'classForm' => $classForm->createView(),
            'Product' => $Product,
            'class_name1' => $ClassName1,
            'class_name2' => $ClassName2,
            'not_product_class' => $not_product_class,
            'error' => $error,
            'has_class_category_flg' => true,
        ));
    }


    /**
     * 規格1と規格2を組み合わせた商品規格を作成
     */
    private function createProductClasses($app, Product $Product, ClassName $ClassName1 = null, ClassName $ClassName2 = null)
    {

        $ClassCategories1 = array();
        if ($ClassName1) {
            $ClassCategories1 = $app['eccube.repository.class_category']->findBy(array('ClassName' => $ClassName1), array('rank' => 'DESC'));
        }

        $ClassCategories2 = array();
        if ($ClassName2) {
            $ClassCategories2 = $app['eccube.repository.class_category']->findBy(array('ClassName' => $ClassName2), array('rank' => 'DESC'));
        }

        $ProductClasses = array();
        foreach ($ClassCategories1 as $ClassCategory1) {
            if ($ClassCategories2) {
                foreach ($ClassCategories2 as $ClassCategory2) {
                    $ProductClass = $this->newProductClass($app);
                    $ProductClass->setProduct($Product);
                    $ProductClass->setClassCategory1($ClassCategory1);
                    $ProductClass->setClassCategory2($ClassCategory2);
                    $ProductClass->setTaxRate(null);
                    $ProductClass->setDelFlg(Constant::DISABLED);
                    $ProductClasses[] = $ProductClass;
                }
            } else {
                $ProductClass = $this->newProductClass($app);
                $ProductClass->setProduct($Product);
                $ProductClass->setClassCategory1($ClassCategory1);
                $ProductClass->setTaxRate(null);
                $ProductClass->setDelFlg(Constant::DISABLED);
                $ProductClasses[] = $ProductClass;
            }

        }
        return $ProductClasses;
    }

    /**
     * 新しい商品規格を作成
     */
    private function newProductClass(Application $app)
    {
        $ProductType = $app['eccube.repository.master.product_type']->find($app['config']['product_type_normal']);

        $ProductClass = new ProductClass();
        $ProductClass->setProductType($ProductType);
        return $ProductClass;
    }

    /**
     * 商品規格のコピーを取得.
     *
     * @see http://symfony.com/doc/current/cookbook/form/form_collections.html
     * @param Product $Product
     * @return \Eccube\Entity\ProductClass[]
     */
    private function getProductClassesOriginal(Product $Product)
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
    private function getProductClassesExcludeNonClass(Product $Product)
    {
        $ProductClasses = $Product->getProductClasses();
        return $ProductClasses->filter(function($ProductClass) {
            $ClassCategory1 = $ProductClass->getClassCategory1();
            $ClassCategory2 = $ProductClass->getClassCategory2();
            return ($ClassCategory1 || $ClassCategory2);
        });
    }

    /**
     * デフォルトとなる商品規格を設定
     *
     * @param $productClassDest コピー先となる商品規格
     * @param $productClassOrig コピー元となる商品規格
     */
    private function setDefualtProductClass($app, $productClassDest, $productClassOrig) {
        $productClassDest->setDeliveryDate($productClassOrig->getDeliveryDate());
        $productClassDest->setProduct($productClassOrig->getProduct());
        $productClassDest->setProductType($productClassOrig->getProductType());
        $productClassDest->setCode($productClassOrig->getCode());
        $productClassDest->setStock($productClassOrig->getStock());
        $productClassDest->setStockUnlimited($productClassOrig->getStockUnlimited());
        $productClassDest->setSaleLimit($productClassOrig->getSaleLimit());
        $productClassDest->setPrice01($productClassOrig->getPrice01());
        $productClassDest->setPrice02($productClassOrig->getPrice02());
        $productClassDest->setDeliveryFee($productClassOrig->getDeliveryFee());

        // 個別消費税
        $BaseInfo = $app['eccube.repository.base_info']->get();
        if ($BaseInfo->getOptionProductTaxRule() == Constant::ENABLED) {
            if ($productClassOrig->getTaxRate() !== false && $productClassOrig->getTaxRate() !== null) {
                $productClassDest->setTaxRate($productClassOrig->getTaxRate());
                if ($productClassDest->getTaxRule()) {
                    $productClassDest->getTaxRule()->setTaxRate($productClassOrig->getTaxRate());
                    $productClassDest->getTaxRule()->setDelFlg(Constant::DISABLED);
                } else {
                    $taxrule = $app['eccube.repository.tax_rule']->newTaxRule();
                    $taxrule->setTaxRate($productClassOrig->getTaxRate());
                    $taxrule->setApplyDate(new \DateTime());
                    $taxrule->setProduct($productClassDest->getProduct());
                    $taxrule->setProductClass($productClassDest);
                    $productClassDest->setTaxRule($taxrule);
                }
            } else {
                if ($productClassDest->getTaxRule()) {
                    $productClassDest->getTaxRule()->setDelFlg(Constant::ENABLED);
                }
            }
        }
    }


    /**
     * 商品規格を登録
     *
     * @param Application     $app
     * @param Product         $Product
     * @param ArrayCollection $ProductClasses 登録される商品規格
     */
    private function insertProductClass($app, $Product, $ProductClasses) {

        $BaseInfo = $app['eccube.repository.base_info']->get();

        // 選択された商品を登録
        foreach ($ProductClasses as $ProductClass) {

            $ProductClass->setDelFlg(Constant::DISABLED);
            $ProductClass->setProduct($Product);
            $app['orm.em']->persist($ProductClass);

            // 在庫情報を作成
            $ProductStock = new ProductStock();
            $ProductClass->setProductStock($ProductStock);
            $ProductStock->setProductClass($ProductClass);
            if (!$ProductClass->getStockUnlimited()) {
                $ProductStock->setStock($ProductClass->getStock());
            } else {
                // 在庫無制限時はnullを設定
                $ProductStock->setStock(null);
            }
            $app['orm.em']->persist($ProductStock);

        }

        // 商品税率が設定されている場合、商品税率をセット
        if ($BaseInfo->getOptionProductTaxRule() == Constant::ENABLED) {
            // 初期設定の税設定.
            $TaxRule = $app['eccube.repository.tax_rule']->find(TaxRule::DEFAULT_TAX_RULE_ID);
            // 初期税率設定の計算方法を設定する
            $CalcRule = $TaxRule->getCalcRule();
            foreach ($ProductClasses as $ProductClass) {
                if ($ProductClass && is_numeric($taxRate = $ProductClass->getTaxRate())) {
                    $TaxRule = new TaxRule();
                    $TaxRule->setProduct($Product);
                    $TaxRule->setProductClass($ProductClass);
                    $TaxRule->setCalcRule($CalcRule);
                    $TaxRule->setTaxRate($taxRate);
                    $TaxRule->setTaxAdjust(0);
                    $TaxRule->setApplyDate(new \DateTime());
                    $TaxRule->setDelFlg(Constant::DISABLED);
                    $app['orm.em']->persist($TaxRule);
                }
            }
        }

    }

    /**
     * 規格の分類判定
     *
     * @param $class_name
     * @return boolean
     */
    private function isValiedCategory($class_name)
    {
        if (empty($class_name)) {
            return true;
        }
        if (count($class_name->getClassCategories()) < 1) {
            return false;
        }
        return true;
    }
}