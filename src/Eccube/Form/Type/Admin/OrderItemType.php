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

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Master\OrderItemType as OrderItemTypeMaster;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\OrderItem;
use Eccube\Entity\ProductClass;
use Eccube\Form\DataTransformer\EntityToIdTransformer;
use Eccube\Form\Type\PriceType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Repository\OrderItemRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Util\StringUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderItemType extends AbstractType
{
    protected BaseInfo $BaseInfo;

    /**
     * OrderItemType constructor.
     *
     * @throws \Exception
     */
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected EccubeConfig $eccubeConfig,
        BaseInfoRepository $baseInfoRepository,
        protected ProductClassRepository $productClassRepository,
        protected OrderItemRepository $orderItemRepository,
        protected OrderItemTypeRepository $orderItemTypeRepository,
        protected TaxRuleRepository $taxRuleRepository,
        protected ValidatorInterface $validator,
    ) {
        $this->BaseInfo = $baseInfoRepository->get();
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $options
     */
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product_name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: $this->eccubeConfig['eccube_mtext_len']),
                ],
            ])
            ->add('price', PriceType::class, [
                'accept_minus' => true,
            ])
            ->add('quantity', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: $this->eccubeConfig['eccube_int_len']),
                ],
            ])
            ->add('tax_rate', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range(min: 0),
                    new Assert\Regex(
                        pattern: "/^\d+(\.\d+)?$/u",
                        message: 'form_error.float_only'
                    ),
                ],
            ])
            ->add('point_rate', HiddenType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Range(min: 0),
                    new Assert\Regex(
                        pattern: "/^\d+(\.\d+)?$/u",
                        message: 'form_error.float_only'
                    ),
                ],
            ])
        ;

        $builder
            ->add($builder->create('order_item_type', HiddenType::class)
                ->addModelTransformer(new EntityToIdTransformer(
                    $this->entityManager,
                    OrderItemTypeMaster::class
                )))
            ->add($builder->create('tax_type', HiddenType::class)
                ->addModelTransformer(new EntityToIdTransformer(
                    $this->entityManager,
                    TaxType::class
                )))
            ->add($builder->create('ProductClass', HiddenType::class)
                ->addModelTransformer(new EntityToIdTransformer(
                    $this->entityManager,
                    ProductClass::class
                )));

        // 受注編集画面で明細の最終行を削除し, 保存せずに明細を追加すると, 未保存のため DB から再読込された
        // 削除対象の OrderItem に新しい明細がバインドされ, 明細種別/規格が変わっても tax_display_type や
        // 商品コード・規格名等の派生値が旧明細から引き継がれてしまう(#6444). これを防ぐため, 明細種別または
        // ProductClass が元の値から変わった場合は派生値をクリアし, 後続の補完ロジックで正しく再設定させる.
        $originalOrderItemTypeId = null;
        $originalProductClassId = null;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use (&$originalOrderItemTypeId, &$originalProductClassId): void {
            /** @var OrderItem|null $OrderItem */
            $OrderItem = $event->getData();
            if (null === $OrderItem) {
                return;
            }
            $originalOrderItemTypeId = $OrderItem->getOrderItemType()?->getId();
            $originalProductClassId = $OrderItem->getProductClass()?->getId();
        });

        // 既存の補完 POST_SUBMIT リスナより先に実行する必要があるため priority を高くする.
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use (&$originalOrderItemTypeId, &$originalProductClassId): void {
            /** @var OrderItem|null $OrderItem */
            $OrderItem = $event->getData();
            if (null === $OrderItem) {
                return;
            }

            // 元データを持たない新規追加明細は既存の補完ロジックに委ねる.
            if (null === $originalOrderItemTypeId) {
                return;
            }

            $newOrderItemTypeId = $OrderItem->getOrderItemType()?->getId();
            $newProductClassId = $OrderItem->getProductClass()?->getId();

            // 種別・ProductClass ともに変化していなければ引き継ぎ問題は起きないため何もしない.
            if ($originalOrderItemTypeId === $newOrderItemTypeId && $originalProductClassId === $newProductClassId) {
                return;
            }

            // 旧明細から引き継がれた派生値をクリアする. tax_adjust は rounding_type を null にすると
            // 後続の補完で TaxRule から再設定されるため, ここでは触らない.
            // processor_name はフォーム項目ではないため, クリアしないと旧値が残る. 特にポイント明細の
            // スロットが再利用されると PointHelper::removePointDiscountItem が processor_name だけで判定して
            // 追加明細を削除してしまうため, ここでクリアしておく.
            $OrderItem->setProduct()->setProductCode()->setClassName1()->setClassName2()->setClassCategoryName1()->setClassCategoryName2()->setTaxDisplayType()->setRoundingType()->setProcessorName();
        }, 10);

        // 受注明細フォームの税率を補完する
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $OrderItem = $event->getData();

            if (!isset($OrderItem['tax_rate']) || StringUtil::isBlank($OrderItem['tax_rate'])) {
                $orderItemTypeId = $OrderItem['order_item_type'];

                if ($orderItemTypeId == OrderItemTypeMaster::PRODUCT) {
                    /** @var ProductClass $ProductClass */
                    $ProductClass = $this->productClassRepository->find($OrderItem['ProductClass']);
                    $Product = $ProductClass->getProduct();
                    $TaxRule = $this->taxRuleRepository->getByRule($Product, $ProductClass);

                    if (!isset($OrderItem['tax_type']) || StringUtil::isBlank($OrderItem['tax_type'])) {
                        $OrderItem['tax_type'] = TaxType::TAXATION;
                    }
                } else {
                    if ($orderItemTypeId == OrderItemTypeMaster::DISCOUNT && $OrderItem['tax_type'] == TaxType::NON_TAXABLE) {
                        $OrderItem['tax_rate'] = '0';
                        $event->setData($OrderItem);

                        return;
                    }

                    $TaxRule = $this->taxRuleRepository->getByRule();
                }

                $OrderItem['tax_rate'] = $TaxRule->getTaxRate();
                $event->setData($OrderItem);
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
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
                    if (null === $OrderItem->getRoundingType()) {
                        $TaxRule = $this->taxRuleRepository->getByRule($Product, $ProductClass);
                        $OrderItem->setRoundingType($TaxRule->getRoundingType())
                            ->setTaxAdjust($TaxRule->getTaxAdjust());
                    }
                    break;
                default:
                    if (null === $OrderItem->getRoundingType()) {
                        $TaxRule = $this->taxRuleRepository->getByRule();
                        $OrderItem->setRoundingType($TaxRule->getRoundingType())
                            ->setTaxAdjust($TaxRule->getTaxAdjust());
                    }
            }
        });

        // price, quantityのバリデーション
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $form = $event->getForm();
            /** @var OrderItem $OrderItem */
            $OrderItem = $event->getData();

            $OrderItemType = $OrderItem->getOrderItemType();
            switch ($OrderItemType->getId()) {
                // 商品明細: 金額 -> 正, 個数 -> 正負
                case OrderItemTypeMaster::PRODUCT:
                    $errors = $this->validator->validate($OrderItem->getPrice(), [new Assert\GreaterThanOrEqual(0)]);
                    $this->addErrorsIfExists($form['price'], $errors);
                    break;

                    // 値引き明細: 金額 -> 負, 個数 -> 正
                case OrderItemTypeMaster::DISCOUNT:
                    $errors = $this->validator->validate($OrderItem->getPrice(), [new Assert\LessThanOrEqual(0)]);
                    $this->addErrorsIfExists($form['price'], $errors);
                    $errors = $this->validator->validate($OrderItem->getQuantity(), [new Assert\GreaterThanOrEqual(0)]);
                    $this->addErrorsIfExists($form['quantity'], $errors);

                    break;

                    // 送料, 手数料: 金額 -> 正, 個数 -> 正
                case OrderItemTypeMaster::DELIVERY_FEE:
                case OrderItemTypeMaster::CHARGE:
                    $errors = $this->validator->validate($OrderItem->getPrice(), [new Assert\GreaterThanOrEqual(0)]);
                    $this->addErrorsIfExists($form['price'], $errors);
                    $errors = $this->validator->validate($OrderItem->getQuantity(), [new Assert\GreaterThanOrEqual(0)]);
                    $this->addErrorsIfExists($form['quantity'], $errors);

                    break;

                default:
                    break;
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderItem::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'order_item';
    }

    protected function addErrorsIfExists(FormInterface $form, ConstraintViolationListInterface $errors): void
    {
        if (count($errors) < 1) {
            return;
        }

        foreach ($errors as $error) {
            $form->addError(new FormError(
                $error->getMessage(),
                $error->getMessageTemplate(),
                $error->getParameters(),
                $error->getPlural()));
        }
    }
}
