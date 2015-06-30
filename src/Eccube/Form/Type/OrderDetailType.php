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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class OrderDetailType extends AbstractType
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
            ->add('new', 'hidden', array(
                'required' => false,
                'mapped' => false,
                'data' => 1
            ))
            ->add('price', 'money', array(
                'currency' => 'JPY',
                'precision' => 0,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $config['int_len'],
                    )),
                ),
            ))
            ->add('quantity', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $config['int_len'],
                    )),
                ),
            ))
            ->add('tax_rate', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $config['int_len'],
                    )),
                )
            ));

        $builder
            ->add($builder->create('Product', 'hidden')
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->app['orm.em'],
                    '\Eccube\Entity\Product'
                )))
            ->add($builder->create('ProductClass', 'hidden')
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->app['orm.em'],
                    '\Eccube\Entity\ProductClass'
                )));

        $app = $this->app;
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($app){
            // モーダルからのPOST時に、金額等をセットする.
            if ('motal' === $app['request']->get('mode')) {
                $data = $event->getData();
                // 新規明細行の場合にセット.
                if (isset($data['new'])) {
                    $ProductClass = $app['eccube.repository.product_class']
                        ->find($data['ProductClass']);
                    $Product = $ProductClass->getProduct();
                    $TaxRule = $app['eccube.repository.tax_rule']->getByRule($Product, $ProductClass);

                    $data['price'] = $ProductClass->getPrice02();
                    $data['quantity'] = empty($data['quantity']) ? 1 : $data['quantity'];
                    $data['tax_rate'] = $TaxRule->getTaxRate();
                    $event->setData($data);
                }
            }
        });

        $builder->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\OrderDetail',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'order_detail';
    }
}
