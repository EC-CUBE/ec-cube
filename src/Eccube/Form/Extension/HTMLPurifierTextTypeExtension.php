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

namespace Eccube\Form\Extension;

use Eccube\Form\EventListener\HTMLPurifierListener;
use Eccube\Request\Context;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HTMLPurifierTextTypeExtension extends AbstractTypeExtension
{
    /**
     * @var Context
     */
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        if ($this->context->isFront()) {
            $resolver->setDefault('purify_html', true);
        }
    }

    /**
     * @return string
     */
    public function getExtendedType(): string
    {
        return TextType::class;
    }

    /**
     * @return iterable
     */
    public static function getExtendedTypes(): iterable
    {
        yield TextType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->context->isFront() && $options['purify_html']) {
            $builder->addEventSubscriber(
                new HTMLPurifierListener()
            );
        }
    }
}
