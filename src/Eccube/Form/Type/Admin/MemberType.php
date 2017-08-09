<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Form\Type\Admin;

use Eccube\Annotation\Inject;
use Eccube\Application;
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
     * @var \Eccube\Application $app
     * @Inject(Application::class)
     */
    protected $app;

    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => '名前',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('max' => $this->app['config']['stext_len'])),
                ),
            ))
            ->add('department', TextType::class, array(
                'required' => false,
                'label' => '所属',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('max' => $this->app['config']['stext_len'])),
                ),
            ))
            ->add('login_id', TextType::class, array(
                'label' => 'ログインID',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => $this->app['config']['id_min_len'],
                        'max' => $this->app['config']['id_max_len'],
                    )),
                    new Assert\Regex(array('pattern' => '/^[[:graph:][:space:]]+$/i')),
                ),
            ))
            ->add('password', RepeatedType::class, array(
                // 'type' => 'password',
                'first_options'  => array(
                    'label' => 'パスワード',
                ),
                'second_options' => array(
                    'label' => 'パスワード(確認)',
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => $this->app['config']['id_min_len'],
                        'max' => $this->app['config']['id_max_len'],
                    )),
                    new Assert\Regex(array('pattern' => '/^[[:graph:][:space:]]+$/i')),
                ),
            ))
            ->add('Authority', EntityType::class, array(
                'label' => '権限',
                'class' => 'Eccube\Entity\Master\Authority',
                'expanded' => false,
                'multiple' => false,
                'placeholder' => 'form.empty_value',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('Work', EntityType::class, array(
                'label' => '稼働/非稼働',
                'class' => 'Eccube\Entity\Master\Work',
                'expanded' => true,
                'multiple' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\Member',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_member';
    }
}
