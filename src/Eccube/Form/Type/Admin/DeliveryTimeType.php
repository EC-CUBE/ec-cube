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

use Doctrine\ORM\EntityRepository;
use Eccube\Entity\DeliveryTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DeliveryTimeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delivery_time', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'common.select',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('sort_no', HiddenType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('visible', ChoiceType::class, [
                'label' => false,
                'choices' => ['admin.common.show' => true, 'admin.common.hide' => false],
                'required' => false,
                'expanded' => false,
            ])
        ;
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var DeliveryTime $DeliveryTime */
            $DeliveryTime = $event->getData();
            if (null === $DeliveryTime->isVisible()) {
                $DeliveryTime->setVisible(true);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Eccube\Entity\DeliveryTime',
            'query_builder' => function (EntityRepository $er) {
                return $er
                    ->createQueryBuilder('dt')
                    ->orderBy('dt.sort_no', 'ASC');
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'delivery_time';
    }
}
