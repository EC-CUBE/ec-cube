<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Form\Type;

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
     * @param CategoryRepository $categoryRepository
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
            'placeholder' => 'common.select__all_products',
            'required' => false,
        ]);
        $builder->add('name', SearchType::class, [
            'required' => false,
            'attr' => [
                'maxlength' => 50,
            ],
        ]);
        $builder->add('pageno', HiddenType::class, []);
        $builder->add('disp_number', ProductListMaxType::class, [
            'label' => false,
        ]);
        $builder->add('orderby', ProductListOrderByType::class, [
            'label' => false,
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
