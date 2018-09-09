<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Admin\Product;

use Doctrine\ORM\NoResultException;
use Eccube\Controller\AbstractController;
use Eccube\Entity\ClassName;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductStock;
use Eccube\Entity\TaxRule;
use Eccube\Form\Type\Admin\ProductClassMatrixType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\TaxRuleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductClassController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * @var ClassCategoryRepository
     */
    protected $classCategoryRepository;

    /**
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /**
     * ProductClassController constructor.
     *
     * @param ProductClassRepository $productClassRepository
     * @param ClassCategoryRepository $classCategoryRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductClassRepository $productClassRepository,
        ClassCategoryRepository $classCategoryRepository,
        BaseInfoRepository $baseInfoRepository,
        TaxRuleRepository $taxRuleRepository
    ) {
        $this->productRepository = $productRepository;
        $this->productClassRepository = $productClassRepository;
        $this->classCategoryRepository = $classCategoryRepository;
        $this->baseInfoRepository = $baseInfoRepository;
        $this->taxRuleRepository = $taxRuleRepository;
    }

    /**
     * 商品規格が登録されていなければ新規登録, 登録されていれば更新画面を表示する
     *
     * @Route("/%eccube_admin_route%/product/product/class/{id}", requirements={"id" = "\d+"}, name="admin_product_product_class")
     * @Template("@admin/Product/product_class.twig")
     */
    public function index(Request $request, $id)
    {
        $Product = $this->findProduct($id);
        if (!$Product) {
            throw new NotFoundHttpException();
        }

        $ClassName1 = null;
        $ClassName2 = null;

        if ($Product->hasProductClass()) {
            // 規格ありの商品は編集画面を表示する.
            $ProductClasses = $Product->getProductClasses()
                ->filter(function ($pc) {
                    return $pc->getClassCategory1() !== null;
                });

            // 設定されている規格名1, 2を取得(商品規格の規格分類には必ず同じ値がセットされている)
            $FirstProductClass = $ProductClasses->first();
            $ClassName1 = $FirstProductClass->getClassCategory1()->getClassName();
            $ClassCategory2 = $FirstProductClass->getClassCategory2();
            $ClassName2 = $ClassCategory2 ? $ClassCategory2->getClassName() : null;

            // 規格名1/2から組み合わせを生成し, DBから取得した商品規格とマージする.
            $ProductClasses = $this->mergeProductClasses(
                $this->createProductClasses($ClassName1, $ClassName2),
                $ProductClasses);

            // 組み合わせのフォームを生成する.
            $form = $this->createMatrixForm($ProductClasses, $ClassName1, $ClassName2,
                ['product_classes_exist' => true]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // フォームではtokenを無効化しているのでここで確認する.
                $this->isTokenValid();

                $this->saveProductClasses($Product, $form['product_classes']->getData());

                $this->addSuccess('admin.common.save_complete', 'admin');

                if ($request->get('return')) {
                    return $this->redirectToRoute('admin_product_product_class', ['id' => $Product->getId(), 'return' => $request->get('return')]);
                }

                return $this->redirectToRoute('admin_product_product_class', ['id' => $Product->getId()]);
            }
        } else {
            // 規格なし商品
            $form = $this->createMatrixForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // フォームではtokenを無効化しているのでここで確認する.
                $this->isTokenValid();

                // 登録,更新ボタンが押下されたかどうか.
                $isSave = $form['save']->isClicked();

                // 規格名1/2から商品規格の組み合わせを生成する.
                $ClassName1 = $form['class_name1']->getData();
                $ClassName2 = $form['class_name2']->getData();
                $ProductClasses = $this->createProductClasses($ClassName1, $ClassName2);

                // 組み合わせのフォームを生成する.
                // class_name1, class_name2が取得できるのがsubmit後のため, フォームを再生成して組み合わせ部分を構築している
                // submit後だと, フォーム項目の追加やデータ変更が許可されないため.
                $form = $this->createMatrixForm($ProductClasses, $ClassName1, $ClassName2,
                    ['product_classes_exist' => true]);

                // 登録ボタン押下時
                if ($isSave) {
                    $form->handleRequest($request);
                    if ($form->isSubmitted() && $form->isValid()) {
                        $this->saveProductClasses($Product, $form['product_classes']->getData());

                        $this->addSuccess('admin.common.save_complete', 'admin');

                        if ($request->get('return')) {
                            return $this->redirectToRoute('admin_product_product_class', ['id' => $Product->getId(), 'return' => $request->get('return')]);
                        }

                        return $this->redirectToRoute('admin_product_product_class', ['id' => $Product->getId()]);
                    }
                }
            }
        }

        return [
            'Product' => $Product,
            'form' => $form->createView(),
            'clearForm' => $this->createForm(FormType::class)->createView(),
            'ClassName1' => $ClassName1,
            'ClassName2' => $ClassName2,
            'return_product' => $request->get('return'),
        ];
    }

    /**
     * 商品規格を初期化する.
     *
     * @Route("/%eccube_admin_route%/product/product/class/{id}/clear", requirements={"id" = "\d+"}, name="admin_product_product_class_clear")
     */
    public function clearProductClasses(Request $request, Product $Product)
    {
        if (!$Product->hasProductClass()) {
            return $this->redirectToRoute('admin_product_product_class', ['id' => $Product->getId()]);
        }

        $form = $this->createForm(FormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ProductClasses = $this->productClassRepository->findBy([
                'Product' => $Product,
            ]);

            // デフォルト規格のみ有効にする
            foreach ($ProductClasses as $ProductClass) {
                $ProductClass->setVisible(false);
            }
            foreach ($ProductClasses as $ProductClass) {
                if (null === $ProductClass->getClassCategory1() && null === $ProductClass->getClassCategory2()) {
                    $ProductClass->setVisible(true);
                    break;
                }
            }

            $this->entityManager->flush();

            $this->addSuccess('admin.product.reset_complete', 'admin');
        }

        if ($request->get('return')) {
            return $this->redirectToRoute('admin_product_product_class', ['id' => $Product->getId(), 'return' => $request->get('return')]);
        }

        return $this->redirectToRoute('admin_product_product_class', ['id' => $Product->getId()]);
    }

    /**
     * 規格名1/2から, 商品規格の組み合わせを生成する.
     *
     * @param ClassName $ClassName1
     * @param ClassName|null $ClassName2
     *
     * @return array|ProductClass[]
     */
    protected function createProductClasses(ClassName $ClassName1, ClassName $ClassName2 = null)
    {
        $ProductClasses = [];
        $ClassCategories1 = $this->classCategoryRepository->findBy(['ClassName' => $ClassName1], ['sort_no' => 'DESC']);
        $ClassCategories2 = [];
        if ($ClassName2) {
            $ClassCategories2 = $this->classCategoryRepository->findBy(['ClassName' => $ClassName2],
                ['sort_no' => 'DESC']);
        }
        foreach ($ClassCategories1 as $ClassCategory1) {
            // 規格1のみ
            if (!$ClassName2) {
                $ProductClass = new ProductClass();
                $ProductClass->setClassCategory1($ClassCategory1);
                $ProductClasses[] = $ProductClass;
                continue;
            }
            // 規格1/2
            foreach ($ClassCategories2 as $ClassCategory2) {
                $ProductClass = new ProductClass();
                $ProductClass->setClassCategory1($ClassCategory1);
                $ProductClass->setClassCategory2($ClassCategory2);
                $ProductClasses[] = $ProductClass;
            }
        }

        return $ProductClasses;
    }

    /**
     * 商品規格の配列をマージする.
     *
     * @param $ProductClassesForMatrix
     * @param $ProductClasses
     *
     * @return array|ProductClass[]
     */
    protected function mergeProductClasses($ProductClassesForMatrix, $ProductClasses)
    {
        $mergedProductClasses = [];
        foreach ($ProductClassesForMatrix as $pcfm) {
            foreach ($ProductClasses as $pc) {
                if ($pcfm->getClassCategory1()->getId() === $pc->getClassCategory1()->getId()) {
                    $cc2fm = $pcfm->getClassCategory2();
                    $cc2 = $pc->getClassCategory2();

                    if (null === $cc2fm && null === $cc2) {
                        $mergedProductClasses[] = $pc;
                        continue 2;
                    }

                    if ($cc2fm && $cc2 && $cc2fm->getId() === $cc2->getId()) {
                        $mergedProductClasses[] = $pc;
                        continue 2;
                    }
                }
            }

            $mergedProductClasses[] = $pcfm;
        }

        return $mergedProductClasses;
    }

    /**
     * 商品規格を登録, 更新する.
     *
     * @param Product $Product
     * @param array|ProductClass[] $ProductClasses
     */
    protected function saveProductClasses(Product $Product, $ProductClasses = [])
    {
        foreach ($ProductClasses as $pc) {
            // 新規登録時、チェックを入れていなければ更新しない
            if (!$pc->getId() && !$pc->isVisible()) {
                continue;
            }

            // 無効から有効にした場合は, 過去の登録情報を検索.
            if (!$pc->getId()) {
                /** @var ProductClass $ExistsProductClass */
                $ExistsProductClass = $this->productClassRepository->findOneBy([
                    'Product' => $Product,
                    'ClassCategory1' => $pc->getClassCategory1(),
                    'ClassCategory2' => $pc->getClassCategory2(),
                ]);

                // 過去の登録情報があればその情報を復旧する.
                if ($ExistsProductClass) {
                    $ExistsProductClass->copyProperties($pc, [
                        'id',
                        'price01_inc_tax',
                        'price02_inc_tax',
                        'create_date',
                        'update_date',
                        'Creator',
                    ]);
                    $pc = $ExistsProductClass;
                }
            }

            // 更新時, チェックを外した場合はPOST内容を破棄してvisibleのみ更新する.
            if ($pc->getId() && !$pc->isVisible()) {
                $this->entityManager->refresh($pc);
                $pc->setVisible(false);
                continue;
            }

            $pc->setProduct($Product);
            $this->entityManager->persist($pc);

            // 在庫の更新
            $ProductStock = $pc->getProductStock();
            if (!$ProductStock) {
                $ProductStock = new ProductStock();
                $ProductStock->setProductClass($pc);
                $this->entityManager->persist($ProductStock);
            }
            $ProductStock->setStock($pc->isStockUnlimited() ? null : $pc->getStock());

            if ($this->baseInfoRepository->get()->isOptionProductTaxRule()) {
                $rate = $pc->getTaxRate();
                $TaxRule = $pc->getTaxRule();
                if (is_numeric($rate)) {
                    if ($TaxRule) {
                        $TaxRule->setTaxRate($rate);
                    } else {
                        // 初期税率設定の計算方法を設定する
                        $RoundingType = $this->taxRuleRepository->find(TaxRule::DEFAULT_TAX_RULE_ID)
                            ->getRoundingType();

                        $TaxRule = new TaxRule();
                        $TaxRule->setProduct($Product);
                        $TaxRule->setProductClass($pc);
                        $TaxRule->setTaxRate($rate);
                        $TaxRule->setRoundingType($RoundingType);
                        $TaxRule->setTaxAdjust(0);
                        $TaxRule->setApplyDate(new \DateTime());
                        $this->entityManager->persist($TaxRule);
                    }
                } else {
                    if ($TaxRule) {
                        $this->taxRuleRepository->delete($TaxRule);
                        $pc->setTaxRule(null);
                    }
                }
            }
        }

        // デフォルト規格を非表示にする.
        $DefaultProductClass = $this->productClassRepository->findOneBy([
            'Product' => $Product,
            'ClassCategory1' => null,
            'ClassCategory2' => null,
        ]);
        $DefaultProductClass->setVisible(false);

        $this->entityManager->flush();
    }

    /**
     * 商品規格登録フォームを生成する.
     *
     * @param array $ProductClasses
     * @param ClassName|null $ClassName1
     * @param ClassName|null $ClassName2
     * @param array $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createMatrixForm(
        $ProductClasses = [],
        ClassName $ClassName1 = null,
        ClassName $ClassName2 = null,
        array $options = []
    ) {
        $options = array_merge(['csrf_protection' => false], $options);
        $builder = $this->formFactory->createBuilder(ProductClassMatrixType::class, [
            'product_classes' => $ProductClasses,
            'class_name1' => $ClassName1,
            'class_name2' => $ClassName2,
        ], $options);

        return $builder->getForm();
    }

    /**
     * 商品を取得する.
     * 商品規格はvisible=trueのものだけを取得し, 規格分類はsort_no=DESCでソートされている.
     *
     * @param $id
     *
     * @return Product|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function findProduct($id)
    {
        $qb = $this->productRepository->createQueryBuilder('p')
            ->addSelect(['pc', 'cc1', 'cc2'])
            ->leftJoin('p.ProductClasses', 'pc')
            ->leftJoin('pc.ClassCategory1', 'cc1')
            ->leftJoin('pc.ClassCategory2', 'cc2')
            ->where('p.id = :id')
            ->andWhere('pc.visible = :pc_visible')
            ->setParameter('id', $id)
            ->setParameter('pc_visible', true)
            ->orderBy('cc1.sort_no', 'DESC')
            ->addOrderBy('cc2.sort_no', 'DESC');

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
