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


namespace Eccube\Form\Type\Front;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\Extension\Core\Type;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;

class EntryType extends AbstractType
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'name', array(
                'required' => true,
                'options' => array(
                    'attr' => array(
                        'maxlength' => $this->config['stext_len'],
                    ),
                ),
            ))
            ->add('kana', 'kana', array(
                'required' => true,
                'options' => array(
                    'attr' => array(
                        'maxlength' => $this->config['stext_len'],
                    ),
                ),
            ))
            ->add('company_name', 'text', array(
                'label' => '会社名',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->config['stext_len'],
                    ))
                ),
            ))
            ->add('zip', 'zip', array())
            ->add('address', 'address', array(
                'help' => 'form.contact.address.help',
                'options' => array(
                    'attr' => array(
                        'maxlength' => $this->config['stext_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
            ))
            ->add('tel', 'tel', array(
                'required' => true,
                'options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
            ))
            ->add('fax', 'tel', array(
                'label' => 'FAX番号',
                'required' => false,
            ))
            ->add('email', 'repeated', array(
                'invalid_message' => 'form.member.email.invalid',
                'options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Email(),
                    ),
                ),
            ))
            ->add('password', 'text', array(
                'label' => 'パスワード',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => $this->config['password_min_len'],
                        'max' => $this->config['password_max_len'],
                    )),
                    new Assert\Regex(array('pattern' => '/^[[:graph:][:space:]]+$/i')),
                ),
            ))
            ->add('birth', 'birthday', array(
                'label' => '生年月日',
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y')-80, date('Y')),
                'widget' => 'choice',
                'format' => 'yyyy/MM/dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('sex', 'sex', array(
                'label' => '性別',
                'required' => false,
            ))
            ->add('job', 'job', array(
                'label' => '職業',
                'required' => false,
            ))
            ->add('save', 'submit', array('label' => 'この内容で登録する'))
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'entry';
    }
}
