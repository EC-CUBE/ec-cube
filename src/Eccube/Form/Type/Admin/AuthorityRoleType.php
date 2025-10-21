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

use Eccube\Entity\AuthorityRole;
use Eccube\Entity\Master\Authority;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class AuthorityRoleType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Authority', EntityType::class, [
                'class' => Authority::class,
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'placeholder' => 'common.select',
            ])
            ->add('deny_url', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\\/.*/',
                        'message' => trans('admin.setting.system.authority.deny_url_is_invalid'),
                    ]),
                ],
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();

                $Authority = $form['Authority']->getData();
                $denyUrl = $form['deny_url']->getData();

                if (!$Authority && !empty($denyUrl)) {
                    $form['Authority']->addError(new FormError(trans('admin.setting.system.authority.authority_not_selected')));
                } elseif ($Authority && empty($denyUrl)) {
                    $form['deny_url']->addError(new FormError(trans('admin.setting.system.authority.deny_url_is_empty')));
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AuthorityRole::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'admin_authority_role';
    }
}
