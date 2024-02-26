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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class LoginType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var SessionInterface
     */
    protected $session;

    public function __construct(
        EccubeConfig $eccubeConfig,
        SessionInterface $session
    ) {
        $this->eccubeConfig = $eccubeConfig;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('login_id', TextType::class, [
            'attr' => [
                'maxlength' => $this->eccubeConfig['eccube_id_max_len'],
            ],
            'constraints' => [
                new Assert\NotBlank(),
            ],
            'data' => $this->session->get('_security.last_username'),
        ]);
        $builder->add('password', PasswordType::class, [
            'attr' => [
                'maxlength' => $this->eccubeConfig['eccube_password_max_len'],
            ],
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_login';
    }
}
