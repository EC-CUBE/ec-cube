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

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Form\Type\Master\CategoryType as MasterCategoryType;
use Eccube\Form\Type\Master\CustomerStatusType;
use Eccube\Form\Type\Master\PrefType;
use Eccube\Form\Type\Master\SexType;
use Eccube\Repository\Master\CustomerStatusRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SearchCustomerType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var CustomerStatusRepository
     */
    protected $customerStatusRepository;

    /**
     * SearchCustomerType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param CustomerStatusRepository $customerStatusRepository
     */
    public function __construct(
        CustomerStatusRepository $customerStatusRepository,
        EccubeConfig $eccubeConfig
    ) {
        $this->eccubeConfig = $eccubeConfig;
        $this->customerStatusRepository = $customerStatusRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $months = range(1, 12);
        $builder
            // 会員ID・メールアドレス・名前・名前(フリガナ)
            ->add('multi', TextType::class, [
                'label' => '会員ID・メールアドレス・名前・名前(フリガナ)',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['stext_len']]),
                ],
            ])
            ->add('company_name', TextType::class, [
                'label' => '会社名',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['stext_len']]),
                ],
            ])
            ->add('pref', PrefType::class, [
                'label' => '都道府県',
                'required' => false,
            ])
            ->add('sex', SexType::class, [
                'label' => '性別',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('birth_month', ChoiceType::class, [
                'label' => '誕生月',
                'required' => false,
                'choices' => array_combine($months, $months),
            ])
            ->add('birth_start', BirthdayType::class, [
                'label' => '誕生日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('birth_end', BirthdayType::class, [
                'label' => '誕生日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('tel', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^[\d-]+$/u",
                        'message' => 'form.type.admin.nottelstyle',
                    ]),
                ],
            ])
            ->add('buy_total_start', IntegerType::class, [
                'label' => '購入金額',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['price_len']]),
                ],
            ])
            ->add('buy_total_end', IntegerType::class, [
                'label' => '購入金額',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['price_len']]),
                ],
            ])
            ->add('buy_times_start', IntegerType::class, [
                'label' => '購入回数',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['int_len']]),
                ],
            ])
            ->add('buy_times_end', IntegerType::class, [
                'label' => '購入回数',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['int_len']]),
                ],
            ])
            ->add('create_date_start', DateType::class, [
                'label' => '登録日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('create_date_end', DateType::class, [
                'label' => '登録日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('update_date_start', DateType::class, [
                'label' => '更新日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('update_date_end', DateType::class, [
                'label' => '更新日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('last_buy_start', DateType::class, [
                'label' => '最終購入日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('last_buy_end', DateType::class, [
                'label' => '最終購入日',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('buy_product_name', TextType::class, [
                'label' => '購入商品名',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['stext_len']]),
                ],
            ])
            ->add('buy_product_code', TextType::class, [
                'label' => '購入商品コード',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['stext_len']]),
                ],
            ])
            ->add('buy_category', MasterCategoryType::class, [
                'label' => '商品カテゴリ',
                'required' => false,
            ])
            ->add('customer_status', CustomerStatusType::class, [
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'placeholder' => false,
                'data' => $this->customerStatusRepository->findBy([
                    'id' => [
                        CustomerStatus::PROVISIONAL,
                        CustomerStatus::REGULAR,
                    ]
                ])
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_search_customer';
    }
}
