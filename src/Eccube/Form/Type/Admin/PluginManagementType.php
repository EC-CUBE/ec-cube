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

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\Extension\Core\Type;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;
use \Symfony\Component\OptionsResolver\OptionsResolverInterface;


class PluginManagementType extends AbstractType
{

    public function __construct()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $plugin_id=$options['plugin_id'];
        $enable=$options['enable'];

        $builder
            ->add('uninstall', 'submit') 
            ->add('enable', 'submit',array(
                'disabled'=>(boolean)$enable
            )) 
            ->add('disable', 'submit',array(
                'disabled'=>!(boolean)$enable
            )) 
            ->add('plugin_id', 'hidden', array(
                'data' => $plugin_id,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('plugin_archive', 'file', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'required' => false
            ))
            ->add('update', 'submit') 
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'plugin_management';
    }
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('plugin_id','enable'));
#        $resolver->setRequired('enable');
/*
        $resolver->setDefaults(array(
            'id_add_product_id' => true,
            'constraints' => array(
                new Assert\Callback(array($this, 'validate')),
            ),
        ));
*/
    }

}
