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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\Payment;
use Eccube\Form\DataTransformer;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\KanaType;
use Eccube\Form\Type\NameType;
use Eccube\Form\Type\PhoneNumberType;
use Eccube\Form\Type\PostalType;
use Eccube\Form\Type\PriceType;
use Eccube\Form\Validator\Email;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Service\OrderStateMachine;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
     * @var OrderStateMachine
     */
    protected $orderStateMachine;

    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * OrderType constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EccubeConfig $eccubeConfig
     * @param OrderStateMachine $orderStateMachine
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EccubeConfig $eccubeConfig,
        OrderStateMachine $orderStateMachine,
        OrderStatusRepository $orderStatusRepository
    ) {
        $this->entityManager = $entityManager;
        $this->eccubeConfig = $eccubeConfig;
        $this->orderStateMachine = $orderStateMachine;
        $this->orderStatusRepository = $orderStatusRepository;
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
                'constraints' => [
                    new Assert\NotBlank(),
                    new Email(['strict' => $this->eccubeConfig['eccube_rfc_email_check']]),
                ],
            ])
            ->add('phone_number', PhoneNumberType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('company_name', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_ltext_len'],
                    ]),
                ],
            ])
            ->add('discount', PriceType::class, [
                'required' => false,
            ])
            ->add('delivery_fee_total', PriceType::class, [
                'required' => false,
            ])
            ->add('charge', PriceType::class, [
                'required' => false,
            ])
            ->add('use_point', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid',
                    ]),
                ],
            ])
            ->add('note', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_ltext_len'],
                    ]),
                ],
            ])
            ->add('Payment', EntityType::class, [
                'required' => false,
                'class' => Payment::class,
                'choice_label' => function (Payment $Payment) {
                    return $Payment->isVisible()
                        ? $Payment->getMethod()
                        : $Payment->getMethod().trans('admin.common.hidden_label');
                },
                'placeholder' => false,
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.visible', 'DESC')  // 非表示は下に配置
                        ->addOrderBy('p.sort_no', 'ASC');
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

        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'sortOrderItems']);
        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'addOrderStatusForm']);
        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'addShippingForm']);
        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'copyFields']);
        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'validateOrderStatus']);
        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'validateOrderItems']);
        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'associateOrderAndShipping']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'order';
    }

    /**
     * 受注明細をソートする.
     *
     * @param FormEvent $event
     */
    public function sortOrderItems(FormEvent $event)
    {
        /** @var Order $Order */
        $Order = $event->getData();
        if (null === $Order) {
            return;
        }
        $OrderItems = $Order->getItems();

        $form = $event->getForm();
        $form['OrderItems']->setData($OrderItems);
    }

    /**
     * 受注ステータスのフォームを追加する
     * 新規登録の際は, ユーザ編集不可のため追加しない.
     *
     * ステータスのプルダウンは, ステートマシンで遷移可能なステータスのみ表示する.
     *
     * @param FormEvent $event
     */
    public function addOrderStatusForm(FormEvent $event)
    {
        /** @var Order $Order */
        $Order = $event->getData();
        if (null === $Order || ($Order && !$Order->getId())) {
            return;
        }

        /** @var ArrayCollection|OrderStatus[] $OrderStatuses */
        $OrderStatuses = $this->orderStatusRepository->findBy([], ['sort_no' => 'ASC']);
        $OrderStatuses = new ArrayCollection($OrderStatuses);

        foreach ($OrderStatuses as $Status) {
            // 同一ステータスはスキップ
            if ($Order->getOrderStatus()->getId() == $Status->getId()) {
                continue;
            }
            // 遷移できないステータスはリストから除外する.
            if (!$this->orderStateMachine->can($Order, $Status)) {
                $OrderStatuses->removeElement($Status);
            }
        }

        $form = $event->getForm();
        $form->add('OrderStatus', EntityType::class, [
            'class' => OrderStatus::class,
            'choices' => $OrderStatuses,
            'choice_label' => 'name',
            'constraints' => [
                new Assert\NotBlank(),
            ],
            // 変更前後のステータスチェックが必要なのでmapped => false で定義する.
            'mapped' => false,
            'data' => $Order->getOrderStatus(),
        ]);
    }

    /**
     * 単一配送時に, Shippingのフォームを追加する.
     * 複数配送時はShippingの編集は行わない.
     *
     * @param FormEvent $event
     */
    public function addShippingForm(FormEvent $event)
    {
        /** @var Order $Order */
        $Order = $event->getData();

        // 複数配送時はShippingの編集は行わない
        if ($Order && $Order->isMultiple()) {
            return;
        }

        $data = $Order ? $Order->getShippings()->first() : null;
        $form = $event->getForm();
        $form->add('Shipping', ShippingType::class, [
            'mapped' => false,
            'data' => $data,
        ]);
    }

    /**
     * フォームからPOSTされない情報をコピーする.
     *
     * - 支払方法の名称
     * - 会員の性別/職業/誕生日
     * - 受注ステータス(新規登録時)
     *
     * @param FormEvent $event
     */
    public function copyFields(FormEvent $event)
    {
        /** @var Order $Order */
        $Order = $event->getData();

        // 支払方法の名称をコピーする.
        if ($Payment = $Order->getPayment()) {
            $Order->setPaymentMethod($Payment->getMethod());
        }

        // 会員受注の場合、会員の性別/職業/誕生日をエンティティにコピーする
        if ($Customer = $Order->getCustomer()) {
            $Order->setSex($Customer->getSex());
            $Order->setJob($Customer->getJob());
            $Order->setBirth($Customer->getBirth());
        }

        // 新規登録時は, 新規受付ステータスで登録する.
        if (null === $Order->getOrderStatus()) {
            $Order->setOrderStatus($this->orderStatusRepository->find(OrderStatus::NEW));
        } else {
            // 編集時は, mapped => falseで定義しているため, フォームから変更後データを取得する.
            $form = $event->getForm();
            $Order->setOrderStatus($form['OrderStatus']->getData());
        }

        // 新規登録時は受注日を登録する.
        if (null === $Order->getOrderDate()) {
            $Order->setOrderDate(new \DateTime());
        }
    }

    /**
     * 受注ステータスのバリデーションを行う.
     *
     * @param FormEvent $event
     */
    public function validateOrderStatus(FormEvent $event)
    {
        /** @var Order $Order */
        $Order = $event->getData();
        if (!$Order->getId()) {
            return;
        }

        // mapped => falseで定義しているため, Orderのステータスは変更されない
        $oldStatus = $Order->getOrderStatus();
        // 変更後のステータスはFormから直接取得する.
        $form = $event->getForm();
        $newStatus = $form['OrderStatus']->getData();

        // ステータスに変更があった場合のみチェックする.
        if (!is_null($oldStatus) && !is_null($newStatus)) {
            if ($oldStatus->getId() != $newStatus->getId()) {
                if (!$this->orderStateMachine->can($Order, $newStatus)) {
                    $form['OrderStatus']->addError(
                        new FormError(sprintf('%sから%sには変更できません', $oldStatus->getName(), $newStatus->getName())));
                }
            }
        } else {
            $form['OrderStatus']->addError(new FormError('ステータス変更できません。'));
        }
    }

    /**
     * 受注明細のバリデーションを行う.
     * 商品明細が1件も登録されていない場合はエラーとする.
     *
     * @param FormEvent $event
     */
    public function validateOrderItems(FormEvent $event)
    {
        /** @var Order $Order */
        $Order = $event->getData();
        $OrderItems = $Order->getOrderItems();

        $count = 0;
        foreach ($OrderItems as $OrderItem) {
            if ($OrderItem->isProduct()) {
                $count++;
            }
        }
        // 商品明細が1件もない場合はエラーとする.
        if ($count < 1) {
            // 画面下部にエラーメッセージを表示させる
            $form = $event->getForm();
            $form['OrderItemsErrors']->addError(new FormError(trans('admin.order.product_item_not_found')));
        }
    }

    /**
     * 受注明細と, Order/Shippingの紐付けを行う.
     *
     * @param FormEvent $event
     */
    public function associateOrderAndShipping(FormEvent $event)
    {
        /** @var Order $Order */
        $Order = $event->getData();
        $OrderItems = $Order->getOrderItems();

        // 明細とOrder, Shippingを紐付ける.
        // 新規の明細のみが対象, 更新時はスキップする.
        foreach ($OrderItems as $OrderItem) {
            // 更新時はスキップ
            if ($OrderItem->getId()) {
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
    }
}
