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
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Master\OrderItemType as OrderItemTypeMaster;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\OrderItem;
use Eccube\Entity\ProductClass;
use Eccube\Form\DataTransformer;
use Eccube\Form\Type\PriceType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Repository\OrderItemRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Repository\TaxRuleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
     * @var BaseInfoRepository
     */
    protected $BaseInfo;

    /**
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * @var OrderItemTypeRepository
     */
    protected $orderItemTypeRepository;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

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
        BaseInfo $BaseInfo,
        ProductClassRepository $productClassRepository,
        OrderItemRepository $orderItemRepository,
        OrderItemTypeRepository $orderItemTypeRepository,
        TaxRuleRepository $taxRuleRepository
    ) {
        $this->entityManager = $entityManager;
        $this->eccubeConfig = $eccubeConfig;
        $this->BaseInfo = $BaseInfo;
        $this->productClassRepository = $productClassRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderItemTypeRepository = $orderItemTypeRepository;
        $this->taxRuleRepository = $taxRuleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product_name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_mtext_len'],
                    ]),
                ],
            ])
            ->add('price', PriceType::class, [
            ])
            ->add('quantity', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_int_len'],
                    ]),
                ],
            ]);

        $builder
            ->add($builder->create('order_item_type', HiddenType::class)
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->entityManager,
                    OrderItemTypeMaster::class
                )))
            ->add($builder->create('ProductClass', HiddenType::class)
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->entityManager,
                    ProductClass::class
                )));

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var OrderItem $OrderItem */
            $OrderItem = $event->getData();

            $OrderItemType = $OrderItem->getOrderItemType();
            switch ($OrderItemType->getId()) {
                case OrderItemTypeMaster::PRODUCT:
                    $ProductClass = $OrderItem->getProductClass();
                    $Product = $ProductClass->getProduct();
                    $OrderItem->setProduct($Product);
                    if (null === $OrderItem->getPrice()) {
                        $OrderItem->setPrice($ProductClass->getPrice02());
                    }
                    if (null === $OrderItem->getProductCode()) {
                        $OrderItem->setProductCode($ProductClass->getCode());
                    }
                    if (null === $OrderItem->getClassName1() && $ProductClass->hasClassCategory1()) {
                        $ClassCategory1 = $ProductClass->getClassCategory1();
                        $OrderItem->setClassName1($ClassCategory1->getClassName()->getName());
                        $OrderItem->setClassCategoryName1($ClassCategory1->getName());
                    }
                    if (null === $OrderItem->getClassName2() && $ProductClass->hasClassCategory2()) {
                        if ($ClassCategory2 = $ProductClass->getClassCategory2()) {
                            $OrderItem->setClassName2($ClassCategory2->getClassName()->getName());
                            $OrderItem->setClassCategoryName2($ClassCategory2->getName());
                        }
                    }
                    // 商品明細は税抜表示・課税
                    if (null === $OrderItem->getTaxDisplayType()) {
                        $OrderItem->setTaxDisplayType($this->entityManager->find(TaxDisplayType::class,
                            TaxDisplayType::EXCLUDED));
                    }
                    if (null === $OrderItem->getTaxType()) {
                        $OrderItem->setTaxType($this->entityManager->find(TaxType::class, TaxType::TAXATION));
                    }
                    if (null === $OrderItem->getTaxRule()) {
                        $TaxRule = $this->taxRuleRepository->getByRule($Product, $ProductClass);
                        $OrderItem->setTaxRule($TaxRule->getId());
                        $OrderItem->setTaxRate($TaxRule->getTaxRate());
                    }
                    break;
                case OrderItemTypeMaster::DELIVERY_FEE:
                    // 送料明細は税込表示・課税
                    if (null === $OrderItem->getTaxDisplayType()) {
                        $OrderItem->setTaxDisplayType($this->entityManager->find(TaxDisplayType::class,
                            TaxDisplayType::INCLUDED));
                    }
                    if (null === $OrderItem->getTaxType()) {
                        $OrderItem->setTaxType($this->entityManager->find(TaxType::class, TaxType::TAXATION));
                    }
                    if (null === $OrderItem->getTaxRule()) {
                        $TaxRule = $this->taxRuleRepository->getByRule();
                        $OrderItem->setTaxRule($TaxRule->getId());
                        $OrderItem->setTaxRate($TaxRule->getTaxRate());
                    }
                    // no break
                case OrderItemTypeMaster::CHARGE:
                    // 手数料明細は税込表示・課税
                    if (null === $OrderItem->getTaxDisplayType()) {
                        $OrderItem->setTaxDisplayType($this->entityManager->find(TaxDisplayType::class,
                            TaxDisplayType::INCLUDED));
                    }
                    if (null === $OrderItem->getTaxType()) {
                        $OrderItem->setTaxType($this->entityManager->find(TaxType::class, TaxType::TAXATION));
                    }
                    if (null === $OrderItem->getTaxRule()) {
                        $TaxRule = $this->taxRuleRepository->getByRule();
                        $OrderItem->setTaxRule($TaxRule->getId());
                        $OrderItem->setTaxRate($TaxRule->getTaxRate());
                    }
                    break;
                case OrderItemTypeMaster::DISCOUNT:
                    // 値引き明細は税抜表示・課税
                    if (null === $OrderItem->getTaxDisplayType()) {
                        $OrderItem->setTaxDisplayType($this->entityManager->find(TaxDisplayType::class,
                            TaxDisplayType::EXCLUDED));
                    }
                    if (null === $OrderItem->getTaxType()) {
                        $OrderItem->setTaxType($this->entityManager->find(TaxType::class, TaxType::TAXATION));
                    }
                    if (null === $OrderItem->getTaxRule()) {
                        $TaxRule = $this->taxRuleRepository->getByRule();
                        $OrderItem->setTaxRule($TaxRule->getId());
                        $OrderItem->setTaxRate($TaxRule->getTaxRate());
                    }
                    break;
            }

            // TaxRuleEventSubscriberを呼ぶため.
            $this->entityManager->persist($OrderItem);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrderItem::class,
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
