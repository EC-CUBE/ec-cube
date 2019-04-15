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
                'placeholder' => 'common.select__unspecified',
            ])
            ->add('deliveryTime', EntityType::class, [
                'class' => 'Eccube\Entity\DeliveryTime',
                'choice_label' => 'deliveryTime',
                'choices' => $delivery->getDeliveryTimes(),
                'required' => false,
                'placeholder' => 'common.select__unspecified',
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
