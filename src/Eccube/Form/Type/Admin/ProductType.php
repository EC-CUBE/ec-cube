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


namespace Eccube\Form\Type\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\Category;
use Eccube\Form\Type\Master\ProductStatusType;
use Eccube\Form\Validator\TwigLint;
use Eccube\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ProductType.
 */
class ProductType extends AbstractType
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * ProductType constructor.
     *
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        CategoryRepository $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // 商品規格情報
            ->add('class', ProductClassType::class, array(
                'mapped' => false,
            ))
            // 基本情報
            ->add('name', TextType::class, array(
                'label' => 'product.label.product_name',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('product_image', FileType::class, array(
                'label' => 'product.label.product_image',
                'multiple' => true,
                'required' => false,
                'mapped' => false,
            ))
            ->add('description_detail', TextareaType::class, array(
                'label' => 'product.label.product_description',
            ))
            ->add('description_list', TextareaType::class, array(
                'label' => 'product.label.product_description_list',
                'required' => false,
            ))
            ->add('Category', ChoiceType::class, array(
                'choice_label' => 'Name',
                'label' => 'product.label.product_category',
                'multiple' => true,
                'mapped' => false,
                'expanded' => true,
                'choices' => $this->categoryRepository->getList(null, true),
                'choice_value' => function (Category $Category = null) {
                    return $Category ? $Category->getId() : null;
                }
            ))

            // 詳細な説明
            ->add('Tag', EntityType::class, array(
                'class' => 'Eccube\Entity\Tag',
                'query_builder' => function($er) {
                    return $er->createQueryBuilder('t')
                    ->orderBy('t.sort_no', 'DESC');
                },
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
            ))
            ->add('search_word', TextType::class, array(
                'label' => "product.label.search_word",
                'required' => false,
            ))
            // サブ情報
            ->add('free_area', TextareaType::class, array(
                'label' => 'product.label.product_sub',
                'required' => false,
                'constraints' => [
                    new TwigLint()
                ]
            ))

            // 右ブロック
            ->add('Status', ProductStatusType::class, array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('note', TextareaType::class, array(
                'label' => 'product.label.owner_memo',
                'required' => false,
            ))

            // タグ
            ->add('tags', CollectionType::class, array(
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            // 画像
            ->add('images', CollectionType::class, array(
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('add_images', CollectionType::class, array(
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('delete_images', CollectionType::class, array(
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_product';
    }
}
