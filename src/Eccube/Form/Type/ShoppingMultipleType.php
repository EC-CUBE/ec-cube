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

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('delivery', EntityType::class, [
                'class' => 'Eccube\Entity\Delivery',
                'choice_label' => 'name',
                'choices' => $deliveries,
                'data' => $delivery,
            ])
            ->add('deliveryDuration', ChoiceType::class, [
                'choices' => array_flip($deliveryDurations),
                'required' => false,
                'placeholder' => 'shoppingmultiple.placeholder.not_selected',
            ])
            ->add('deliveryTime', EntityType::class, [
                'class' => 'Eccube\Entity\DeliveryTime',
                'choice_label' => 'deliveryTime',
                'choices' => $delivery->getDeliveryTimes(),
                'required' => false,
                'placeholder' => 'shoppingmultiple.placeholder.not_selected',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'deliveries' => [],
            'delivery' => null,
            'deliveryDurations' => [],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'shopping_multiple';
    }
}
