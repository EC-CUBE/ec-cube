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
use Eccube\Form\Validator\PasswordBlocklist;
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
     * RepeatedPasswordType constructor.
     */
    public function __construct(protected EccubeConfig $eccubeConfig)
    {
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $constraints = [
            new Assert\Length([
                'min' => $this->eccubeConfig['eccube_password_min_len'],
                'max' => $this->eccubeConfig['eccube_password_max_len'],
            ]),
            new Assert\Regex([
                'pattern' => $this->eccubeConfig['eccube_password_pattern'],
                'message' => 'form_error.password_pattern_invalid',
            ]),
            new PasswordBlocklist(),
        ];
        // NIST SP 800-63B-4 対応の漏洩パスワードチェック. 閉域網等では config で無効化できる.
        if ($this->eccubeConfig['eccube_password_compromised_check']) {
            $constraints[] = new Assert\NotCompromisedPassword(['skipOnError' => true]);
        }

        $resolver->setDefaults([
            'type' => TextType::class, // type password だと入力欄を空にされてしまうので、widgetで対応
            'invalid_message' => 'form_error.same_password',
            'required' => true,
            'error_bubbling' => false,
            'options' => [
                'constraints' => $constraints,
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
                    new Assert\NotBlank(),
                ],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getParent(): ?string
    {
        return RepeatedType::class;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'repeated_password';
    }
}
