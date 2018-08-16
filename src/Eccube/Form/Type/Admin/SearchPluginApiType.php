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

use Eccube\Entity\Master\PageMax;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        // Todo: constant for the API key
        $priceType = [
            'charge' => trans('admin.store.plugin_owners_search.form.price_type.fee'),
            'free' => trans('admin.store.plugin_owners_search.form.price_type.free'),
        ];
        // Todo: constant for the API key
        $orderBy = [
            'date' => trans('admin.store.plugin_owners_search.form.sort.new'),
            'price' => trans('admin.store.plugin_owners_search.form.sort.price'),
            'dl' => trans('admin.store.plugin_owners_search.form.sort.dl'),
        ];

        $builder->add('category_id', ChoiceType::class, [
            'choices' => array_flip($category),
            'placeholder' => 'admin.store.plugin_owners_search.form.placeholder',
            'required' => false,
            'label' => 'admin.store.plugin_owners_search.form.category',
        ]);
        $builder->add('price_type', ChoiceType::class, [
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
        $builder->add('sort', ChoiceType::class, [
            'label' => 'searchproduct.label.sort_by',
            'required' => false,
            'placeholder' => null,
            'choices' => array_flip($orderBy),
        ]);
        $builder->add('page_count', EntityType::class, [
            'required' => false,
            'placeholder' => null,
            'class' => PageMax::class,
            'choice_label' => function (PageMax $pageMax) {
                return $pageMax->getName().trans('admin.product.index.num');
            }
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'category' => [],
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
