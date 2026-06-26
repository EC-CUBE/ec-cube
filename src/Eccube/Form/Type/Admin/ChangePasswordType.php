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

namespace Eccube\Form\Type\Admin;

use Eccube\Common\EccubeConfig;
use Eccube\Form\Validator\PasswordBlocklist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordType extends AbstractType
{
    /**
     * ChangePasswordType constructor.
     */
    public function __construct(protected EccubeConfig $eccubeConfig)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $options
     */
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $changePasswordConstraints = [
            new Assert\NotBlank(),
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
            $changePasswordConstraints[] = new Assert\NotCompromisedPassword(['skipOnError' => true]);
        }

        $builder
            ->add('current_password', PasswordType::class, [
                'label' => 'changepassword.label.current_pass',
                'constraints' => [
                    new Assert\NotBlank(),
                    new UserPassword(),
                ],
            ])
            ->add('change_password', RepeatedType::class, [
                'first_options' => [
                    'label' => 'changepassword.label.new_pass',
                ],
                'second_options' => [
                    'label' => 'changepassword.label.verify_pass',
                ],
                'constraints' => $changePasswordConstraints,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'admin_change_password';
    }
}
