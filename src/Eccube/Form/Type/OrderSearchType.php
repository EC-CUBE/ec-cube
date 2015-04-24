<?php

namespace Eccube\Form\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\Extension\Core\Type;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;
use \Symfony\Component\Validator\ExecutionContextInterface;

class OrderSearchType extends AbstractType
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
            ->add('order_id_start', 'integer', array(
                'label' => '注文番号',
                'required' => false,
                'constraints' => array(
                    new Assert\Type(array(
                        'type' => 'integer',
                    )),
                ),
            ))
            ->add('order_id_end', 'integer', array(
                'label' => '注文番号',
                'required' => false,
                'constraints' => array(
                    new Assert\Type(array(
                        'type' => 'integer',
                    )),
                ),
            ))
            ->add('status', 'order_status', array(
                'label' => '対応状況',
            ))
            ->add('name', 'text', array(
                'required' => false,
            ))
            ->add('kana', 'text', array(
                'required' => false,
            ))
            ->add('email', 'email', array(
                'required' => false,
            ))
            ->add('tel', 'tel', array(
                'required' => false,
            ))
            ->add('birth_start', 'birthday', array(
                'label' => '誕生日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('birth_end', 'birthday', array(
                'label' => '誕生日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('sex', 'sex', array(
                'label' => '性別',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('payment', 'payment', array(
                'label' => '支払方法',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('order_date_start', 'date', array(
                'label' => '注文日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('order_date_end', 'date', array(
                'label' => '注文日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('update_date_start', 'date', array(
                'label' => '更新日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('update_date_end', 'date', array(
                'label' => '更新日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('payment_total_start', 'integer', array(
                'label' => '購入金額',
                'required' => false,
            ))
            ->add('payment_total_end', 'integer', array(
                'label' => '購入金額',
                'required' => false,
            ))
            ->add('buy_product_name', 'text', array(
                'label' => '購入商品名',
                'required' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'order_search';
    }

}