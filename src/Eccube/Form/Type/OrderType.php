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

use Eccube\Form\DataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class OrderType extends AbstractType
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $config = $this->app['config'];

        $builder
            ->add('name', 'name', array(
                'required' => true,
                'options' => array(
                    'attr' => array(
                        'maxlength' => $config['stext_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('max' => $config['stext_len'])),
                    ),
                ),
            ))
            ->add('kana', 'name', array(
                'options' => array(
                    'attr' => array(
                        'maxlength' => $config['stext_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('max' => $config['stext_len'])),
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
                        'max' => $config['stext_len'],
                    ))
                ),
            ))
            ->add('zip', 'zip', array())
            ->add('address', 'address', array(
                'addr01_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'max' => $config['mtext_len'],
                        )),
                    ),
                ),
                'addr02_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'max' => $config['mtext_len'],
                        )),
                    ),
                ),
            ))
            ->add('email', 'email', array(
                'label' => 'メールアドレス',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ),
            ))
            ->add('tel', 'tel', array())
            ->add('fax', 'tel', array(
                'label' => 'FAX番号',
                'required' => false,
            ))
            ->add('company_name', 'text', array(
                'label' => '会社名',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['stext_len'],
                    ))
                ),
            ))
            ->add('message', 'textarea', array(
                'label' => '備考',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['ltext_len'],
                    )),
                ),
            ))
            ->add('discount', 'money', array(
                'label' => '値引き',
                'currency' => 'JPY',
                'precision' => 0,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $config['int_len'],
                    )),
                ),
            ))
            ->add('delivery_fee_total', 'money', array(
                'label' => '送料',
                'currency' => 'JPY',
                'precision' => 0,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $config['int_len'],
                    )),
                ),
            ))
            ->add('charge', 'money', array(
                'label' => '手数料',
                'currency' => 'JPY',
                'precision' => 0,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $config['int_len'],
                    )),
                ),
            ))
            ->add('note', 'textarea', array(
                'label' => 'SHOP用メモ欄',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['ltext_len'],
                    )),
                ),
            ))
            ->add('OrderStatus', 'entity', array(
                'class' => 'Eccube\Entity\Master\OrderStatus',
                'property' => 'name',
                'empty_value' => false,
                'empty_data' => null,
            ))
            ->add('Payment', 'entity', array(
                'class' => 'Eccube\Entity\Payment',
                'property' => 'method',
                'empty_value' => false,
                'empty_data' => null,
            ))
            ->add('OrderDetails', 'collection', array(
                'type' => new OrderDetailType($this->app),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ))
            ->add('Shippings', 'collection', array(
                'type' => new ShippingType($this->app)
            ));
        $builder
            ->add($builder->create('Customer', 'hidden')
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->app['orm.em'],
                    '\Eccube\Entity\Customer'
                )));
        $builder->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\Order',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'order';
    }
}
