<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Form\Type\Admin;

use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Form\Type\Master\CategoryType as MasterCategoryType;
use Eccube\Form\Type\Master\ProductStatusType;
use Eccube\Repository\Master\ProductStatusRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

;

/**
 * @FormType
 */
class SearchProductType extends AbstractType
{

    /**
     * @Inject(ProductStatusRepository::class)
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', TextType::class, array(
                'label' => 'searchproduct.label.product_code',
                'required' => false,
            ))
            /*
            ->add('code', 'text', array(
                'label' => '商品コード',
                'required' => false,
            ))
            ->add('name', 'text', array(
                'label' => '商品名',
                'required' => false,
            ))
             */
            ->add('category_id', MasterCategoryType::class, array(
                'label' => 'searchproduct.label.category',
                'placeholder' => 'searchproduct.placeholder.select',
                'required' => false,
            ))
            ->add('status', ProductStatusType::class, array(
                'label' => 'searchproduct.label.type',
                'multiple'=> true,
                'required' => false,
                'data' => $this->productStatusRepository->findBy(['id' => [
                    ProductStatus::DISPLAY_SHOW,
                    ProductStatus::DISPLAY_HIDE
                ]])
            ))
            ->add('create_date_start', DateType::class, array(
                'label' => 'searchproduct.label.registration_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('create_date_end', DateType::class, array(
                'label' => 'searchproduct.label.registration_date_to',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('update_date_start', DateType::class, array(
                'label' => '更新日(FROM)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('update_date_end', DateType::class, array(
                'label' => 'searchproduct.label.updated_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('link_status', HiddenType::class, array(
                'mapped' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_search_product';
    }
}
