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

use Eccube\Entity\Delivery;
use Eccube\Entity\DeliveryTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShoppingMultipleType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $deliveries = $options['deliveries'];
        $delivery = $options['delivery'];
        $deliveryDurations = $options['deliveryDurations'];

        $builder
            ->add('delivery', EntityType::class, [
                'class' => Delivery::class,
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
                'class' => DeliveryTime::class,
                'choice_label' => 'deliveryTime',
                'choices' => $delivery->getDeliveryTimes(),
                'required' => false,
                'placeholder' => 'common.select__unspecified',
            ]);
    }

    /**
     * {@inheritDoc}
     *
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
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
    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'shopping_multiple';
    }
}
