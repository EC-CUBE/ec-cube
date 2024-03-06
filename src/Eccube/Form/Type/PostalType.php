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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ZipType
 */
class PostalType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * ZipType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new \Eccube\Form\EventListener\ConvertKanaListener());
        $builder->addEventSubscriber(new \Eccube\Form\EventListener\TruncateHyphenListener());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setNormalizer('constraints', function($options, $value) {
            $constraints = [];
            // requiredがtrueに指定されている場合, NotBlankを追加
            if (isset($options['required']) && true === $options['required']) {
                $constraints[] = new Assert\NotBlank();
            }

            $constraints[] = new Assert\Length([
                'max' => $this->eccubeConfig['eccube_postal_code'],
            ]);

            $constraints[] = new Assert\Type([
                'type' => 'digit',
                'message' => 'form_error.numeric_only',
            ]);

            return array_merge($constraints, $value);
        });

        $resolver->setDefaults([
            'options' => [],
            'attr' => [
                'class' => 'p-postal-code',
                'placeholder' => 'common.postal_code_sample',
            ],
            'trim' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TelType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'postal';
    }
}
