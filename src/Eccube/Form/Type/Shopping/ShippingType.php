<?php

namespace Eccube\Form\Type\Shopping;

use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Entity\Shipping;
use Eccube\Repository\DeliveryFeeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @FormType
 */
class ShippingType extends AbstractType
{
    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @var \Eccube\Application $app
     * @Inject(Application::class)
     */
    protected $app;

    /**
     * @var DeliveryRepository
     * @Inject("eccube.repository.delivery")
     */
    protected $deliveryRepository;

    /**
     * @var DeliveryFeeRepository
     * @Inject("eccube.repository.delivery_fee")
     */
    protected $deliveryFeeRepository;

    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'OrderItems',
                CollectionType::class,
                array(
                    'entry_type' => OrderItemType::class,
                )
            );

        // 配送業者のプルダウンを生成
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /* @var Shipping $Shipping */
                $Shipping = $event->getData();
                if (is_null($Shipping) || !$Shipping->getId()) {
                    return;
                }

                // 配送商品に含まれる販売種別を抽出.
                $OrderItems = $Shipping->getProductOrderItems();
                $SaleTypes = array();
                foreach ($OrderItems as $OrderItem) {
                    $ProductClass = $OrderItem->getProductClass();
                    $SaleType = $ProductClass->getSaleType();
                    $SaleTypes[$SaleType->getId()] = $SaleType;
                }

                // 販売種別に紐づく配送業者を取得.
                $Deliveries = $this->deliveryRepository->getDeliveries($SaleTypes);

                // 配送業者のプルダウンにセット.
                $form = $event->getForm();
                $form->add(
                    'Delivery',
                    EntityType::class,
                    array(
                        'required' => false,
                        'label' => '配送業者',
                        'class' => 'Eccube\Entity\Delivery',
                        'choice_label' => 'name',
                        'choices' => $Deliveries,
                        'placeholder' => null,
                        'constraints' => array(
                            new NotBlank(),
                        ),
                    )
                );
            }
        );

        // お届け日のプルダウンを生成
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $Shipping = $event->getData();
                if (is_null($Shipping) || !$Shipping->getId()) {
                    return;
                }

                // お届け日の設定
                $minDate = 0;
                $deliveryDateFlag = false;

                // 配送時に最大となる商品日数を取得
                foreach ($Shipping->getOrderItems() as $detail) {
                    $ProductClass = $detail->getProductClass();
                    if (is_null($ProductClass)) {
                        continue;
                    }
                    $deliveryDate = $ProductClass->getDeliveryDate();
                    if (is_null($deliveryDate)) {
                        continue;
                    }
                    if ($deliveryDate->getValue() < 0) {
                        // 配送日数がマイナスの場合はお取り寄せなのでスキップする
                        $deliveryDateFlag = false;
                        break;
                    }

                    if ($minDate < $deliveryDate->getValue()) {
                        $minDate = $deliveryDate->getValue();
                    }
                    // 配送日数が設定されている
                    $deliveryDateFlag = true;
                }

                // 配達最大日数期間を設定
                $deliveryDates = array();

                // 配送日数が設定されている
                if ($deliveryDateFlag) {
                    $period = new \DatePeriod (
                        new \DateTime($minDate.' day'),
                        new \DateInterval('P1D'),
                        new \DateTime($minDate + $this->appConfig['deliv_date_end_max'].' day')
                    );

                    foreach ($period as $day) {
                        $deliveryDates[$day->format('Y/m/d')] = $day->format('Y/m/d');
                    }
                }

                $form = $event->getForm();
                $form
                    ->add(
                        'shipping_delivery_date',
                        ChoiceType::class,
                        array(
                            'choices' => array_flip($deliveryDates),
                            'required' => false,
                            'placeholder' => '指定なし',
                            'mapped' => false,
                        )
                    );
            }
        );
        // お届け時間のプルダウンを生成
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $Shipping = $event->getData();
                if (is_null($Shipping) || !$Shipping->getId()) {
                    return;
                }

                $DeliveryTimes = array();
                $Delivery = $Shipping->getDelivery();
                if ($Delivery) {
                    $DeliveryTimes = $Delivery->getDeliveryTimes();
                }

                $form = $event->getForm();
                $form->add(
                    'DeliveryTime',
                    EntityType::class,
                    array(
                        'label' => 'お届け時間',
                        'class' => 'Eccube\Entity\DeliveryTime',
                        'choice_label' => 'deliveryTime',
                        'choices' => $DeliveryTimes,
                        'required' => false,
                        'placeholder' => '指定なし',
                        'mapped' => false
                    )
                );
            }
        );

        // POSTされないデータをエンティティにセットする.
        // TODO Calculatorで行うのが適切.
        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
            $Shipping = $event->getData();
            $Delivery = $Shipping->getDelivery();

            if ($Delivery) {
                $DeliveryFee = $this->deliveryFeeRepository->findOneBy(array(
                    'Delivery' => $Delivery,
                    'Pref' => $Shipping->getPref()
                ));

                $Shipping->setFeeId($DeliveryFee ? $DeliveryFee->getId() : null);
                $Shipping->setShippingDeliveryFee($DeliveryFee->getFee());
                $Shipping->setShippingDeliveryName($Delivery->getName());
            }
            $form = $event->getForm();
            $DeliveryTime = $form['DeliveryTime']->getData();
            if ($DeliveryTime) {
                $Shipping->setShippingDeliveryTime($DeliveryTime->getDeliveryTime());
                $Shipping->setTimeId($DeliveryTime->getId());
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Eccube\Entity\Shipping',
            )
        );
    }

    public function getBlockPrefix()
    {
        return '_shopping_shipping';
    }
}
