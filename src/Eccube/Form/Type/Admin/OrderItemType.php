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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Entity\Master\OrderItemType as OrderItemTypeMaster;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Form\DataTransformer;
use Eccube\Form\Type\PriceType;
use Eccube\Repository\ProductClassRepository;
use Eccube\Repository\OrderItemRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @FormType
 */
class OrderItemType extends AbstractType
{
    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject(ProductClassRepository::class)
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * @Inject(OrderItemRepository::class)
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * @Inject("request_stack")
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var \Eccube\Application $app
     * @Inject(Application::class)
     */
    protected $app;

    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('new', HiddenType::class, array(
                'required' => false,
                'mapped' => false,
                'data' => 1
            ))
            ->add('id', HiddenType::class, array(
                'required' => false,
                'mapped' => false
            ))
            ->add('price', PriceType::class, array(
                'accept_minus' => true,
            ))
            ->add('quantity', TextType::class, array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $this->appConfig['int_len'],
                    )),
                ),
            ))
            ->add('tax_rate', TextType::class, array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $this->appConfig['int_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => "/^\d+(\.\d+)?$/u",
                        'message' => 'form.type.float.invalid'
                    )),
                )
            ))
            ->add('product_name', HiddenType::class)
            ->add('product_code', HiddenType::class)
            ->add('class_name1', HiddenType::class)
            ->add('class_name2', HiddenType::class)
            ->add('class_category_name1', HiddenType::class)
            ->add('class_category_name2', HiddenType::class)
            ->add('tax_rule', HiddenType::class)
            // ->add('order_id', HiddenType::class)
        ;

        $builder
            ->add($builder->create('order_item_type', HiddenType::class)
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->entityManager,
                    '\Eccube\Entity\Master\OrderItemType'
                )))
            ->add($builder->create('tax_type', HiddenType::class)
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->entityManager,
                    '\Eccube\Entity\Master\TaxType'
                )))
            ->add($builder->create('tax_display_type', HiddenType::class)
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->entityManager,
                    '\Eccube\Entity\Master\TaxDisplayType'
                )))
            ->add($builder->create('Product', HiddenType::class)
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->entityManager,
                    '\Eccube\Entity\Product'
                )))
            ->add($builder->create('ProductClass', HiddenType::class)
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->entityManager,
                    '\Eccube\Entity\ProductClass'
                )))
            ->add($builder->create('Order', HiddenType::class)
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->entityManager,
                    '\Eccube\Entity\Order'
                )))
            ->add($builder->create('Shipping', HiddenType::class)
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->entityManager,
                    '\Eccube\Entity\Shipping'
                )));

        $app = $this->app;
        // XXX price を priceIncTax にセットし直す
        // OrderItem::getTotalPrice でもやっているので、どこか一箇所にまとめたい
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($app) {
                /** @var \Eccube\Entity\OrderItem $OrderItem */
                $OrderItem = $event->getData();
                $TaxDisplayType = $OrderItem->getTaxDisplayType();
                if (!$TaxDisplayType) {
                    return;
                }
                switch ($TaxDisplayType->getId()) {
                    // 税込価格
                    case TaxDisplayType::INCLUDED:
                        $OrderItem->setPriceIncTax($OrderItem->getPrice());
                        break;
                    // 税別価格の場合は税額を加算する
                    case TaxDisplayType::EXCLUDED:
                        // TODO 課税規則を考慮する
                        $OrderItem->setPriceIncTax($OrderItem->getPrice() + $OrderItem->getPrice() * $OrderItem->getTaxRate() / 100);
                        break;
                }

                $event->setData($OrderItem);
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($app) {
            // モーダルからのPOST時に、金額等をセットする.
            if ('modal' === $this->requestStack->getCurrentRequest()->get('modal')) {
                $data = $event->getData();
                // 新規明細行の場合にセット.
                if (isset($data['new'])) {
                    // 受注済み明細の場合
                    if (array_key_exists('id', $data) && isset($data['id'])) {
                        /** @var \Eccube\Entity\OrderItem $OrderItem */
                        $OrderItem = $this->orderItemRepository
                            ->find($data['id']);
                        $data = array_merge($data, $OrderItem->toArray(['Order', 'Product', 'ProductClass', 'Shipping', 'TaxType', 'TaxDisplayType', 'OrderItemType']));

                        if (is_object($OrderItem->getOrder())) {
                            $data['Order'] = $OrderItem->getOrder()->getId();
                        }
                        if (is_object($OrderItem->getProduct())) {
                            $data['Product'] = $OrderItem->getProduct()->getId();
                        }
                        if (is_object($OrderItem->getProduct())) {
                            $data['ProductClass'] = $OrderItem->getProductClass()->getId();
                        }
                        if (is_object($OrderItem->getTaxType())) {
                            $data['tax_type'] = $OrderItem->getTaxType()->getId();
                        }
                        if (is_object($OrderItem->getTaxDisplayType())) {
                            $data['tax_display_type'] = $OrderItem->getTaxDisplayType()->getId();
                        }
                        if (is_object($OrderItem->getOrderItemType())) {
                            $data['order_item_type'] = $OrderItem->getOrderItemType()->getId();
                        }
                    } else {
                        // 新規受注登録時の場合
                        $data['product_code'] = null;
                        $data['class_name1'] = null;
                        $data['class_name2'] = null;
                        $data['class_category_name1'] = null;
                        $data['class_category_name2'] = null;
                        switch ($data['order_item_type']) {
                            case OrderItemTypeMaster::DELIVERY_FEE:
                                $data['product_name'] = '送料';
                                $data['price'] = 0;
                                $data['quantity'] = 1;
                                $data['tax_type'] = TaxType::TAXATION;
                                $data['tax_display_type'] = TaxDisplayType::INCLUDED;
                                break;
                            case OrderItemTypeMaster::CHARGE:
                                $data['product_name'] = '手数料';
                                $data['price'] = 0;
                                $data['quantity'] = 1;
                                $data['tax_type'] = TaxType::TAXATION;
                                $data['tax_display_type'] = TaxDisplayType::INCLUDED;
                                break;
                            case OrderItemTypeMaster::DISCOUNT:
                                $data['product_name'] = '値引き';
                                $data['price'] = -0;
                                $data['quantity'] = 1;
                                $data['tax_type'] = TaxType::NON_TAXABLE;
                                $data['tax_display_type'] = TaxDisplayType::INCLUDED;
                                break;
                            case OrderItemTypeMaster::PRODUCT:
                            default:
                                /** @var \Eccube\Entity\ProductClass $ProductClass */
                                $ProductClass = $this->productClassRepository
                                    ->find($data['ProductClass']);
                                /** @var \Eccube\Entity\Product $Product */
                                $Product = $ProductClass->getProduct();
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
                                $data['price'] = $ProductClass->getPrice02();
                                $data['quantity'] = empty($data['quantity']) ? 1 : $data['quantity'];
                                $data['tax_type'] = TaxType::TAXATION;
                                $data['tax_display_type'] = TaxDisplayType::EXCLUDED;
                        }
                    }
                    $event->setData($data);
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\OrderItem',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'order_item';
    }
}
