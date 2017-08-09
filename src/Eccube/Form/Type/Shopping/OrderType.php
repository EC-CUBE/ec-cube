<?php

namespace Eccube\Form\Type\Shopping;

use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Entity\Order;
use Eccube\Entity\ShipmentItem;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\PaymentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderType extends AbstractType
{
    /**
     * @var Application
     * @Inject(Application::class)
     */
    protected $app;

    /**
     * @var OrderRepository
     * @Inject("eccube.repository.order")
     */
    protected $orderRepository;

    /**
     * @var DeliveryRepository
     * @Inject("eccube.repository.delivery")
     */
    protected $deliveryRepository;

    /**
     * @var PaymentRepository
     * @Inject("eccube.repository.payment")
     */
    protected $paymentRepository;

    public function __construct() {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'message',
                TextareaType::class,
                [
                    'required' => false,
                    'constraints' => [
                        new Length(['min' => 0, 'max' => 3000]),
                    ],
                ]
            )
            ->add(
                'Shippings',
                CollectionType::class,
                [
                    'entry_type' => ShippingType::class,
                    'by_reference' => false
                ]
            )->add(
                'mode',
                HiddenType::class,
                [
                    'mapped' => false,
                ]
            )->add(
                'param',
                HiddenType::class,
                [
                    'mapped' => false,
                ]
            );

        // 支払い方法のプルダウンを生成
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var Order $Order */
                $Order = $event->getData();
                if (is_null($Order) || !$Order->getId()) {
                    return;
                }

                // 受注明細に含まれる商品種別を抽出.
                $ProductTypes = array_reduce($Order->getShipmentItems()->toArray(), function($results, $ShipmentItem) {
                    /* @var ShipmentItem $ShipmentItem */
                    $ProductClass = $ShipmentItem->getProductClass();
                    if (!is_null($ProductClass)) {
                        $ProductType = $ProductClass->getProductType();
                        $results[$ProductType->getId()] = $ProductType;
                    }
                    return $results;
                }, []);

                // 商品種別に紐づく配送業者を抽出
                $Deliveries = $this->deliveryRepository->getDeliveries($ProductTypes);
                // 利用可能な支払い方法を抽出.
                $Payments = $this->paymentRepository->findAllowedPayments($Deliveries, true);

                $form = $event->getForm();
                $form->add(
                    'Payment',
                    EntityType::class,
                    [
                        'class' => 'Eccube\Entity\Payment',
                        'choice_label' => function($Payment) {
                            return $Payment->getMethod();
                        },
                        'expanded' => true,
                        'multiple' => false,
                        'placeholder' => null,
                        'constraints' => [
                            new NotBlank(),
                        ],
                        'choices' => $Payments,
                    ]
                );
            }
        );

        // POSTされないデータをエンティティにセットする.
        // TODO Calculatorで行うのが適切.
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var Order $Order */
                $Order = $event->getData();
                $Payment = $Order->getPayment();
                $Order->setPaymentMethod($Payment ? $Payment->getMethod() : null);
                // TODO CalculateChargeStrategy でセットする
                // $Order->setCharge($Payment ? $Payment->getCharge() : null);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Eccube\Entity\Order',
            ]
        );
    }

    public function getBlockPrefix()
    {
        return '_shopping_order';
    }
}
