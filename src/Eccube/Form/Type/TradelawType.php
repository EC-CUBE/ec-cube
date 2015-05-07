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

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;

class TradelawType extends AbstractType
{
    public $app;

    public function __construct (\Eccube\Application $app)
    {
        $this->app = $app;
    }

    public function getName()
    {
        return 'tradelaw';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('law_company', 'text', array(
                'required' => true
            ))
            ->add('law_manager', 'text', array(
                'required' => true
            ))
            ->add('law_zip01', 'text', array('required' => true, 'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Length(array('min' => 3, 'max' => 3)),
                new Assert\Regex(array('pattern' => '/\A\d+\z/')),
             )))
            ->add('law_zip02', 'text', array('required' => true, 'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Length(array('min' => 4, 'max' => 4)),
                new Assert\Regex(array('pattern' => '/\A\d+\z/')),
             )))
            ->add('law_pref', 'pref', array(
                'required' => true
            ))
            ->add('law_addr01', 'text', array(
                'required' => true
            ))
            ->add('law_addr02', 'text', array(
                'required' => true
            ))
            ->add('law_tel01', 'text', array(
                'required' => true
            ))
            ->add('law_tel01', 'text', array(
                'required' => true,
                'attr' => array(
                    'maxlength' => 4,
                ),
                'constraints' => array(
                    new Assert\Length(array('min' => 2, 'max' => 4)),
                    new Assert\Regex(array('pattern' => '/\A\d+\z/')),
                ),
            ))
            ->add('law_tel02', 'text', array(
                'required' => true,
                'attr' => array(
                    'maxlength' => 4,
                ),
                'constraints' => array(
                    new Assert\Length(array('min' => 2, 'max' => 4)),
                    new Assert\Regex(array('pattern' => '/\A\d+\z/')),
                ),
            ))
            ->add('law_tel03', 'text', array(
                'required' => true,
                'attr' => array(
                    'maxlength' => 4,
                ),
                'constraints' => array(
                    new Assert\Length(array('min' => 2, 'max' => 4)),
                    new Assert\Regex(array('pattern' => '/\A\d+\z/')),
                ),
            ))
            ->add('law_fax01', 'text', array(
                'required' => true,
                'attr' => array(
                    'maxlength' => 4,
                ),
                'constraints' => array(
                    new Assert\Length(array('min' => 2, 'max' => 4)),
                    new Assert\Regex(array('pattern' => '/\A\d+\z/')),
                ),
            ))
            ->add('law_fax02', 'text', array(
                'required' => true,
                'attr' => array(
                    'maxlength' => 4,
                ),
                'constraints' => array(
                    new Assert\Length(array('min' => 2, 'max' => 4)),
                    new Assert\Regex(array('pattern' => '/\A\d+\z/')),
                ),
            ))
            ->add('law_fax03', 'text', array(
                'required' => true,
                'attr' => array(
                    'maxlength' => 4,
                ),
                'constraints' => array(
                    new Assert\Length(array('min' => 2, 'max' => 4)),
                    new Assert\Regex(array('pattern' => '/\A\d+\z/')),
                ),
            ))
            ->add('law_email', 'email', array('constraints' => array(
                new Assert\NotBlank(),
                new Assert\Email(),
             )))
            ->add('law_url', 'text', array(
                'required' => true
            ))
            ->add('law_term01', 'textarea', array(
                'required' => true
            ))
            ->add('law_term02', 'textarea', array(
                'required' => true
            ))
            ->add('law_term03', 'textarea', array(
                'required' => true
            ))
            ->add('law_term04', 'textarea', array(
                'required' => true
            ))
            ->add('law_term05', 'textarea', array(
                'required' => true
            ))
            ->add('law_term06', 'textarea', array(
                'required' => true
            ))
            ->add('save', 'submit', array('label' => 'この内容で登録する'));
    }
}
