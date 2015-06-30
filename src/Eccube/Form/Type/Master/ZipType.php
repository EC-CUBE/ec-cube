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


namespace Eccube\Form\Type\Master;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ZipType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['zip01_options']['required'] = $options['required'];
        $options['zip02_options']['required'] = $options['required'];

        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }

        if (empty($options['zip01_name'])) {
            $options['zip01_name'] = $builder->getName() . '01';
        }
        if (empty($options['zip02_name'])) {
            $options['zip02_name'] = $builder->getName() . '02';
        }

        $builder
            ->add($options['zip01_name'], 'text', array_merge($options['options'], $options['zip01_options']))
            ->add($options['zip02_name'], 'text', array_merge($options['options'], $options['zip02_options']))
        ;

        $builder->setAttribute('zip01_name', $options['zip01_name']);
        $builder->setAttribute('zip02_name', $options['zip02_name']);
        $builder->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'options' => array(),
            'zip01_options' => array(
                'attr' => array(
                    'maxlength' => 3,
                ),
                'constraints' => array(
                    new Assert\Length(array('min' => 3, 'max' => 3)),
                    new Assert\Regex(array('pattern' => '/^\d{3}$/')),
                ),
            ),
            'zip02_options' => array(
                'attr' => array(
                    'maxlength' => 4,
                ),
                'constraints' => array(
                    new Assert\Length(array('min' => 4, 'max' => 4)),
                    new Assert\Regex(array('pattern' => '/^\d{4}$/')),
                ),
            ),
            'zip01_name' => '',
            'zip02_name' => '',
            'error_bubbling' => false,
            'inherit_data' => true,
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
