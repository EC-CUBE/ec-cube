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

use Eccube\Common\EccubeConfig;
use Eccube\Form\Type\Master\OrderStatusType;
use Eccube\Form\Type\Master\ShippingStatusType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SearchShippingType extends AbstractType
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
            // 配送番号・お届け先名・お届け先（フリガナ）・お届け先会社名
            ->add('multi', TextType::class, [
                'label' => 'searchshipping.label.multi',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('order_status', OrderStatusType::class, [
                'label' => 'searchshipping.label.order_status',
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('shipping_status', ShippingStatusType::class, [
                'label' => 'searchshipping.label.shipping_status',
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('name', TextType::class, [
                'required' => false,
                'label' => 'searchshipping.label.name',
            ])
            ->add('order_name', TextType::class, [
                'required' => false,
                'label' => 'searchshipping.label.order_name',
            ])
            ->add('order_id', TextType::class, [
                'label' => 'searchshipping.label.order_id',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 10]),
                ],
            ])
            ->add('shipping_delivery_date_start', DateType::class, [
                'label' => 'searchshipping.label.delivery_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('shipping_delivery_date_end', DateType::class, [
                'label' => 'searchshipping.label.delivery_date_to',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('update_date_start', DateType::class, [
                'label' => 'searchshipping.label.updated_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('update_date_end', DateType::class, [
                'label' => 'searchshipping.label.updated_date_to',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('payment_total_start', IntegerType::class, [
                'label' => 'searchshipping.label.purchased_amount_min',
                'required' => false,
            ])
            ->add('payment_total_end', IntegerType::class, [
                'label' => 'searchshipping.label.purchased_amount_max',
                'required' => false,
            ])
            ->add(
                $builder
                    ->create('kana', TextType::class, [
                        'label' => 'searchshipping.label.kana',
                        'required' => false,
                        'constraints' => [
                            new Assert\Regex([
                                'pattern' => '/^[ァ-ヶｦ-ﾟー]+$/u',
                                'message' => 'form.type.admin.notkanastyle',
                            ]),
                        ],
                    ])
                    ->addEventSubscriber(new \Eccube\Form\EventListener\ConvertKanaListener('CV'))
            )
            ->add(
                $builder
                    ->create('order_kana', TextType::class, [
                        'label' => 'searchshipping.label.order_kana',
                        'required' => false,
                        'constraints' => [
                            new Assert\Regex([
                                'pattern' => '/^[ァ-ヶｦ-ﾟー]+$/u',
                                'message' => 'form.type.admin.notkanastyle',
                            ]),
                        ],
                    ])
                    ->addEventSubscriber(new \Eccube\Form\EventListener\ConvertKanaListener('CV'))
            )
            ->add('tel', TextType::class, [
                'label' => 'searchshipping.label.tel',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^[\d-]+$/u",
                        'message' => 'form.type.admin.nottelstyle',
                    ]),
                ],
            ])
            ->add('shipping_date_start', DateType::class, [
                'label' => 'searchshipping.label.shipping_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('shipping_date_end', DateType::class, [
                'label' => 'searchshipping.label.shipping_date_to',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('buy_product_name', TextType::class, [
                'label' => 'searchshipping.label.purchased_products',
                'required' => false,
            ])

            // FXIME 未使用
            ->add('email', TextType::class, [
                'required' => false,
            ])
            ->add('order_code', TextType::class, [
                'label' => '注文番号',
                'required' => false,
            ])
            // FIXME 未使用
            ->add('order_date_start', DateType::class, [
                'label' => 'searchshipping.label.order_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            // FIXME 未使用
            ->add('order_date_end', DateType::class, [
                'label' => 'searchshipping.label.order_date_to',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_search_shipping';
    }
}
