<?php

namespace Eccube\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\DataTransformer\BooleanToStringTransformer;

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
                'value' => 1,
                'empty_data' => 0,
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
    public function getName()
    {
        return 'admin_product_class';
    }

}