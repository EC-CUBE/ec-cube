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

use Silex\Application;
use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\Extension\Core\Type;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;

class NonMemberType extends AbstractType
{
    public $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder
            ->add('name', 'name', array(
                'options' => array(
                    'attr' => array(
                        'maxlength' => $app['config']['stext_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('max' => $app['config']['stext_len'])),
                    ),
                ),
            ))
            ->add('kana', 'name', array(
                'options' => array(
                    'attr' => array(
                        'maxlength' => $app['config']['stext_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('max' => $app['config']['stext_len'])),
                        new Assert\Regex(array(
                            'pattern' => "/^[ァ-ヶｦ-ﾟー]+$/u",
                        )),
                    ),
                ),
            ))
            ->add('company_name', 'text', array(
                'label' => '会社名',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                ),
            ))
            ->add('zip', 'zip', array())
            ->add('address', 'address', array(
                'help' => 'form.contact.address.help',
                'options' => array(
                    'attr' => array(
                        'maxlength' => $app['config']['stext_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
            ))
            ->add('tel', 'tel', array(
                'tel01_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('min' => 2, 'max' => 4)),
                        new Assert\Regex(array('pattern' => '/\A\d+\z/')),
                    ),
                ),
                'tel02_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('min' => 2, 'max' => 4)),
                        new Assert\Regex(array('pattern' => '/\A\d+\z/')),
                    ),
                ),
                'tel03_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('min' => 2, 'max' => 4)),
                        new Assert\Regex(array('pattern' => '/\A\d+\z/')),
                    ),
                ),
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
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'nonmember';
    }
}
