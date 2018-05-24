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

use Doctrine\ORM\EntityRepository;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Delivery;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\KanaType;
use Eccube\Form\Type\Master\ShippingStatusType;
use Eccube\Form\Type\NameType;
use Eccube\Form\Type\TelType;
use Eccube\Form\Type\ZipType;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\DeliveryTimeRepository;
use Eccube\Util\StringUtil;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ShippingType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * @var DeliveryTimeRepository
     */
    protected $deliveryTimeRepository;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * ShippingType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param DeliveryRepository $deliveryRepository
     * @param DeliveryTimeRepository $deliveryTimeRepository
     * @param BaseInfo $BaseInfo
     */
    public function __construct(
        EccubeConfig $eccubeConfig,
        DeliveryRepository $deliveryRepository,
        DeliveryTimeRepository $deliveryTimeRepository,
        BaseInfo $BaseInfo
    ) {
        $this->eccubeConfig = $eccubeConfig;
        $this->deliveryRepository = $deliveryRepository;
        $this->deliveryTimeRepository = $deliveryTimeRepository;
        $this->BaseInfo = $BaseInfo;
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
                'label' => 'shipping.label.company_name',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('zip', ZipType::class, [
                'required' => false,
                'options' => [
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
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
            ->add('tel', TelType::class, [
                'required' => false,
                'options' => [
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ],
            ])
            ->add('fax', TelType::class, [
                'label' => 'shipping.label.fax',
                'required' => false,
            ])
            ->add('Delivery', EntityType::class, [
                'required' => false,
                'label' => 'shipping.label.shipping_company',
                'class' => 'Eccube\Entity\Delivery',
                'choice_label' => function (Delivery $Delivery) {
                    return $Delivery->isVisible()
                        ? $Delivery->getServiceName()
                        : $Delivery->getServiceName().trans('delivery.text.hidden');
                },
                'placeholder' => 'shipping.placeholder.please_select',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('shipping_delivery_date', DateType::class, [
                'label' => 'shipping.label.delivery_date',
                'placeholder' => '',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => false,
            ])
            ->add('tracking_number', TextType::class, [
                'label' => 'shipping.label.tracking_num',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_mtext_len'],
                    ]),
                ],
            ])
            ->add('note', TextareaType::class, [
                'label' => 'shipping.label.memo',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_ltext_len'],
                    ]),
                ],
            ])
            ->add('OrderItems', CollectionType::class, [
                'entry_type' => OrderItemForShippingRegistrationType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ])
            // 明細業のエラー表示用
            ->add('OrderItemsError', TextType::class, [
                'mapped' => false,
            ])
            ->add('ShippingStatus', ShippingStatusType::class)
            ->add('notify_email', CheckboxType::class, [
                'label' => 'admin.shipping.index.813',
                'mapped' => false,
                'required' => false,
                'data' => true,
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                /** @var \Eccube\Entity\Shipping $data */
                $data = $event->getData();
                /** @var \Symfony\Component\Form\Form $form */
                $form = $event->getForm();

                $Delivery = $data->getDelivery();
                $timeId = $data->getTimeId();
                $DeliveryTime = null;
                if ($timeId) {
                    $DeliveryTime = $this->deliveryTimeRepository->find($timeId);
                }

                // お届け時間を配送業者で絞り込み
                $form->add('DeliveryTime', EntityType::class, [
                    'label' => 'shipping.label.delivery_hour',
                    'class' => 'Eccube\Entity\DeliveryTime',
                    'choice_label' => 'delivery_time',
                    'placeholder' => 'shipping.placeholder.not_specified',
                    'required' => false,
                    'data' => $DeliveryTime,
                    'query_builder' => function (EntityRepository $er) use ($Delivery) {
                        $qb = $er->createQueryBuilder('dt');
                        if ($Delivery) {
                            $qb
                                ->where('dt.Delivery = :Delivery')
                                ->setParameter('Delivery', $Delivery);
                        }

                        return $qb;
                    },
                    'mapped' => false,
                ]);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

                if (!$data) {
                    return;
                }

                $Delivery = null;
                if (StringUtil::isNotBlank($data['Delivery'])) {
                    $Delivery = $this->deliveryRepository->find($data['Delivery']);
                }

                // お届け時間を配送業者で絞り込み
                $form->remove('DeliveryTime');
                $form->add('DeliveryTime', EntityType::class, [
                    'label' => 'shipping.label.delivery_hour',
                    'class' => 'Eccube\Entity\DeliveryTime',
                    'choice_label' => 'delivery_time',
                    'placeholder' => 'shipping.placeholder.not_specified',
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) use ($Delivery) {
                        $qb = $er->createQueryBuilder('dt');
                        if ($Delivery) {
                            $qb
                                ->where('dt.Delivery = :Delivery')
                                ->setParameter('Delivery', $Delivery);
                        }

                        return $qb;
                    },
                    'mapped' => false,
                ]);
            })
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $Shipping = $event->getData();
                $Delivery = $Shipping->getDelivery();
                $Shipping->setShippingDeliveryName($Delivery ? $Delivery->getName() : null);
                $DeliveryTime = $form['DeliveryTime']->getData();
                if ($DeliveryTime) {
                    $Shipping->setShippingDeliveryTime($DeliveryTime->getDeliveryTime());
                    $Shipping->setTimeId($DeliveryTime->getId());
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Eccube\Entity\Shipping',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'shipping';
    }
}
