<?php

namespace Eccube\Form\Type\Master;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class OrderStatusType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Eccube\Entity\Master\OrderStatus',
            'property' => 'name',
            'label' => false,
            'multiple'=> false,
            'expanded' => false,
            'required' => false,
            'empty_value' => '-',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'order_status';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

}