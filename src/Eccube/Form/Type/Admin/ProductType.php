<?php

namespace Eccube\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProductType extends AbstractType
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
            ->add('name', 'text', array(
                'label' => '商品名',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
//            ->add('Category', 'category', array(
//                'label' => '商品カテゴリ',
//                'constraints' => array(
//                    new Assert\NotBlank(),
//                ),
//            ))
            ->add('status', 'disp', array(
                'label' => '公開・非公開',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
//            ->add('ProductStatuses', 'status', array(
//                'label' => '商品ステータス',
//                'multiple'=> true,
//                'constraints' => array(
//                    new Assert\NotBlank(),
//                ),
//            ))
            ->add('DeliveryDate', 'delivery_date', array(
                'label' => '発送日目安',
                'empty_value' => '選択してください',
                'required' => false,
            ))
            ->add('Maker', 'maker', array(
                'label' => 'メーカー',
                'empty_value' => '選択してください',
                'required' => false,
            ))
            ->add('comment1', 'text', array(
                'label' => 'メーカーURL',
                'required' => false,
            ))
            ->add('comment3', 'textarea', array(
                'label' => "検索ワード",
                'required' => false,
            ))
            ->add('note', 'textarea', array(
                'label' => '備考欄(SHOP専用)',
                'required' => false,
            ))
            ->add('main_list_comment', 'textarea', array(
                'label' => '一覧-メインコメント',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('main_comment', 'textarea', array(
                'label' => '詳細-メインコメント(タグ許可)',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('main_list_image', 'text', array(
                'label' => '一覧-メイン画像',
                'required' => false,
            ))
            ->add('main_image', 'text', array(
                'label' => '詳細-メイン画像',
                'required' => false,
            ))
            ->add('main_large_image', 'text', array(
                'label' => '詳細-メイン拡大画像',
                'required' => false,
            ))
        ;
        for ($i = 1; $i <= $app['config']['productsub_max']; $i++) {
            $builder
                ->add("sub_title{$i}", 'text', array(
                    'label' => "詳細-サブタイトル({$i})",
                    'required' => false,
                ))
                ->add("sub_comment{$i}", 'textarea', array(
                    'label' => "詳細-サブコメント({$i})",
                    'required' => false,
                ))
                ->add("sub_image{$i}", 'text', array(
                    'label' => "詳細-サブ画像({$i})",
                    'required' => false,
                ))
                ->add("sub_large_image{$i}", 'text', array(
                    'label' => "詳細-サブ拡大画像({$i})",
                    'required' => false,
                ))
            ;
        }
        $builder->add('ProductClasses', 'collection', array(
            'type' => 'admin_product_class',
            'options'  => array(
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_product';
    }

}