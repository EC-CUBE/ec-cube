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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class PriceType extends AbstractType
{
    /**
     * @var string
     */
    protected $currency;

    /**
     * @var int
     */
    protected $scale;

    /**
     * @var int
     */
    protected $acceptMinus;

    /**
     * @var int
     */
    protected $max;

    public function __construct($currency = 'JPY')
    {
        $this->currency = $currency;
        $this->scale = Intl::getCurrencyBundle()->getFractionDigits($this->currency);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $constraints = function (Options $options) {
            $constraints = [];

            // requiredがtrueに指定されている場合, NotBlankを追加
            if (isset($options['required']) && true === $options['required']) {
                $constraints[] = new NotBlank();
            }

            if (isset($options['accept_minus']) && true === $options['accept_minus']) {
                // マイナス値を許容する場合は最大値のみチェックする
                $constraints[] = new Range(['max' => PHP_INT_MAX]);
            } else {
                $constraints[] = new Range(['min' => 0, 'max' => PHP_INT_MAX]);
            }

            return $constraints;
        };

        $resolver->setDefaults(
            [
                'currency' => $this->currency,
                'scale' => $this->scale,
                'grouping' => true,
                'constraints' => $constraints,
                'accept_minus' => false, // マイナス値を許容するかどうか
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return MoneyType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'price';
    }
}
