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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * SecurityType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param ValidatorInterface $validator
     */
    public function __construct(EccubeConfig $eccubeConfig, ValidatorInterface $validator, RequestStack $requestStack)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->validator = $validator;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $allowHosts = $this->eccubeConfig->get('eccube_admin_allow_hosts');
        $allowHosts = implode("\n", $allowHosts);

        $denyHosts = $this->eccubeConfig->get('eccube_admin_deny_hosts');
        $denyHosts = implode("\n", $denyHosts);

        $builder
            ->add('admin_route_dir', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                    new Assert\Regex([
                        'pattern' => '/\A\w+\z/',
                    ]),
                ],
                'data' => $this->eccubeConfig->get('eccube_admin_route'),
            ])
            ->add('admin_allow_hosts', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_ltext_len']]),
                ],
                'data' => $allowHosts,
            ])
            ->add('admin_deny_hosts', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_ltext_len']]),
                ],
                'data' => $denyHosts,
            ])
            ->add('force_ssl', CheckboxType::class, [
                'label' => 'admin.setting.system.security.force_ssl',
                'required' => false,
                'data' => $this->eccubeConfig->get('eccube_force_ssl'),
            ])
            ->add('trusted_hosts', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                    new Assert\Regex([
                        'pattern' => '/^[\x21-\x7e]+$/',
                    ]),
                ],
                'data' => env('TRUSTED_HOSTS'),
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
                        $form['admin_allow_hosts']->addError(new FormError(trans('admin.setting.system.security.ip_limit_invalid_ipv4', ['%ip%' => $ip])));
                    }
                }

                $ips = preg_split("/\R/", $data['admin_deny_hosts'], null, PREG_SPLIT_NO_EMPTY);

                foreach ($ips as $ip) {
                    $errors = $this->validator->validate($ip, [
                            new Assert\Ip(),
                        ]
                    );
                    if ($errors->count() != 0) {
                        $form['admin_deny_hosts']->addError(new FormError(trans('admin.setting.system.security.ip_limit_invalid_ipv4', ['%ip%' => $ip])));
                    }
                }

                $request = $this->requestStack->getCurrentRequest();
                if ($data['force_ssl'] && !$request->isSecure()) {
                    $form['force_ssl']->addError(new FormError(trans('admin.setting.system.security.ip_limit_invalid_https')));
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_security';
    }
}
