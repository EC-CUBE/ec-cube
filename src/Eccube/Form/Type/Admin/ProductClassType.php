<?php

namespace Eccube\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductClassType extends AbstractType
{
    public $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder
            ->add('code', 'text', array(
                'required' => false,
            ))
            ->add('stock', 'text', array(
                'required' => false,
            ))
            ->add('stock_unlimited', 'checkbox', array(
                'value' => '1',
                'required' => false,
            ))
            ->add('price01', 'money', array(
                'currency' => 'JPY',
                'precision' => 0,
                'required' => false,
            ))
            ->add('price02', 'money', array(
                'currency' => 'JPY',
                'precision' => 0,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('product_type', 'product_type', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\ProductClass',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_product_class';
    }

}