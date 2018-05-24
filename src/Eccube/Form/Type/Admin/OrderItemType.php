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
use Eccube\Entity\Master\OrderItemType as OrderItemTypeMaster;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Form\DataTransformer;
use Eccube\Form\Type\PriceType;
use Eccube\Repository\OrderItemRepository;
use Eccube\Repository\ProductClassRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class OrderItemType extends AbstractType
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
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * OrderItemType constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EccubeConfig $eccubeConfig
     * @param ProductClassRepository $productClassRepository
     * @param OrderItemRepository $orderItemRepository
     * @param RequestStack $requestStack
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EccubeConfig $eccubeConfig,
        ProductClassRepository $productClassRepository,
        OrderItemRepository $orderItemRepository,
        RequestStack $requestStack
    ) {
        $this->entityManager = $entityManager;
        $this->eccubeConfig = $eccubeConfig;
        $this->productClassRepository = $productClassRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('new', HiddenType::class, [
                'required' => false,
                'mapped' => false,
                'data' => 1,
            ])
            ->add('id', HiddenType::class, [
                'required' => false,
                'mapped' => false,
            ])
            ->add('price', PriceType::class, [
                'accept_minus' => true,
            ])
            ->add('quantity', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_int_len'],
                    ]),
                ],
            ])
            ->add('tax_rate', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_int_len'],
                    ]),
                    new Assert\Regex([
                        'pattern' => "/^\d+(\.\d+)?$/u",
                        'message' => 'form.type.float.invalid',
                    ]),
                ],
            ])
            ->add('product_name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_mtext_len'],
                    ]),
                ],
            ])
            ->add('product_code', HiddenType::class)
            ->add('class_name1', HiddenType::class)
            ->add('class_name2', HiddenType::class)
            ->add('class_category_name1', HiddenType::class)
            ->add('class_category_name2', HiddenType::class)
            ->add('tax_rule', HiddenType::class)// ->add('order_id', HiddenType::class)
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

        // XXX price を priceIncTax にセットし直す
        // OrderItem::getTotalPrice でもやっているので、どこか一箇所にまとめたい
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
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
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            // モーダルからのPOST時に、金額等をセットする.
            if ('modal' === $this->requestStack->getCurrentRequest()->get('modal')) {
                $data = $event->getData();
                // 受注済み明細の場合
                if (array_key_exists('id', $data) && isset($data['id'])) {
                    /** @var \Eccube\Entity\OrderItem $OrderItem */
                    $OrderItem = $this->orderItemRepository
                        ->find($data['id']);
                    $data = array_merge($data, $OrderItem->toArray([
                        'Order',
                        'Product',
                        'ProductClass',
                        'Shipping',
                        'TaxType',
                        'TaxDisplayType',
                        'OrderItemType',
                    ]));

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
                            // $data['product_name'] = trans('orderitem.text.data.shipping_charge');
                            // $data['price'] = 0;
                            // $data['quantity'] = 1;
                            $data['tax_type'] = TaxType::TAXATION;
                            $data['tax_display_type'] = TaxDisplayType::INCLUDED;
                            break;
                        case OrderItemTypeMaster::CHARGE:
                            // $data['product_name'] = trans('orderitem.text.data.commision');
                            // $data['price'] = 0;
                            // $data['quantity'] = 1;
                            $data['tax_type'] = TaxType::TAXATION;
                            $data['tax_display_type'] = TaxDisplayType::INCLUDED;
                            break;
                        case OrderItemTypeMaster::DISCOUNT:
                            // $data['product_name'] = trans('orderitem.text.data.discount');
                            // $data['price'] = -0;
                            // $data['quantity'] = 1;
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
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Eccube\Entity\OrderItem',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'order_item';
    }
}
