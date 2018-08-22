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

namespace Eccube\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Eccube\Common\EccubeConfig;
use Eccube\Form\Type\PriceType;
use Eccube\Form\Type\Master\OrderStatusType;
use Eccube\Form\Type\Master\PaymentType;

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
                'label' => 'searchorder.label.multi',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('status', OrderStatusType::class, [
                'label' => 'searchorder.label.status',
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'searchorder.label.name',
                'required' => false,
            ])
            ->add($builder
                ->create('kana', TextType::class, [
                    'label' => 'searchorder.label.kana',
                    'required' => false,
                    'constraints' => [
                        new Assert\Regex([
                            'pattern' => '/^[ァ-ヶｦ-ﾟー]+$/u',
                            'message' => 'form.type.admin.notkanastyle',
                        ]),
                    ],
                ])
                ->addEventSubscriber(new \Eccube\Form\EventListener\ConvertKanaListener('CV')
            ))
            ->add('company_name', TextType::class, [
                'label' => 'searchorder.label.company_name',
                'required' => false,
            ])
            ->add('email', TextType::class, [
                'label' => 'searchorder.label.email',
                'required' => false,
            ])
            ->add('order_no', TextType::class, [
                'label' => 'searchorder.label.order_no',
                'required' => false,
            ])
            ->add('phone_number', TextType::class, [
                'label' => 'common.label.phone_number',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^[\d-]+$/u",
                        'message' => 'form.type.admin.nottelstyle',
                    ]),
                ],
            ])
            ->add('tracking_number', TextType::class, [
                'label' => 'searchorder.label.tracking_number',
                'required' => false,
            ])
            ->add('shipping_mail_send', ChoiceType::class, [
                'label' => 'searchorder.label.shipping_mail_send',
                'required' => false,
                'placeholder' => false,
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    'searchorder.choice.shipping_mail_send.yes' => 1,
                    'searchorder.choice.shipping_mail_send.no' => 0,
                ],
            ])
            ->add('payment', PaymentType::class, [
                'label' => 'searchorder.label.payment_method',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('order_date_start', DateType::class, [
                'label' => 'searchorder.label.order_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('order_date_end', DateType::class, [
                'label' => 'searchorder.label.order_date_to',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('payment_date_start', DateType::class, [
                'label' => 'searchorder.label.payment_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('payment_date_end', DateType::class, [
                'label' => 'searchorder.label.payment_date_to',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('update_date_start', DateType::class, [
                'label' => 'searchorder.label.updated_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('update_date_end', DateType::class, [
                'label' => 'searchorder.label.updated_date_to',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('shipping_delivery_date_start', DateType::class, [
                'label' => 'searchorder.label.shipping_delivery_date_start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('shipping_delivery_date_end', DateType::class, [
                'label' => 'searchorder.label.shipping_delivery_date_end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('payment_total_start', PriceType::class, [
                'label' => 'searchorder.label.purchased_amount_min',
                'required' => false,
            ])
            ->add('payment_total_end', PriceType::class, [
                'label' => 'searchorder.label.purchased_amount_max',
                'required' => false,
            ])
            ->add('buy_product_name', TextType::class, [
                'label' => 'searchorder.label.purchased_products',
                'required' => false,
            ])
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
