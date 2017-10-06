<?php

namespace Eccube\Form\Type\Shopping;

use Eccube\Annotation\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @FormType
 */
class OrderItemType extends AbstractType
{
    public function __construct()
    {
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
                'data_class' => 'Eccube\Entity\OrderItem',
            )
        );
    }

    public function getBlockPrefix()
    {
        return '_shopping_order_item';
    }
}
