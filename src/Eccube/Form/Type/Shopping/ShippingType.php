<?php

namespace Eccube\Form\Type\Shopping;

use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\DeliveryFeeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ShippingType extends AbstractType
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var array */
    protected $config;

    /** @var  DeliveryRepository */
    protected $deliveryRepository;

    /** @var DeliveryFeeRepository */
    protected $deliveryFeeRepository;

    /** @var BaseInfoRepository */
    protected $baseInfoRepository;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
        $this->config = $app['config'];
        $this->deliveryRepository = $app['eccube.repository.delivery'];
        $this->deliveryFeeRepository = $app['eccube.repository.delivery_fee'];
        $this->baseInfoRepository = $app['eccube.repository.base_info'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'ShipmentItems',
                CollectionType::class,
                array(
                    'type' => '_shopping_shipment_item',
                )
            );

        // 配送業者のプルダウンを生成
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $Shipping = $event->getData();
                if (is_null($Shipping) || !$Shipping->getId()) {
                    return;
                }

                // 配送商品に含まれる商品種別を抽出.
                $ShipmentItems = $Shipping->getShipmentItems();
                $ProductTypes = array();
                foreach ($ShipmentItems as $ShipmentItem) {
                    $ProductClass = $ShipmentItem->getProductClass();
                    $ProductType = $ProductClass->getProductType();
                    $ProductTypes[$ProductType->getId()] = $ProductType;
                }

                // 商品種別に紐づく配送業者を取得.
                $Deliveries = $this->deliveryRepository->getDeliveries($ProductTypes);

                // 配送業者のプルダウンにセット.
                $form = $event->getForm();
                $form->add(
                    'Delivery',
                    'entity',
                    array(
                        'required' => false,
                        'label' => '配送業者',
                        'class' => 'Eccube\Entity\Delivery',
                        'property' => 'name',
                        'choices' => $Deliveries,
                        'empty_value' => false,
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

                $Order = $Shipping->getOrder();

                // お届け日の設定
                $minDate = 0;
                $deliveryDateFlag = false;

                // 配送時に最大となる商品日数を取得
                foreach ($Order->getOrderDetails() as $detail) {
                    $deliveryDate = $detail->getProductClass()->getDeliveryDate();
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
                        new \DateTime($minDate + $this->config['deliv_date_end_max'].' day')
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
                            'choices' => $deliveryDates,
                            'required' => false,
                            'empty_value' => '指定なし',
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
                    'entity',
                    array(
                        'label' => 'お届け時間',
                        'class' => 'Eccube\Entity\DeliveryTime',
                        'property' => 'deliveryTime',
                        'choices' => $DeliveryTimes,
                        'required' => false,
                        'empty_value' => '指定なし',
                        'empty_data' => null,
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

                $Shipping->setDeliveryFee($DeliveryFee);
                $Shipping->setShippingDeliveryFee($DeliveryFee->getFee());
                $Shipping->setShippingDeliveryName($Delivery->getName());
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