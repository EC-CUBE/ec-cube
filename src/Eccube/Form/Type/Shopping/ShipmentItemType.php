<?php

namespace Eccube\Form\Type\Shopping;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipmentItemType extends AbstractType
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Eccube\Entity\ShipmentItem',
            )
        );
    }

    public function getBlockPrefix()
    {
        return '_shopping_shipment_item';
    }
}