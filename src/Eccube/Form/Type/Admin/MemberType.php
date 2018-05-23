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

namespace Eccube\Form\Type\Admin;

use Eccube\Common\EccubeConfig;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MemberType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var string
     */
    protected $blankMessage = 'admin.system.member.form.not.blank';

    /**
     * MemberType constructor.
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
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'member.label.name',
                'constraints' => [
                    new Assert\NotBlank(['message' => $this->blankMessage]),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('department', TextType::class, [
                'required' => false,
                'label' => 'member.label.organization',
                'constraints' => [
                    new Assert\NotBlank(['message' => $this->blankMessage]),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('login_id', TextType::class, [
                'label' => 'member.label.login_id',
                'constraints' => [
                    new Assert\NotBlank(['message' => $this->blankMessage]),
                    new Assert\Length([
                        'min' => $this->eccubeConfig['eccube_id_min_len'],
                        'max' => $this->eccubeConfig['eccube_id_max_len'],
                    ]),
                    new Assert\Regex(['pattern' => '/^[[:graph:][:space:]]+$/i']),
                ],
            ])
            ->add('password', RepeatedType::class, [
                // 'type' => 'password',
                'first_options' => [
                    'label' => 'member.label.pass',
                ],
                'second_options' => [
                    'label' => 'member.label.varify_pass',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => $this->blankMessage]),
                    new Assert\Length([
                        'min' => $this->eccubeConfig['eccube_id_min_len'],
                        'max' => $this->eccubeConfig['eccube_id_max_len'],
                    ]),
                    new Assert\Regex(['pattern' => '/^[[:graph:][:space:]]+$/i']),
                ],
            ])
            ->add('Authority', EntityType::class, [
                'label' => 'admin.setting.system.member.689',
                'class' => 'Eccube\Entity\Master\Authority',
                'expanded' => false,
                'multiple' => false,
                'placeholder' => 'form.empty_value',
                'constraints' => [
                    new Assert\NotBlank(['message' => $this->blankMessage]),
                ],
            ])
            ->add('Work', EntityType::class, [
                'label' => 'admin.setting.system.member.690',
                'class' => 'Eccube\Entity\Master\Work',
                'expanded' => true,
                'multiple' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => $this->blankMessage]),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Eccube\Entity\Member',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_member';
    }
}
