<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ZipType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function __construct($config = array('zip01_len' => 3, 'zip02_len' => 4))
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['zip01_options']['required'] = $options['required'];
        $options['zip02_options']['required'] = $options['required'];

        // required の場合は NotBlank も追加する
        if ($options['required']) {
            $options['options']['constraints'] = array_merge(array(
                new Assert\NotBlank(array()),
            ), $options['options']['constraints']);
        }

        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }

        if (empty($options['zip01_name'])) {
            $options['zip01_name'] = $builder->getName().'01';
        }
        if (empty($options['zip02_name'])) {
            $options['zip02_name'] = $builder->getName().'02';
        }

        $builder
            ->add($options['zip01_name'], 'text', array_merge_recursive($options['options'], $options['zip01_options']))
            ->add($options['zip02_name'], 'text', array_merge_recursive($options['options'], $options['zip02_options']))
        ;

        $builder->setAttribute('zip01_name', $options['zip01_name']);
        $builder->setAttribute('zip02_name', $options['zip02_name']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $builder = $form->getConfig();
        $view->vars['zip01_name'] = $builder->getAttribute('zip01_name');
        $view->vars['zip02_name'] = $builder->getAttribute('zip02_name');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'options' => array('constraints' => array()),
            'zip01_options' => array(
                'constraints' => array(
                    new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
                    new Assert\Length(array('min' => $this->config['zip01_len'], 'max' => $this->config['zip01_len'])),
                ),
            ),
            'zip02_options' => array(
                'constraints' => array(
                    new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
                    new Assert\Length(array('min' => $this->config['zip02_len'], 'max' => $this->config['zip02_len'])),
                ),
            ),
            'zip01_name' => '',
            'zip02_name' => '',
            'error_bubbling' => false,
            'inherit_data' => true,
            'trim' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'zip';
    }
}
