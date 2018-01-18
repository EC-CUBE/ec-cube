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
use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Form\Type\Master\ProductStatusType;
use Eccube\Form\Validator\TwigLint;
use Eccube\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
 * @FormType
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
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var ArrayCollection $arrCategory array of category
         */
        $arrCategory = $this->categoryRepository->getList(null, true);

        $builder
            // 商品規格情報
            ->add('class', ProductClassType::class, array(
                'mapped' => false,
            ))
            // 基本情報
            ->add('name', TextType::class, array(
                'label' => '商品名',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('product_image', FileType::class, array(
                'label' => '商品画像',
                'multiple' => true,
                'required' => false,
                'mapped' => false,
            ))
            ->add('description_detail', TextareaType::class, array(
                'label' => '商品説明',
            ))
            ->add('description_list', TextareaType::class, array(
                'label' => '商品説明(一覧)',
                'required' => false,
            ))
            ->add('Category', EntityType::class, array(
                'class' => 'Eccube\Entity\Category',
                'choice_label' => 'NameWithLevel',
                'label' => '商品カテゴリ',
                'multiple' => true,
                'mapped' => false,
                // Choices list (overdrive mapped)
                'choices' => $arrCategory,
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
            ->add('search_word', TextareaType::class, array(
                'label' => "検索ワード",
                'required' => false,
            ))
            // サブ情報
            ->add('free_area', TextareaType::class, array(
                'label' => 'サブ情報',
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
                'label' => 'ショップ用メモ帳',
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
