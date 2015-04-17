<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ShoppingType extends AbstractType
{
    public $app;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delivery', 'entity', array(
                    'class' => 'Eccube\Entity\Deliv',
                    'property' => "name"))
            ->add('payment', 'entity', array(
                'class' => 'Eccube\Entity\Payment',
                'property' => "method"))
            ->add('delivery_date', 'entity', array(
                    'class' => 'Eccube\Entity\Master\DeliveryDate',
                    'property' => "name"))
            ->add('delivery_time', 'entity', array(
                    'class' => 'Eccube\Entity\DelivTime',
                    'property' => "deliv_time"))
            ->add('message', 'textarea', array(
                    'required' => false,
                    'constraints' => array(
                        new Assert\Length(array('min' => 0, 'max' => 500))),
            ))
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shopping';
    }
}
