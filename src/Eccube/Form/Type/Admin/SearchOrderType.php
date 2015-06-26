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

class SearchOrderType extends AbstractType
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $config = $this->config;
        $builder
            // 受注ID・購入者名・購入者（フリガナ）・購入者会社名
            ->add('multi', 'text', array(
                'label' => '受注ID・購入者名・購入者（フリガナ）・購入者会社名',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array('max' => $config['stext_len'])),
                ),
            ))
            ->add('status', 'order_status', array(
                'label' => '対応状況',
            ))
            ->add('name', 'text', array(
                'required' => false,
            ))
            ->add('kana', 'text', array(
                'required' => false,
            ))
            ->add('email', 'email', array(
                'required' => false,
            ))
            ->add('tel', 'tel', array(
                'required' => false,
            ))
            ->add('sex', 'sex', array(
                'label' => '性別',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('payment', 'payment', array(
                'label' => '支払方法',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('order_date_start', 'date', array(
                'label' => '受注日(FROM)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('order_date_end', 'date', array(
                'label' => '受注日(TO)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('payment_date_start', 'date', array(
                'label' => '入金日(FROM)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('payment_date_end', 'date', array(
                'label' => '入金日(TO)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('commit_date_start', 'date', array(
                'label' => '発送日(FROM)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('commit_date_end', 'date', array(
                'label' => '発送日(TO)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('update_date_start', 'date', array(
                'label' => '更新日(FROM)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('update_date_end', 'date', array(
                'label' => '更新日(TO)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('payment_total_start', 'integer', array(
                'label' => '購入金額(下限)',
                'required' => false,
            ))
            ->add('payment_total_end', 'integer', array(
                'label' => '購入金額(上限)',
                'required' => false,
            ))
            ->add('buy_product_name', 'text', array(
                'label' => '購入商品名',
                'required' => false,
            ))
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_search_order';
    }
}
