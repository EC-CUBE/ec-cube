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

namespace Eccube\Form\Type\Master;

use Doctrine\ORM\EntityRepository;
use Eccube\Entity\DeliveryDuration;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DeliveryDurationType
 */
class DeliveryDurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => DeliveryDuration::class,
            'placeholder' => 'common.select__unspecified',
            'multiple' => false,
            'expanded' => false,
            'required' => false,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('m')
                    ->orderBy('m.sort_no', 'ASC');
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'delivery_duration';
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getParent(): ?string
    {
        return EntityType::class;
    }
}
