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

namespace Eccube\Form\Type;

use Eccube\Application;
use Eccube\Form\Type\Master\ProductListMaxType;
use Eccube\Form\Type\Master\ProductListOrderByType;
use Eccube\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchProductType extends AbstractType
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SearchProductType constructor.
     *
     * @param Application $app
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $Categories = $this->categoryRepository
            ->getList(null, true);

        $builder->add('mode', HiddenType::class, [
            'data' => 'search',
        ]);
        $builder->add('category_id', EntityType::class, [
            'class' => 'Eccube\Entity\Category',
            'choice_label' => 'NameWithLevel',
            'choices' => $Categories,
            'placeholder' => 'searchproduct.placeholder.all_products',
            'required' => false,
            'label' => 'searchproduct.label.select_categories',
        ]);
        $builder->add('name', SearchType::class, [
            'required' => false,
            'label' => 'searchproduct.label.product_name',
            'attr' => [
                'maxlength' => 50,
            ],
        ]);
        $builder->add('pageno', HiddenType::class, []);
        $builder->add('disp_number', ProductListMaxType::class, [
            'label' => 'searchproduct.label.number_results',
        ]);
        $builder->add('orderby', ProductListOrderByType::class, [
            'label' => 'searchproduct.label.sort_by',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'search_product';
    }
}
