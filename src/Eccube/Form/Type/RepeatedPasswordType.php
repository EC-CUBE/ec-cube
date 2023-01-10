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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\ValueToDuplicatesTransformer;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RepeatedPasswordType
 */
class RepeatedPasswordType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * RepeatedPasswordType constructor.
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type' => TextType::class, // type password だと入力欄を空にされてしまうので、widgetで対応
            'invalid_message' => 'form_error.same_password',
            'required' => true,
            'error_bubbling' => false,
            'options' => [
                'constraints' => [
                    new Assert\Length([
                        'min' => $this->eccubeConfig['eccube_password_min_len'],
                        'max' => $this->eccubeConfig['eccube_password_max_len'],
                    ]),
                    new Assert\Regex([
                        'pattern' => $this->eccubeConfig['eccube_password_pattern'],
                        'message' => 'form_error.password_pattern_invalid',
                    ]),
                ],
            ],
            'first_options' => [
                'attr' => [
                    'placeholder' => trans('common.password_sample', [
                        '%min%' => $this->eccubeConfig['eccube_password_min_len'],
                        '%max%' => $this->eccubeConfig['eccube_password_max_len'], ]),
                ],
            ],
            'second_options' => [
                'attr' => [
                    'placeholder' => 'common.repeated_confirm',
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return RepeatedType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'repeated_password';
    }
}
