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

use Eccube\Common\EccubeConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * PriceType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig, ContainerInterface $container)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $currency = $this->container->getParameter('currency');
        $scale = Intl::getCurrencyBundle()->getFractionDigits($currency);
        $max = $this->eccubeConfig['eccube_price_max'];
        $min = -$max;

        $constraints = function (Options $options) use ($max, $min) {
            $constraints = [];
            // requiredがtrueに指定されている場合, NotBlankを追加
            if (isset($options['required']) && true === $options['required']) {
                $constraints[] = new NotBlank();
            }

            if (isset($options['accept_minus']) && true === $options['accept_minus']) {
                $constraints[] = new Range([
                    'min' => $min,
                    'max' => $max,
                ]);
            } else {
                $constraints[] = new Range(['min' => 0, 'max' => $max]);
            }

            return $constraints;
        };

        $resolver->setDefaults(
            [
                'currency' => $currency,
                'scale' => $scale,
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
