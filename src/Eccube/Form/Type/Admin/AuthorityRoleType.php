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

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'label' => 'authorityrole.label.auth',
                'class' => 'Eccube\Entity\Master\Authority',
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'placeholder' => 'form.empty_value',
            ])
            ->add('deny_url', TextType::class, [
                'label' => 'authorityrole.label.denied_url',
                'required' => false,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();

                $Authority = $form['Authority']->getData();
                $denyUrl = $form['deny_url']->getData();

                if (!$Authority && !empty($denyUrl)) {
                    $form['Authority']->addError(new FormError('権限が選択されていません。'));
                } elseif ($Authority && empty($denyUrl)) {
                    $form['deny_url']->addError(new FormError('拒否URLが入力されていません。'));
                }
            })
        ;
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
