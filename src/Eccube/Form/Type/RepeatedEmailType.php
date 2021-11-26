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
use Eccube\Form\Validator\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RepeatedEmailType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * ContactType constructor.
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
            'entry_type' => EmailType::class,
            'required' => true,
            'invalid_message' => 'form_error.same_email',
            'options' => [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Email(['strict' => $this->eccubeConfig['eccube_rfc_email_check']]),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ],
            'first_options' => [
                'attr' => [
                    'placeholder' => 'common.mail_address_sample',
                ],
            ],
            'second_options' => [
                'attr' => [
                    'placeholder' => 'common.repeated_confirm',
                ],
            ],
            'error_bubbling' => false,
            'trim' => true,
            'error_mapping' => function (Options $options) {
                return ['.' => $options['second_name']];
            },
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
        return 'repeated_email';
    }
}
