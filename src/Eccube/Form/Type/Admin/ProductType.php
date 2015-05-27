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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductType extends AbstractType
{
    public $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder
            // 基本情報
            ->add('name', 'text', array(
                'label' => '商品名',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('product_type', 'product_type', array(
                'label' => '商品種別',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'mapped' => false,
            ))
            ->add('product_image', 'collection', array(
                'label' => '商品画像',
                'type' => 'file',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'mapped' => false,
            ))
            ->add('description_detail', 'textarea', array(
                'label' => '商品説明(詳細)',
            ))
            ->add('description_list', 'textarea', array(
                'label' => '商品説明(一覧)',
            ))
            ->add('price02', 'money', array(
                'label' => '販売価格',
                'currency' => 'JPY',
                'precision' => 0,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'mapped' => false,
            ))
            ->add('price01', 'money', array(
                'label' => '通常価格',
                'currency' => 'JPY',
                'precision' => 0,
                'mapped' => false,
            ))
            ->add('stock', 'integer', array(
                'label' => '在庫数',
                'required' => false,
                'mapped' => false,
            ))
            ->add('stock_unlimited', 'checkbox', array(
                'label' => '無制限',
                'value' => '1',
                'required' => false,
                'mapped' => false,
            ))
            ->add('Category', 'category', array(
               'label' => '商品カテゴリ',
               'constraints' => array(
                   new Assert\NotBlank(),
               ),
               'multiple' => true,
               'expanded' => true,
               'mapped' => false,
            ))

            // 詳細な説明
            ->add('code', 'text', array(
                'label' => '商品コード',
                'required' => false,
                'mapped' => false,
            ))
            ->add('sale_limit', 'integer', array(
                'label' => '販売制限数',
                'mapped' => false,
            ))
            ->add('tag', 'text', array(
                'label' => 'タグ',
                'mapped' => false,
            ))
            ->add('search_word', 'textarea', array(
                'label' => "検索ワード",
                'required' => false,
                'mapped' => false,
            ))
            ->add('DeliveryDate', 'delivery_date', array(
                'label' => '発送日目安',
                'empty_value' => '選択してください',
                'required' => false,
            ))
            ->add('point_rate', 'integer', array(
                'label' => 'ポイント(%)',
                'mapped' => false,
            ))

            // サブ情報
            ->add('free_area', 'textarea', array(
                'label' => 'サブ情報',
            ))

            // 右ブロック
            ->add('status', 'choice', array(
                'choices' => array(
                    '0' => '公開',
                    '1' => '非公開',
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('note', 'textarea', array(
                'label' => 'ショップ用メモ帳',
                'required' => false,
            ))
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_product';
    }
}
