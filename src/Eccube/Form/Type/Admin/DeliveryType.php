<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Form\Type\Admin;

use Eccube\Form\Type\Master\PaymentType;
use Eccube\Form\Type\Master\SaleTypeType;
use Eccube\Form\Type\PriceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DeliveryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'delivery.label.shipping_company',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('service_name', TextType::class, [
                'label' => 'delivery.label.name',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'delivery.label.owner_note',
                'required' => false,
            ])
            ->add('confirm_url', TextType::class, [
                'label' => 'delivery.label.tracking_num',
                'required' => false,
                'constraints' => [
                    new Assert\Url(),
                ],
            ])
            ->add('sale_type', SaleTypeType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('payments', PaymentType::class, [
                'label' => 'delivery.label.payment',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('delivery_times', CollectionType::class, [
                'label' => 'delivery.label.delivery_time',
                'required' => false,
                'entry_type' => DeliveryTimeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ])
            ->add('free_all', PriceType::class, [
                'label' => false,
                'required' => false,
                'mapped' => false,
            ])
            ->add('delivery_fees', CollectionType::class, [
                'label' => 'delivery.label.pref_setting',
                'required' => true,
                'entry_type' => DeliveryFeeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Eccube\Entity\Delivery',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'delivery';
    }
}
