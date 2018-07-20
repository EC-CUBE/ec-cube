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

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Payment;
use Eccube\Form\DataTransformer;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\KanaType;
use Eccube\Form\Type\NameType;
use Eccube\Form\Type\PhoneNumberType;
use Eccube\Form\Type\PostalType;
use Eccube\Form\Type\PriceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class OrderType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * OrderType constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EntityManagerInterface $entityManager, EccubeConfig $eccubeConfig)
    {
        $this->entityManager = $entityManager;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', NameType::class, [
                'required' => false,
                'options' => [
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ],
            ])
            ->add('kana', KanaType::class, [
                'required' => false,
                'options' => [
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ],
            ])
            ->add('company_name', TextType::class, [
                'label' => 'order.label.company_name',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('postal_code', PostalType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'options' => [
                    'attr' => ['class' => 'p-postal-code'],
                ],
            ])
            ->add('address', AddressType::class, [
                'required' => false,
                'pref_options' => [
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                    'attr' => ['class' => 'p-region-id'],
                ],
                'addr01_options' => [
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Length([
                            'max' => $this->eccubeConfig['eccube_mtext_len'],
                        ]),
                    ],
                    'attr' => ['class' => 'p-locality p-street-address'],
                ],
                'addr02_options' => [
                    'required' => false,
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Length([
                            'max' => $this->eccubeConfig['eccube_mtext_len'],
                        ]),
                    ],
                    'attr' => ['class' => 'p-extended-address'],
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'label' => 'order.label.email',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(['strict' => true]),
                ],
            ])
            ->add('phone_number', PhoneNumberType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('company_name', TextType::class, [
                'label' => 'order.label.company_name',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'order.label.inquiry',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_ltext_len'],
                    ]),
                ],
            ])
            ->add('discount', PriceType::class, [
                'required' => false,
                'label' => 'order.label.discount',
            ])
            ->add('delivery_fee_total', PriceType::class, [
                'required' => false,
                'label' => 'order.label.shipping_charge',
            ])
            ->add('charge', PriceType::class, [
                'required' => false,
                'label' => 'order.label.commision',
            ])
            ->add('add_point', NumberType::class, [
                'required' => false,
                'label' => 'admin.common.label.add_point',
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid',
                    ]),
                ],
                'attr' => ['readonly' => true],
            ])
            ->add('use_point', NumberType::class, [
                'required' => false,
                'label' => 'admin.common.label.use_point',
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid',
                    ]),
                ],
            ])
            ->add('note', TextareaType::class, [
                'label' => 'order.label.owner_note',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_ltext_len'],
                    ]),
                ],
            ])
            ->add('OrderStatus', EntityType::class, [
                'class' => OrderStatus::class,
                'choice_label' => 'name',
                'placeholder' => 'order.placeholder.select',
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('o')
                        ->orderBy('o.sort_no', 'ASC');
                },
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('Payment', EntityType::class, [
                'required' => false,
                'class' => Payment::class,
                'choice_label' => 'method',
                'placeholder' => 'order.placeholder.select',
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('o')
                        ->orderBy('o.sort_no', 'ASC');
                },
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('OrderItems', CollectionType::class, [
                'entry_type' => OrderItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'data' => $options['SortedItems'],
            ])
            ->add('Shipping', ShippingType::class, [
                'mapped' => false,
            ])
            ->add('OrderItemsErrors', TextType::class, [
                'mapped' => false,
            ]);

        $builder
            ->add($builder->create('Customer', HiddenType::class)
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->entityManager,
                    '\Eccube\Entity\Customer'
                )));

        // 選択された支払い方法の名称をエンティティにコピーする
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var Order $Order */
            $Order = $event->getData();
            if ($Payment = $Order->getPayment()) {
                $Order->setPaymentMethod($Payment->getMethod());
            }

            // 会員受注の場合、会員の性別/職業/誕生日をエンティティにコピーする
            if ($Customer = $Order->getCustomer()) {
                $Order->setSex($Customer->getSex());
                $Order->setJob($Customer->getJob());
                $Order->setBirth($Customer->getBirth());
            }

            $form = $event->getForm();
            /** @var OrderItem[] $OrderItems */
            $OrderItems = $form['OrderItems']->getData();
            if (empty($OrderItems) || count($OrderItems) < 1) {
                // 画面下部にエラーメッセージを表示させる
                $form['OrderItemsErrors']->addError(new FormError(trans('admin.order.edit.product.error')));
            }

            // 明細とOrder, Shippingを紐付ける.
            // 新規の明細のみが対象, 更新時はスキップする.
            foreach ($OrderItems as $OrderItem) {
                // 更新時はスキップ
                if (!$OrderItem->getId()) {
                    continue;
                }

                $OrderItem->setOrder($Order);

                // 送料明細の紐付けを行う.
                // 複数配送の場合は, 常に最初のShippingと紐付ける.
                // Order::getShippingsは氏名でソートされている.
                if ($OrderItem->isDeliveryFee()) {
                    $OrderItem->setShipping($Order->getShippings()->first());
                }

                // 商品明細の紐付けを行う.
                // 複数配送時は, 明細の追加は行われないためスキップする.
                if ($OrderItem->isProduct() && !$Order->isMultiple()) {
                    $OrderItem->setShipping($Order->getShippings()->first());
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'SortedItems' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'order';
    }
}
