<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Form\Type\Admin;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Shipping;
use Eccube\Form\Type\Master\OrderStatusType;
use Eccube\Form\Type\Master\PaymentType;
use Eccube\Form\Type\PriceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SearchOrderType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // 受注ID・注文者名・注文者（フリガナ）・注文者会社名
            ->add('multi', TextType::class, [
                'label' => 'admin.order.multi_search_label',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('status', OrderStatusType::class, [
                'label' => 'admin.order.order_status',
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'admin.order.orderer_name',
                'required' => false,
            ])
            ->add($builder
                ->create('kana', TextType::class, [
                    'label' => 'admin.order.orderer_kana',
                    'required' => false,
                    'constraints' => [
                        new Assert\Regex([
                            'pattern' => '/^[ァ-ヶｦ-ﾟー]+$/u',
                            'message' => 'form_error.kana_only',
                        ]),
                    ],
                ])
                ->addEventSubscriber(new \Eccube\Form\EventListener\ConvertKanaListener('CV')
            ))
            ->add('company_name', TextType::class, [
                'label' => 'admin.order.orderer_company_name',
                'required' => false,
            ])
            ->add('email', TextType::class, [
                'label' => 'admin.common.mail_address',
                'required' => false,
            ])
            ->add('order_no', TextType::class, [
                'label' => 'admin.order.order_no',
                'required' => false,
            ])
            ->add('phone_number', TextType::class, [
                'label' => 'admin.common.phone_number',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^[\d-]+$/u",
                        'message' => 'form_error.graph_and_hyphen_only',
                    ]),
                ],
            ])
            ->add('tracking_number', TextType::class, [
                'label' => 'admin.order.tracking_number',
                'required' => false,
            ])
            ->add('shipping_mail', ChoiceType::class, [
                'label' => 'admin.order.shipping_mail',
                'placeholder' => false,
                'choices' => [
                    'admin.order.shipping_mail__unsent' => Shipping::SHIPPING_MAIL_UNSENT,
                    'admin.order.shipping_mail__sent' => Shipping::SHIPPING_MAIL_SENT,
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('payment', PaymentType::class, [
                'label' => 'admin.common.payment_method',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('order_date_start', DateType::class, [
                'label' => 'admin.order.order_date__start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_order_date_start',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('order_datetime_start', DateTimeType::class, [
                'label' => 'admin.order.order_date__start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_order_datetime_start',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('order_date_end', DateType::class, [
                'label' => 'admin.order.order_date__end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_order_date_end',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('order_datetime_end', DateTimeType::class, [
                'label' => 'admin.order.order_date__end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_order_datetime_end',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('payment_date_start', DateType::class, [
                'label' => 'admin.order.payment_date__start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_payment_date_start',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('payment_datetime_start', DateTimeType::class, [
                'label' => 'admin.order.payment_date__start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_payment_datetime_start',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('payment_date_end', DateType::class, [
                'label' => 'admin.order.payment_date__end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_payment_date_end',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('payment_datetime_end', DateTimeType::class, [
                'label' => 'admin.order.payment_date__end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_payment_datetime_end',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('update_date_start', DateType::class, [
                'label' => 'admin.common.update_date__start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_update_date_start',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('update_datetime_start', DateTimeType::class, [
                'label' => 'admin.common.update_date__start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_update_datetime_start',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('update_date_end', DateType::class, [
                'label' => 'admin.common.update_date__end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_update_date_end',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('update_datetime_end', DateTimeType::class, [
                'label' => 'admin.common.update_date__end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_update_datetime_end',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('shipping_delivery_date_start', DateType::class, [
                'label' => 'admin.order.delivery_date__start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_shipping_delivery_date_start',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('shipping_delivery_datetime_start', DateTimeType::class, [
                'label' => 'admin.order.delivery_date__start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_shipping_delivery_datetime_start',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('shipping_delivery_date_end', DateType::class, [
                'label' => 'admin.order.delivery_date__end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_shipping_delivery_date_end',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('shipping_delivery_datetime_end', DateTimeType::class, [
                'label' => 'admin.order.delivery_date__end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_shipping_delivery_datetime_end',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('payment_total_start', PriceType::class, [
                'label' => 'admin.order.purchase_price__start',
                'required' => false,
            ])
            ->add('payment_total_end', PriceType::class, [
                'label' => 'admin.order.purchase_price__end',
                'required' => false,
            ])
            ->add('buy_product_name', TextType::class, [
                'label' => 'admin.order.purchase_product',
                'required' => false,
            ])
            // ソート用
            ->add('sortkey', HiddenType::class, [
                'label' => 'admin.list.sort.key',
                'required' => false,
            ])
            ->add('sorttype', HiddenType::class, [
                'label' => 'admin.list.sort.type',
                'required' => false,
            ]);
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_search_order';
    }
}
