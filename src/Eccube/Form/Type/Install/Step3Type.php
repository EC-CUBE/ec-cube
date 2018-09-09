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

namespace Eccube\Form\Type\Install;

use Eccube\Common\EccubeConfig;
use Eccube\Form\Validator\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Step3Type extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(ValidatorInterface $validator, EccubeConfig $eccubeConfig)
    {
        $this->validator = $validator;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shop_name', TextType::class, [
                'label' => trans('install.shop_name'),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => trans('install.mail_address'),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Email(['strict' => $this->eccubeConfig['eccube_rfc_email_check']]),
                ],
            ])
            ->add('login_id', TextType::class, [
                'label' => trans('install.login_id', [
                    '%min%' => $this->eccubeConfig['eccube_id_min_len'],
                    '%max%' => $this->eccubeConfig['eccube_id_max_len'],
                ]),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => $this->eccubeConfig['eccube_id_min_len'],
                        'max' => $this->eccubeConfig['eccube_id_max_len'],
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[[:graph:][:space:]]+$/i',
                        'message' => 'form_error.graph_only',
                    ]),
                ],
            ])
            ->add('login_pass', PasswordType::class, [
                'label' => trans('install.login_password', [
                    '%min%' => $this->eccubeConfig['eccube_password_min_len'],
                    '%max%' => $this->eccubeConfig['eccube_password_max_len'],
                ]),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => $this->eccubeConfig['eccube_password_min_len'],
                        'max' => $this->eccubeConfig['eccube_password_max_len'],
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[[:graph:][:space:]]+$/i',
                        'message' => 'form_error.graph_only',
                    ]),
                ],
            ])
            ->add('admin_dir', TextType::class, [
                'label' => trans('install.directory_name', [
                    '%min%' => $this->eccubeConfig['eccube_id_min_len'],
                    '%max%' => $this->eccubeConfig['eccube_id_max_len'],
                ]),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => $this->eccubeConfig['eccube_id_min_len'],
                        'max' => $this->eccubeConfig['eccube_id_max_len'],
                    ]),
                    new Assert\Regex(['pattern' => '/\A\w+\z/']),
                ],
            ])
            ->add('admin_force_ssl', CheckboxType::class, [
                'label' => trans('install.https_only'),
                'required' => false,
            ])
            ->add('admin_allow_hosts', TextareaType::class, [
                'label' => trans('install.ip_restriction'),
                'required' => false,
            ])
            ->add('smtp_host', TextType::class, [
                'label' => trans('install.smtp_host'),
                'required' => false,
            ])
            ->add('smtp_port', TextType::class, [
                'label' => trans('install.smtp_port'),
                'required' => false,
            ])
            ->add('smtp_username', TextType::class, [
                'label' => trans('install.smtp_user'),
                'required' => false,
            ])
            ->add('smtp_password', PasswordType::class, [
                'label' => trans('install.smtp_password'),
                'required' => false,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();

                $ips = preg_split("/\R/", $data['admin_allow_hosts'], null, PREG_SPLIT_NO_EMPTY);

                foreach ($ips as $ip) {
                    $errors = $this->validator->validate($ip, [
                            new Assert\Ip(),
                        ]
                    );
                    if ($errors->count() != 0) {
                        $form['admin_allow_hosts']->addError(new FormError(trans('install.ip_is_invalid', ['%ip%' => $ip])));
                    }
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'install_step3';
    }
}
