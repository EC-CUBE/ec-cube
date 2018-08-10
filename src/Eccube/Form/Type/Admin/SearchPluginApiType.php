<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Form\Type\Admin;

use Eccube\Form\Type\Master\ProductListMaxType;
use Eccube\Form\Type\Master\ProductListOrderByType;
use Eccube\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchPluginApiType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $category = $options['category'];
        $priceType = $options['priceType'];

        $orderBy = [
            'date' => '新着順',
            'price' => '価格が低い順',
            'dl' => 'DL',
        ];

        $builder->add('mode', HiddenType::class, [
            'data' => 'search',
        ]);

        $builder->add('category_id', ChoiceType::class, [
            'choices' => array_flip($category),
            'placeholder' => 'admin.store.plugin_owners_search.form.placeholder',
            'required' => false,
            'label' => 'admin.store.plugin_owners_search.form.category',
        ]);

        $builder->add('price_type_id', ChoiceType::class, [
            'choices' => array_flip($priceType),
            'placeholder' => 'admin.store.plugin_owners_search.form.placeholder',
            'required' => false,
            'label' => 'admin.store.plugin_owners_search.form.price_type',
        ]);

        $builder->add('keyword', SearchType::class, [
            'required' => false,
            'label' => 'admin.store.plugin_owners_search.form.keyword',
            'attr' => [
                'maxlength' => 50,
            ],
        ]);

//        $builder->add('pageno', HiddenType::class, []);
//        $builder->add('disp_number', ProductListMaxType::class, [
//            'label' => 'searchproduct.label.number_results',
//        ]);

        $builder->add('sort', ChoiceType::class, [
            'label' => 'searchproduct.label.sort_by',
            'required' => false,
            'placeholder' => null,
            'choices' => array_flip($orderBy),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
//            'csrf_protection' => false,
//            'allow_extra_fields' => true,
            'category' => [],
            'priceType' => []
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'search_plugin';
    }
}
