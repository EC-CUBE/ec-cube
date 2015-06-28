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


namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class DeliveryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => '配送業者名',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank,
                ),
            ))
            ->add('service_name', 'text', array(
                'label' => '名称',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('description', 'textarea', array(
                'label' => 'ショップ用メモ欄',
                'required' => false,
            ))
            ->add('confirm_url', 'text', array(
                'label' => '伝票No.URL',
                'required' => false,
                'constraints' => array(
                    new Assert\Url(),
                ),
            ))
            ->add('product_type', 'product_type', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('payments', 'entity', array(
                'label' => '支払方法',
                'class' => 'Eccube\Entity\Payment',
                'property' => 'method',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.rank', 'ASC');
                },
                'mapped' => false,
            ))
            ->add('delivery_times', 'collection', array(
                'label' => 'お届け時間',
                'required' => false,
                'type' => 'delivery_time',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ))
            ->add('free_all', 'money', array(
                'label' => false,
                'currency' => 'JPY',
                'precision' => 0,
                'required' => false,
                'mapped' => false,
            ))
            ->add('delivery_fees', 'collection', array(
                'label' => '都道府県別設定',
                'required' => true,
                'type' => 'delivery_fee',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ))
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\Delivery',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'delivery';
    }
}
