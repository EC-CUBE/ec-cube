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
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ClientType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * ClientType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        EccubeConfig $eccubeConfig
    ) {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier', TextType::class, [
                'mapped' => false,
                'data' => hash('md5', random_bytes(16)),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 32]),
                    new Assert\Regex(['pattern' => '/^[0-9a-zA-Z]+$/']),
                ],
            ])
            ->add('secret', TextType::class, [
                'mapped' => false,
                'data' => hash('sha512', random_bytes(32)),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 128]),
                    new Assert\Regex(['pattern' => '/^[0-9a-zA-Z]+$/']),
                ],
            ])
            ->add('scopes', TextType::class, [
                'mapped' => false,
                'data' => 'read',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('redirect_uris', TextType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                    new Assert\Url(),
                ],
            ])
            ->add('grants', TextType::class, [
                'mapped' => false,
                'data' => 'authorization_code',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_client';
    }
}
