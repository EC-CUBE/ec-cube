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


namespace Eccube\Form\Type\Admin;

use Eccube\Common\Constant;
use Eccube\Form\DataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
        $app = $this->app;
        $config = $app['config'];
        $BaseInfo = $app['eccube.repository.base_info']->get();

        $builder
            ->add('name', 'name', array(
                'required' => false,
                'options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
            ))
            ->add('kana', 'kana', array(
                'required' => false,
                'options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
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
            ->add('zip', 'zip', array(
                'required' => false,
                'options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
            ))
            ->add('address', 'address', array(
                'required' => false,
                'pref_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
                'addr01_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'max' => $config['mtext_len'],
                        )),
                    ),
                ),
                'addr02_options' => array(
                    'required' => false,
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'max' => $config['mtext_len'],
                        )),
                    ),
                ),
            ))
            ->add('email', 'email', array(
                'required' => false,
                'label' => 'メールアドレス',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(array('strict' => true)),
                ),
            ))
            ->add('tel', 'tel', array(
                'required' => false,
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
                'label' => 'お問い合わせ',
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
                'scale' => 0,
                'grouping' => true,
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
                'scale' => 0,
                'grouping' => true,
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
                'scale' => 0,
                'grouping' => true,
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
                'empty_value' => '選択してください',
                'empty_data' => null,
                'query_builder' => function($er) {
                    return $er->createQueryBuilder('o')
                        ->orderBy('o.rank', 'ASC');
                },
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('Payment', 'entity', array(
                'required' => false,
                'class' => 'Eccube\Entity\Payment',
                'property' => 'method',
                'empty_value' => '選択してください',
                'empty_data' => null,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('OrderDetails', 'collection', array(
                'type' => 'order_detail',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ))
            ->add('Shippings', 'collection', array(
                'type' => 'shipping',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ));
        $builder
            ->add($builder->create('Customer', 'hidden')
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->app['orm.em'],
                    '\Eccube\Entity\Customer'
                )));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($BaseInfo) {

            $data = $event->getData();
            $orderDetails = &$data['OrderDetails'];

            // 数量0フィルター
            $quantityFilter = function ($v) {
                return !(isset($v['quantity']) && preg_match('/^0+$/', trim($v['quantity'])));
            };

            if ($BaseInfo->getOptionMultipleShipping() == Constant::ENABLED) {

                $shippings = &$data['Shippings'];

                // 数量を抽出
                $getQuantity = function ($v) {
                    return (isset($v['quantity']) && preg_match('/^\d+$/', trim($v['quantity']))) ?
                        trim($v['quantity']) :
                        0;
                };

                foreach ($shippings as &$shipping) {
                    if (!empty($shipping['ShipmentItems'])) {
                        $shipping['ShipmentItems'] = array_filter($shipping['ShipmentItems'], $quantityFilter);
                    }
                }

                if (!empty($orderDetails)) {

                    foreach ($orderDetails as &$orderDetail) {

                        $orderDetail['quantity'] = 0;

                        // 受注詳細と同じ商品規格のみ抽出
                        $productClassFilter = function ($v) use ($orderDetail) {
                            return $orderDetail['ProductClass'] === $v['ProductClass'];
                        };

                        foreach ($shippings as &$shipping) {

                            if (!empty($shipping['ShipmentItems'])) {

                                // 同じ商品規格の受注詳細の価格を適用
                                $applyPrice = function (&$v) use ($orderDetail) {
                                    $v['price'] = ($v['ProductClass'] === $orderDetail['ProductClass']) ?
                                        $orderDetail['price'] :
                                        $v['price'];
                                };
                                array_walk($shipping['ShipmentItems'], $applyPrice);

                                // 数量適用
                                $relatedShipmentItems = array_filter($shipping['ShipmentItems'], $productClassFilter);
                                $quantities = array_map($getQuantity, $relatedShipmentItems);
                                $orderDetail['quantity'] += array_sum($quantities);
                            }
                        }
                    }
                }
            }

            if (!empty($orderDetails)) {
                $data['OrderDetails'] = array_filter($orderDetails, $quantityFilter);
            }

            $event->setData($data);
        });
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $orderDetails = $form['OrderDetails']->getData();
            if (empty($orderDetails) || count($orderDetails) < 1) {
                // 画面下部にエラーメッセージを表示させる
                $form['charge']->addError(new FormError('商品が追加されていません。'));
            }
        });
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