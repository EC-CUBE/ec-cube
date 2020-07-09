<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
                'label' => 'admin.product.multi_search_label',
                'required' => false,
            ])
            ->add('category_id', MasterCategoryType::class, [
                'choice_label' => 'NameWithLevel',
                'label' => 'admin.product.category',
                'placeholder' => 'common.select__all_products',
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'choices' => $this->categoryRepository->getList(null, true),
                'choice_value' => function (Category $Category = null) {
                    return $Category ? $Category->getId() : null;
                },
            ])
            ->add('status', ProductStatusType::class, [
                'label' => 'admin.product.display_status',
                'multiple' => true,
                'required' => false,
                'expanded' => true,
                'data' => $this->productStatusRepository->findBy(['id' => [
                    ProductStatus::DISPLAY_SHOW,
                    ProductStatus::DISPLAY_HIDE,
                ]]),
            ])
            ->add('stock', ChoiceType::class, [
                'label' => 'admin.product.stock',
                'choices' => [
                    'admin.product.stock__in_stock' => ProductStock::IN_STOCK,
                    'admin.product.stock__out_of_stock' => ProductStock::OUT_OF_STOCK,
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('create_date_start', DateTimeType::class, [
                'label' => 'admin.common.create_date__start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm',
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_create_date_start',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('create_date_end', DateTimeType::class, [
                'label' => 'admin.common.create_date__end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm',
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_create_date_end',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('update_date_start', DateTimeType::class, [
                'label' => 'admin.common.update_date__start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm',
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_update_date_start',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('update_date_end', DateTimeType::class, [
                'label' => 'admin.common.update_date__end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm',
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_update_date_end',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
        ;

        // EC-CUBE 4.0.4 以前のバージョンで互換性を保つため
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            /** @var $child Form */
            foreach ($form->all() as $child) {
                // DateTimeType でデータが送られている場合はチェック
                if ($child->getConfig()->getType()->getInnerType() instanceof DateTimeType && isset($data[$child->getName()])) {
                    // 日時が yyyy-MM-dd で指定されて入れば末尾に 00:00 を追加
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data[$child->getName()])) {
                        $data[$child->getName()] .= ' 00:00';
                    }
                }
            }

            $event->setData($data);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_search_product';
    }
}
