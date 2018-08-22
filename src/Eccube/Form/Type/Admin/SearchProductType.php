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

namespace Eccube\Form\Type\Admin;

use Eccube\Entity\Category;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\ProductStock;
use Eccube\Form\Type\Master\CategoryType as MasterCategoryType;
use Eccube\Form\Type\Master\ProductStatusType;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\Master\ProductStatusRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchProductType extends AbstractType
{
    /**
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SearchProductType constructor.
     *
     * @param ProductStatusRepository $productStatusRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(ProductStatusRepository $productStatusRepository, CategoryRepository $categoryRepository)
    {
        $this->productStatusRepository = $productStatusRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', TextType::class, [
                'label' => 'searchproduct.label.multi',
                'required' => false,
            ])
            ->add('category_id', ChoiceType::class, [
                'choice_label' => 'NameWithLevel',
                'label' => 'searchproduct.label.category',
                'placeholder' => 'searchproduct.placeholder.select',
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'choices' => $this->categoryRepository->getList(null, true),
                'choice_value' => function (Category $Category = null) {
                    return $Category ? $Category->getId() : null;
                },
            ])
            ->add('status', ProductStatusType::class, [
                'label' => 'searchproduct.label.type',
                'multiple' => true,
                'required' => false,
                'expanded' => true,
                'data' => $this->productStatusRepository->findBy(['id' => [
                    ProductStatus::DISPLAY_SHOW,
                    ProductStatus::DISPLAY_HIDE,
                ]]),
            ])
            ->add('stock', ChoiceType::class, [
                'label' => 'searchproduct.label.stock',
                'choices' => [
                    'admin.product.index.filter_in_stock' => ProductStock::IN_STOCK,
                    'admin.product.index.filter_out_of_stock' => ProductStock::OUT_OF_STOCK,
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('create_date_start', DateType::class, [
                'label' => 'searchproduct.label.registration_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('create_date_end', DateType::class, [
                'label' => 'searchproduct.label.registration_date_to',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('update_date_start', DateType::class, [
                'label' => 'searchproduct.label.updated_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('update_date_end', DateType::class, [
                'label' => 'searchproduct.label.updated_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_search_product';
    }
}
