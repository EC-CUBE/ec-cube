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

use Eccube\Form\Type\MasterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoundingTypeType extends AbstractType
{
    #[\Override]
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => \Eccube\Entity\Master\RoundingType::class,
            'expanded' => false,
        ]);
    }

    #[\Override]
    public function getParent()
    {
        return MasterType::class;
    }

    #[\Override]
    public function getBlockPrefix()
    {
        return 'rounding_type';
    }
}
