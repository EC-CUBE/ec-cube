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

namespace Eccube\Form\Type\Shopping;

use Eccube\Entity\Delivery;
use Eccube\Entity\Order;
use Eccube\Entity\Payment;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Request\Context;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class OrderType extends AbstractType
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    /**
     * @var Context
     */
    protected $requestContext;

    /**
     * OrderType constructor.
     *
     * @param OrderRepository $orderRepository
     * @param DeliveryRepository $deliveryRepository
     * @param PaymentRepository $paymentRepository
     * @param BaseInfoRepository $baseInfoRepository
     * @param Context $requestContext
     */
    public function __construct(
        OrderRepository $orderRepository,
        DeliveryRepository $deliveryRepository,
        PaymentRepository $paymentRepository,
        BaseInfoRepository $baseInfoRepository,
        Context $requestContext
    ) {
        $this->orderRepository = $orderRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->paymentRepository = $paymentRepository;
        $this->baseInfoRepository = $baseInfoRepository;
        $this->requestContext = $requestContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('message', TextareaType::class, [
            'required' => false,
            'constraints' => [
                new Length(['min' => 0, 'max' => 3000]),
            ],
        ])->add('Shippings', CollectionType::class, [
            'entry_type' => ShippingType::class,
            'by_reference' => false,
        ])->add('redirect_to', HiddenType::class, [
            'mapped' => false,
        ]);

        if ($this->baseInfoRepository->get()->isOptionPoint() && $this->requestContext->getCurrentUser()) {
            $builder->add('use_point', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid',
                    ]),
                    new Length(['max' => 11]),
                ],
            ]);
        }

        // 支払い方法のプルダウンを生成
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            /** @var Order $Order */
            $Order = $event->getData();
            if (null === $Order || !$Order->getId()) {
                return;
            }

            $Deliveries = $this->getDeliveries($Order);
            $Payments = $this->getPayments($Deliveries);
            $Payments = $this->filterPayments($Payments, $Order->getPaymentTotal());

            $form = $event->getForm();
            $this->addPaymentForm($form, $Payments, $Order->getPayment());
        });

        // 支払い方法のプルダウンを生成(Submit時)
        // 配送方法の選択によって使用できる支払い方法がかわるため, フォームを再生成する.
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Order $Order */
            $Order = $event->getData();
            if (null === $Order || !$Order->getId()) {
                return;
            }

            $Deliveries = $this->getDeliveries($Order);
            $Payments = $this->getPayments($Deliveries);
            $Payments = $this->filterPayments($Payments, $Order->getPaymentTotal());

            if (!empty($Payments) && !in_array($Order->getPayment(), $Payments)) {
                $Order->setPayment(current($Payments));
            }

            $form = $event->getForm();
            $this->addPaymentForm($form, $Payments, $Order->getPayment());
        });

        // 支払い方法のバリデーション
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var Order $Order */
            $Order = $event->getData();
            $Payment = $Order->getPayment();
            if (null === $Payment) {
                $form = $event->getForm();
                $form['Payment']->addError(new FormError('選択できるお支払方法がありません。配送方法を統一してください。'));

                return;
            }
            $Order->setPaymentMethod($Payment->getMethod());
        });
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

    private function addPaymentForm(FormInterface $form, array $choices, Payment $data = null)
    {
        $form->add('Payment', EntityType::class, [
            'class' => Payment::class,
            'choice_label' => 'method',
            'expanded' => true,
            'multiple' => false,
            'placeholder' => false,
            'constraints' => [
                new NotBlank(),
            ],
            'choices' => $choices,
            'data' => $data,
        ]);
    }

    /**
     * 出荷に紐づく配送方法を取得する.
     *
     * @param Order $Order
     *
     * @return Delivery[]
     */
    private function getDeliveries(Order $Order)
    {
        $Deliveries = [];
        foreach ($Order->getShippings() as $Shipping) {
            $Delivery = $Shipping->getDelivery();
            if ($Delivery->isVisible()) {
                $Deliveries[] = $Shipping->getDelivery();
            }
        }

        return array_unique($Deliveries);
    }

    /**
     * 配送方法に紐づく支払い方法を取得する
     * 各配送方法に共通する支払い方法のみ返す.
     *
     * @param Delivery[] $Deliveries
     *
     * @return ArrayCollection|Payment[]
     */
    private function getPayments($Deliveries)
    {
        $PaymentsByDeliveries = [];
        foreach ($Deliveries as $Delivery) {
            $PaymentOptions = $Delivery->getPaymentOptions();
            foreach ($PaymentOptions as $PaymentOption) {
                /** @var Payment $Payment */
                $Payment = $PaymentOption->getPayment();
                if ($Payment->isVisible()) {
                    $PaymentsByDeliveries[$Delivery->getId()][] = $Payment;
                }
            }
        }

        if (empty($PaymentsByDeliveries)) {
            return [];
        }

        $i = 0;
        $PaymentsIntersected = [];
        foreach ($PaymentsByDeliveries as $Payments) {
            if ($i === 0) {
                $PaymentsIntersected = $Payments;
            } else {
                $PaymentsIntersected = array_intersect($PaymentsIntersected, $Payments);
            }
            $i++;
        }

        return $PaymentsIntersected;
    }

    /**
     * 支払い方法の利用条件でフィルタをかける.
     *
     * @param Payment[] $Payments
     * @param $total
     *
     * @return Payment[]
     */
    private function filterPayments(array $Payments, $total)
    {
        return array_filter($Payments, function (Payment $Payment) use ($total) {
            $min = $Payment->getRuleMin();
            $max = $Payment->getRuleMax();

            if (null !== $min && $total < $min) {
                return false;
            }

            if (null !== $max && $total > $max) {
                return false;
            }

            return true;
        });
    }
}
