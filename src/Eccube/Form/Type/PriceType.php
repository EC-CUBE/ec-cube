<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PriceType extends AbstractType
{
    public function __construct($config = array('price_len' => 8))
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $defaultValues = array(
            new Assert\Length(array('max' => $this->config['price_len'])),
            new Assert\GreaterThanOrEqual(array('value' => 0)),
        );

        $constraints = function (Options $options) use ($defaultValues) {
            if (false !== $options['required']) {
                return array_merge($defaultValues, array(new Assert\NotBlank()));
            }
            return $defaultValues;
        };

        $resolver->setDefaults(array(
            'currency' => 'JPY',
            'precision' => 0,
            'scale' => 0,
            'grouping' => true,
            'constraints' => $constraints,
            'invalid_message' => 'form.type.numeric.invalid'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'money';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'price';
    }
}
