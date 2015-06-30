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

use Eccube\Form\DataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductClassType extends AbstractType
{
    public $app;

    public function __construct(\Silex\Application $app)
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
            ->add('code', 'text', array(
                'label' => '商品コード',
                'required' => false,
            ))
            ->add('stock', 'number', array(
                'label' => '在庫数',
                'required' => false,
            ))
            ->add('stock_unlimited', 'checkbox', array(
                'label' => '無制限',
                'value' => '1',
                'required' => false,
            ))
            ->add('sale_limit', 'number', array(
                'label' => '販売制限数',
                'required' => false,
            ))
            ->add('price01', 'money', array(
                'label' => '通常価格',
                'currency' => 'JPY',
                'precision' => 0,
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => 10,
                    )),
                ),
            ))
            ->add('price02', 'money', array(
                'label' => '販売価格',
                'currency' => 'JPY',
                'precision' => 0,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => 10,
                    )),
                ),
            ))
            ->add('tax_rate', 'text', array(
                'label' => '消費税率',
                'required' => false,
                'constraints' => array(
                    new Assert\Range(array('min' => 0, 'max' => 100))),
            ))
            ->add('delivery_fee', 'money', array(
                'label' => '商品送料',
                'currency' => 'JPY',
                'precision' => 0,
                'required' => false,
            ))
            ->add('product_type', 'product_type', array(
                'label' => '商品種別',
				'multiple'=> false,
				'expanded' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('delivery_date', 'delivery_date', array(
                'label' => 'お届け可能日',
                'required' => false,
                'empty_value' => '指定なし',
            ))
            ->add('add', 'checkbox', array(
                'label' => false,
                'required' => false,
                'value' => 1,
            ))
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();

                if (empty($data['stock_unlimited']) && is_null($data['stock'])) {
                    $form['stock_unlimited']->addError(new FormError('在庫数を入力、もしくは在庫無制限を設定してください。'));
                }
            })
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber())
        ;

        $transformer = new DataTransformer\EntityToIdTransformer(
            $app['orm.em'],
            '\Eccube\Entity\ClassCategory'
        );
        $builder
            ->add($builder->create('ClassCategory1', 'hidden')
                          ->addModelTransformer($transformer)
            )
            ->add($builder->create('ClassCategory2', 'hidden')
                          ->addModelTransformer($transformer)
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\ProductClass',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_product_class';
    }
}
