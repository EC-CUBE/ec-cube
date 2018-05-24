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

namespace Eccube\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ShoppingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $payments = $options['payments'];
        $payment = $options['payment'];
        $message = $options['message'];

        $builder
            ->add('payment', EntityType::class, [
                'class' => 'Eccube\Entity\Payment',
                'choice_label' => 'method',
                'choices' => $payments,
                'data' => $payment,
                'expanded' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('message', TextareaType::class, [
                'required' => false,
                'data' => $message,
                'constraints' => [
                    new Assert\Length(['min' => 0, 'max' => 3000]), ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'payments' => [],
            'payment' => null,
            'message' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'shopping';
    }
}
