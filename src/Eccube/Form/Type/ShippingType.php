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

class ShippingType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Country')
            ->add('zipcode')
            ->add('name', 'name')
            ->add('kana01')
            ->add('kana02')
            ->add('company_name')
            ->add('tel', 'tel')
            ->add('fax', 'fax', array(
                'label' => 'FAX',
            ))
            ->add('zip', 'zip')
            ->add('address', 'address')
            ->add('time_id')
            ->add('shipping_time')
            ->add('shipping_date', 'date', array(
                'format' => 'yyyy-MM-dd',
            ))
            ->add('shipping_commit_date')
            ->add('ShipmentItems', 'collection', array(
                'type' => new ShipmentItemType()
            ))
            ->add('time', 'entity', array(
                'class' => 'Eccube\Entity\DelivTime',
                'property' => 'deliv_time',
                'expanded' => false,
                'multiple' => false,
                'mapped' => false,
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Eccube\Entity\Shipping',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shipping';
    }
}
