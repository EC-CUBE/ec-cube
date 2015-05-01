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
                'label' => '商品コード',
                'required' => false,
            ))
            ->add('stock', 'text', array(
                'label' => '在庫数',
                'required' => false,
            ))
            ->add('stock_unlimited', 'checkbox', array(
                'label' => '無制限',
                'value' => '1',
                'required' => false,
            ))
            ->add('sale_limit', 'integer', array(
                'label' => '販売制限数',
            ))
            ->add('price01', 'money', array(
                'label' => '通常価格',
                'currency' => 'JPY',
                'precision' => 0,
                'required' => false,
            ))
            ->add('price02', 'money', array(
                'label' => '販売価格',
                'currency' => 'JPY',
                'precision' => 0,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('tax_rate', 'integer', array(
                'label' => '消費税率',
                'mapped' => false,
            ))
            ->add('deliv_fee', 'integer', array(
                'label' => '商品送料',
                'required' => false,
            ))
            ->add('point_rate', 'integer', array(
                'label' => 'ポイント付与率',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('product_type', 'product_type', array(
                'label' => '商品種別',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('down_filename', 'text', array(
                'label' => 'ダウンロードファイル名',
            ))
            ->add('down_file', 'file', array(
                'label' => 'ダウンロードファイル',
                'mapped' => false,
            ))
            ->add('down_real_filename', 'hidden')
            ->add('class_category1', null, array(
                'required' => false,
                'read_only' => true,
                'disabled' => true,
            ))
            ->add('class_category2', null, array(
                'required' => false,
                'read_only' => true,
                'disabled' => true,
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