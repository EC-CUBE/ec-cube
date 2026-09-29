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
use Eccube\Entity\Payment;
use Eccube\Form\Type\MasterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Payment::class,
            'choice_label' => 'method',
            'placeholder' => '-',
            // fixme 何故かここはDESC
            'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('m')
                ->orderBy('m.sort_no', 'DESC'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getParent(): string
    {
        return MasterType::class;
    }
}
