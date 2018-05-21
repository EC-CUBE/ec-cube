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

namespace Eccube\Form\Type;

use Eccube\Common\EccubeConfig;
use Symfony\Component\Form\AbstractType;
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
            'entry_type' => TextType::class, // type password だと入力欄を空にされてしまうので、widgetで対応
            'required' => true,
            'error_bubbling' => false,
            'invalid_message' => 'form.member.password.invalid',
            'options' => [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => $this->eccubeConfig['eccube_password_min_len'],
                        'max' => $this->eccubeConfig['eccube_password_max_len'],
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[[:graph:][:space:]]+$/i',
                        'message' => 'form.type.graph.invalid',
                    ]),
                ],
            ],
            'first_options' => [
                'attr' => [
                    'placeholder' => '半角英数字記号'.$this->eccubeConfig['eccube_password_min_len'].'～'.$this->eccubeConfig['eccube_password_max_len'].'文字',
                ],
            ],
            'second_options' => [
                'attr' => [
                    'placeholder' => 'form.member.repeated.confirm',
                ],
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
