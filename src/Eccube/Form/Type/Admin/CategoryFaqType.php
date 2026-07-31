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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * カテゴリごとFAQ の専用編集ページ用フォーム。
 *
 * カテゴリ管理画面（ツリー型）とは別に、1 カテゴリの FAQ だけを編集する画面で使う。
 * この画面は常に faqs コレクションを描画するため、未描画による全削除は起きない。
 */
class CategoryFaqType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('faqs', CollectionType::class, [
                'entry_type' => FaqType::class,
                'entry_options' => ['sortable' => true],
                'prototype' => true,
                'mapped' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'admin_category_faq';
    }
}
