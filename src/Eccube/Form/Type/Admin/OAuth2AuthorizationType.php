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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class OAuth2AuthorizationType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TODO
        $builder
            ->add('client_id', HiddenType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('client_secret', HiddenType::class, [
//                'constraints' => [
//                     new Assert\NotBlank(),
//                ],
            ])
            ->add('redirect_uri', HiddenType::class, [
//                'constraints' => [
//                    new Assert\NotBlank(),
//                ],
            ])
            ->add('response_type', HiddenType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('state', HiddenType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('scope', HiddenType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ]);
    }

    public function getBlockPrefix()
    {
        return 'oauth_authorization';
    }
}
