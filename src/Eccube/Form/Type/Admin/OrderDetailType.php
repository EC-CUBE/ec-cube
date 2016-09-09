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
                    new Assert\Regex(array(
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid'
                    )),
                ),
            ))
            ->add('tax_rate', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $config['int_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => "/^\d+(\.\d+)?$/u",
                        'message' => 'form.type.float.invalid'
                    )),
                )
            ))
            ->add('product_name', 'hidden')
            ->add('product_code', 'hidden')
            ->add('class_name1', 'hidden')
            ->add('class_name2', 'hidden')
            ->add('class_category_name1', 'hidden')
            ->add('class_category_name2', 'hidden')
            ->add('tax_rule', 'hidden')
        ;

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
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($app) {
            // モーダルからのPOST時に、金額等をセットする.
            if ('modal' === $app['request']->get('modal')) {
                $data = $event->getData();
                // 新規明細行の場合にセット.
                if (isset($data['new'])) {
                    /** @var \Eccube\Entity\ProductClass $ProductClass */
                    $ProductClass = $app['eccube.repository.product_class']
                        ->find($data['ProductClass']);
                    /** @var \Eccube\Entity\Product $Product */
                    $Product = $ProductClass->getProduct();
                    /** @var \Eccube\Entity\TaxRule $TaxRule */
                    $TaxRule = $app['eccube.repository.tax_rule']->getByRule($Product, $ProductClass);

                    $data['product_name'] = $Product->getName();
                    $data['product_code'] = $ProductClass->getCode();
                    $data['class_name1'] = $ProductClass->hasClassCategory1() ?
                        $ProductClass->getClassCategory1()->getClassName() :
                        null;
                    $data['class_name2'] = $ProductClass->hasClassCategory2() ?
                        $ProductClass->getClassCategory2()->getClassName() :
                        null;
                    $data['class_category_name1'] = $ProductClass->hasClassCategory1() ?
                        $ProductClass->getClassCategory1()->getName() :
                        null;
                    $data['class_category_name2'] = $ProductClass->hasClassCategory2() ?
                        $ProductClass->getClassCategory2()->getName() :
                        null;
                    $data['tax_rule'] = $TaxRule->getCalcRule()->getId();
                    $data['price'] = $ProductClass->getPrice02();
                    $data['quantity'] = empty($data['quantity']) ? 1 : $data['quantity'];
                    $data['tax_rate'] = $TaxRule->getTaxRate();
                    $event->setData($data);
                }
            }
        });

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
