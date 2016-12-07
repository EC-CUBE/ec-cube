<?php

namespace Eccube\Form\Type\Shopping;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ShippingType extends AbstractType
{
    /** @var \Eccube\Application */
    protected $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shipping_delivery_date', 'date', array(
                'label' => 'お届け日',
                'placeholder' => '',
                'format' => 'yyyy-MM-dd',
                'required' => false,
            ))
            ->add('DeliveryTime', 'entity', array(
                'label' => 'お届け時間',
                'class' => 'Eccube\Entity\DeliveryTime',
                'property' => 'delivery_time',
                'empty_value' => '指定なし',
                'empty_data' => null,
                'required' => false,
            ))
            ->add(
                'ShipmentItems',
                'collection',
                array(
                    'type' => '_shopping_shipment_item',
                )
            );
        //
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $form = $event->getForm();
            $form->add('Delivery', 'entity', array(
                'required' => false,
                'label' => '配送業者',
                'class' => 'Eccube\Entity\Delivery',
                'property' => 'name',
                'empty_value' => '選択してください',
                'empty_data' => null,
                'constraints' => array(
                    new NotBlank(),
                ),
            ));
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

    public function getName()
    {
        return '_shopping_shipping';
    }
}