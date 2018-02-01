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

use Doctrine\ORM\EntityRepository;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\KanaType;
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
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

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
            ->add('name', NameType::class, array(
                'required' => false,
                'options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
            ))
            ->add('kana', KanaType::class, array(
                'required' => false,
                'options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
            ))
            ->add('company_name', TextType::class, array(
                'label' => '会社名',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->eccubeConfig['stext_len'],
                    ))
                ),
            ))
            ->add('zip', ZipType::class, array(
                'required' => false,
                'options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                    'attr' => array('class' => 'p-postal-code')
                ),
            ))
            ->add('address', AddressType::class, array(
                'required' => false,
                'pref_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                    'attr' => array('class' => 'p-region-id')
                ),
                'addr01_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'max' => $this->eccubeConfig['mtext_len'],
                        )),
                    ),
                    'attr' => array('class' => 'p-locality')
                ),
                'addr02_options' => array(
                    'required' => false,
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'max' => $this->eccubeConfig['mtext_len'],
                        )),
                    ),
                    'attr' => array('class' => 'p-street-address')
                ),
            ))
            ->add('tel', TelType::class, array(
                'required' => false,
                'options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
            ))
            ->add('fax', TelType::class, array(
                'label' => 'FAX番号',
                'required' => false,
            ))
            ->add('Delivery', EntityType::class, array(
                'required' => false,
                'label' => '配送業者',
                'class' => 'Eccube\Entity\Delivery',
                'choice_label' => 'serviceNameForAdmin',
                'placeholder' => '選択してください',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('shipping_delivery_date', DateType::class, array(
                'label' => 'お届け日',
                'placeholder' => '',
                'format' => 'yyyy-MM-dd',
                'required' => false,
            ))
            ->add('tracking_number', TextType::class, array(
                'label' => '配送伝票番号',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->eccubeConfig['mtext_len'],
                    )),
                ),
            ))
            ->add('note', TextareaType::class, array(
                'label' => '配送用メモ欄',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->eccubeConfig['ltext_len'],
                    )),
                ),
            ))
            ->add('OrderItems', CollectionType::class, array(
                'entry_type' => OrderItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ))
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
                $form->add('DeliveryTime', EntityType::class, array(
                    'label' => 'お届け時間',
                    'class' => 'Eccube\Entity\DeliveryTime',
                    'choice_label' => 'delivery_time',
                    'placeholder' => '指定なし',
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
                ));
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
                $form->add('DeliveryTime', EntityType::class, array(
                    'label' => 'お届け時間',
                    'class' => 'Eccube\Entity\DeliveryTime',
                    'choice_label' => 'delivery_time',
                    'placeholder' => '指定なし',
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
                ));
            })
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $OrderItems = $form['OrderItems']->getData();

                if (empty($OrderItems) || count($OrderItems) < 1) {
                    // 画面下部にエラーメッセージを表示させる
                    $form['shipping_delivery_date']->addError(new FormError('商品が追加されていません。'));
                }
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
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\Shipping',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'shipping';
    }
}
