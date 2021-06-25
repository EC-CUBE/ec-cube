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
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('Authority', EntityType::class, [
            'class' => 'Eccube\Entity\Master\Authority',
            'expanded' => false,
            'multiple' => false,
            'required' => false,
            'placeholder' => 'common.select',
        ])
        ->add('Role', EntityType::class, [
            'class' => 'Eccube\Entity\Master\Role',
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
        ->add('read_only_url', TextType::class, [
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
            $Role = $form['Role']->getData();
            $denyUrl = $form['deny_url']->getData();
            $readOnlyUrl = $form['read_only_url']->getData();

            if (!$Authority) {
                $form['Authority']->addError(new FormError(trans('admin.setting.system.authority.authority_not_selected')));
            }
            if (empty($denyUrl) && empty($readOnlyUrl)) {
                $form['deny_url']->addError(new FormError(trans('admin.setting.system.authority.deny_url_is_empty')));
            }
            if (!$Role) {
                $form['Role']->addError(new FormError(trans('admin.setting.system.authority.role_not_selected')));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Eccube\Entity\AuthorityRole',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_authority_role';
    }
}
