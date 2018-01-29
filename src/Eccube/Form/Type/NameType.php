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


namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class NameType extends AbstractType
{
    /**
     * @var array
     */
    protected $eccubeConfig;

    /**
     * NameType constructor.
     *
     * @param array $eccubeConfig
     */
    public function __construct(
        array $eccubeConfig
    ) {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['lastname_options']['required'] = $options['required'];
        $options['firstname_options']['required'] = $options['required'];

        // required の場合は NotBlank も追加する
        if ($options['required']) {
            $options['lastname_options']['constraints'] = array_merge([
                new Assert\NotBlank(),
            ], $options['lastname_options']['constraints']);

            $options['firstname_options']['constraints'] = array_merge([
                new Assert\NotBlank(),
            ], $options['firstname_options']['constraints']);
        }

        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }

        if (empty($options['lastname_name'])) {
            $options['lastname_name'] = $builder->getName() . '01';
        }
        if (empty($options['firstname_name'])) {
            $options['firstname_name'] = $builder->getName() . '02';
        }

        $builder
            ->add($options['lastname_name'], TextType::class, array_merge_recursive($options['options'], $options['lastname_options']))
            ->add($options['firstname_name'], TextType::class, array_merge_recursive($options['options'], $options['firstname_options']))
        ;

        $builder->setAttribute('lastname_name', $options['lastname_name']);
        $builder->setAttribute('firstname_name', $options['firstname_name']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $builder = $form->getConfig();
        $view->vars['lastname_name'] = $builder->getAttribute('lastname_name');
        $view->vars['firstname_name'] = $builder->getAttribute('firstname_name');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'options' => array(),
            'lastname_options' => array(
                'attr' => array(
                    'placeholder' => 'Name01',
                ),
                'constraints' => array(
                    new Assert\Length(array(
                         'max' => $this->eccubeConfig['name_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[^\s ]+$/u',
                        'message' => 'form.type.name.firstname.nothasspace'
                    ))
                ),
            ),
            'firstname_options' => array(
                'attr' => array(
                    'placeholder' => 'Name02',
                ),
                'constraints' => array(
                    new Assert\Length(array(
                         'max' => $this->eccubeConfig['name_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[^\s ]+$/u',
                        'message' => 'form.type.name.lastname.nothasspace'
                    ))
                ),
            ),
            'lastname_name' => '',
            'firstname_name' => '',
            'error_bubbling' => false,
            'inherit_data' => true,
            'trim' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'name';
    }
}
