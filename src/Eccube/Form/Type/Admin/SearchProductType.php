<?php

namespace Eccube\Form\Type\Admin;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\Extension\Core\Type;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;
use \Symfony\Component\Validator\ExecutionContextInterface;

class SearchProductType extends AbstractType
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
            ->add('id', 'integer', array(
                'label' => '商品ID',
                'required' => false,
                'constraints' => array(
                    new Assert\Type(array(
                        'type' => 'integer',
                    )),
                ),
            ))
            ->add('code', 'text', array(
                'label' => '商品コード',
                'required' => false,
            ))
            ->add('name', 'text', array(
                'label' => '商品名',
                'required' => false,
            ))
            ->add('category_id', 'category', array(
                'label' => 'カテゴリ',
                'empty_value' => '選択してください',
                'required' => false,
            ))
            ->add('status', 'disp', array(
                'label' => '種別',
                'multiple'=> true,
                'required' => false,
            ))
            ->add('product_status', 'status', array(
                'label' => '商品ステータス',
                'multiple'=> true,
                'required' => false,
            ))
            ->add('register_start', 'date', array(
                'label' => '登録・更新日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('register_end', 'date', array(
                'label' => '登録・更新日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('pageno', 'hidden', array(
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_search_product';
    }

}