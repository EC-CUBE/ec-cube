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

/**
 * Created by PhpStorm.
 * User: chihiro_adachi
 * Date: 15/04/23
 * Time: 15:17
 */

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden', array(
                'mapped' => false,
            ))
            // ->add('Country')
            // ->add('zipcode', 'text')
            ->add('Deliv', 'entity', array(
                'class' => 'Eccube\Entity\Deliv',
                'property' => 'name',
                'expanded' => false,
                'multiple' => false,
                'empty_value' => '-',
            ))
            ->add('Payment', 'entity', array(
                'class' => 'Eccube\Entity\Payment',
                'property' => 'method',
                'expanded' => false,
                'multiple' => false,
                'empty_value' => '-',
            ))
            ->add('Customer', 'hidden', array(
                'mapped' => false,
            ))
            ->add('Sex', 'sex', array(
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('Job')
            ->add('DeviceType')
            ->add('message')
            ->add('name', 'name')
            ->add('kana01', 'text')
            ->add('kana02', 'text')
            ->add('company_name', 'text')
            ->add('email', 'text')
            ->add('tel', 'tel')
            ->add('fax', 'fax')
            ->add('zip', 'zip')
            ->add('address', 'address')
            ->add('birth', 'birthday', array(
                'format' => 'yyyy-MM-dd',
            ))
            ->add('subtotal')
            ->add('discount')
            ->add('deliv_fee')
            ->add('charge')
            ->add('use_point')
            ->add('add_point')
            ->add('birth_point')
            ->add('tax')
            ->add('total')
            ->add('payment_total')
            ->add('payment_method')
            ->add('note', 'textarea')
            ->add('OrderStatus')
            ->add('commit_date')
            ->add('payment_date')
            ->add('create_date', 'date', array(
                'mapped' => false,
            ))
            ->add('OrderDetails', 'collection', array('type' => new OrderDetailType()))
            ->add('Shippings', 'collection', array('type' => new ShippingType()))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Eccube\Entity\Order',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'order';
    }
}
