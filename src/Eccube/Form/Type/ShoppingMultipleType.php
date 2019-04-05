<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ShoppingMultipleType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $deliveries = $options['deliveries'];
        $delivery = $options['delivery'];
        $deliveryDates = $options['deliveryDates'];

        $builder
            ->add('delivery', 'entity', array(
                'class' => 'Eccube\Entity\Delivery',
                'property' => 'name',
                'choices' => $deliveries,
                'data' => $delivery,
            ))
            ->add('deliveryDate', 'choice', array(
                'choices' => $deliveryDates,
                'required' => false,
                'empty_value' => '指定なし',
            ))
            ->add('deliveryTime', 'entity', array(
                'class' => 'Eccube\Entity\DeliveryTime',
                'property' => 'deliveryTime',
                'choices' => $delivery->getDeliveryTimes(),
                'required' => false,
                'empty_value' => '指定なし',
                'empty_data' => null,
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'deliveries' => array(),
            'delivery' => null,
            'deliveryDates' => array(),
        ));

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shopping_multiple';
    }
}
