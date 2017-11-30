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

use Eccube\Annotation\FormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @FormType
 */
class ShoppingMultipleType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $deliveries = $options['deliveries'];
        $delivery = $options['delivery'];
        $deliveryDurations = $options['deliveryDurations'];

        $builder
            ->add('delivery', EntityType::class, array(
                'class' => 'Eccube\Entity\Delivery',
                'choice_label' => 'name',
                'choices' => $deliveries,
                'data' => $delivery,
            ))
            ->add('deliveryDuration', ChoiceType::class, array(
                'choices' => array_flip($deliveryDurations),
                'required' => false,
                'placeholder' => '指定なし',
            ))
            ->add('deliveryTime', EntityType::class, array(
                'class' => 'Eccube\Entity\DeliveryTime',
                'choice_label' => 'deliveryTime',
                'choices' => $delivery->getDeliveryTimes(),
                'required' => false,
                'placeholder' => '指定なし',
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'deliveries' => array(),
            'delivery' => null,
            'deliveryDurations' => array(),
        ));

    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'shopping_multiple';
    }
}
