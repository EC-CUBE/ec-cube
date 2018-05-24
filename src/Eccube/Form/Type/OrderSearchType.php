<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Form\Type;

use Eccube\Form\Type\Master\OrderStatusType;
use Eccube\Form\Type\Master\PaymentType;
use Eccube\Form\Type\Master\SexType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

// deprecated 3.1で削除予定
class OrderSearchType extends AbstractType
{
    public $app;

    // public function __construct(\Silex\Application $app)
    // {
    //     $this->app = $app;
    // }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder
            ->add('order_id_start', IntegerType::class, [
                'label' => 'ordersearch.label.order_id',
                'required' => false,
                'constraints' => [
                    new Assert\Type([
                        'type' => 'integer',
                    ]),
                    new Assert\Length(['max' => 10]),
                ],
            ])
            ->add('order_id_end', IntegerType::class, [
                'label' => 'ordersearch.label.order_id',
                'required' => false,
                'constraints' => [
                    new Assert\Type([
                        'type' => 'integer',
                    ]),
                    new Assert\Length(['max' => 10]),
                ],
            ])
            ->add('status', OrderStatusType::class, [
                'label' => 'ordersearch.label.status',
            ])
            ->add('name', TextType::class, [
                'required' => false,
            ])
            ->add('kana', TextType::class, [
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'required' => false,
            ])
            ->add('tel', TelType::class, [
                'required' => false,
            ])
            ->add('birth_start', BirthdayType::class, [
                'label' => 'ordersearch.label.dob',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('birth_end', BirthdayType::class, [
                'label' => 'ordersearch.label.dob',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('sex', SexType::class, [
                'label' => 'ordersearch.label.gender',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('payment', PaymentType::class, [
                'label' => 'ordersearch.label.payment',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('order_date_start', DateType::class, [
                'label' => 'ordersearch.label.order_date',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('order_date_end', DateType::class, [
                'label' => 'ordersearch.label.order_date',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('update_date_start', DateType::class, [
                'label' => 'ordersearch.label.last_update',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('update_date_end', DateType::class, [
                'label' => 'ordersearch.label.last_update',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('payment_total_start', IntegerType::class, [
                'label' => 'ordersearch.label.purchased_amount',
                'required' => false,
            ])
            ->add('payment_total_end', IntegerType::class, [
                'label' => 'ordersearch.label.purchased_amount',
                'required' => false,
            ])
            ->add('buy_product_name', TextType::class, [
                'label' => 'ordersearch.label.purchased_products',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'order_search';
    }
}
