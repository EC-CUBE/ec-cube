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

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\Extension\Core\Type;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;

class SearchCustomerType extends AbstractType
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
            ->add('customer_id', 'integer', array(
                'label' => '会員ID',
                'required' => false,
                'constraints' => array(
                    new Assert\Type(array(
                        'type' => 'integer',
                    )),
                ),
            ))
            ->add('pref', 'pref', array(
                'label' => '都道府県',
                'required' => false,
            ))
            ->add('name', 'text', array(
                'required' => false,
            ))
            ->add('kana', 'text', array(
                'required' => false,
            ))
            ->add('sex', 'sex', array(
                'label' => '性別',
                'required' => false,
            ))
            ->add('birth_month', 'choice', array(
                'label' => '誕生月',
                'required' => false,
                'choices' => array(1, 2, 3, 4, 5, 6, 7, 8, 8, 10, 11, 12),
            ))
            ->add('birth_start', 'birthday', array(
                'label' => '誕生日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('birth_end', 'birthday', array(
                'label' => '誕生日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('email', 'email', array(
                'required' => false,
            ))
            ->add('tel', 'tel', array(
                'required' => false,
            ))
            ->add('job', 'job', array(
                'label' => '職業',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
            ))
            ->add('buy_total_start', 'integer', array(
                'label' => '購入金額',
                'required' => false,
            ))
            ->add('buy_total_end', 'integer', array(
                'label' => '購入金額',
                'required' => false,
            ))
            ->add('buy_times_start', 'integer', array(
                'label' => '購入回数',
                'required' => false,
            ))
            ->add('buy_times_end', 'integer', array(
                'label' => '購入回数',
                'required' => false,
            ))
            ->add('register_start', 'date', array(
                'label' => '登録・更新日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('register_end', 'date', array(
                'label' => '登録・更新日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('last_buy_start', 'date', array(
                'label' => '最終購入日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('last_buy_end', 'date', array(
                'label' => '最終購入日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('buy_product_name', 'text', array(
                'label' => '購入商品名',
                'required' => false,
            ))
            ->add('buy_product_code', 'text', array(
                'label' => '購入商品コード',
                'required' => false,
            ))
            ->add('customer_status', 'choice', array(
                'label' => '会員状態',
                'required' => false,
                'choices' => array(
                    '1' => '非会員',
                    '2' => '正会員',
                ),
                'expanded' => true,
                'multiple' => false,
                'empty_value' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_search_customer';
    }
}
