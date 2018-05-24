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

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Form\Type\Master\CustomerStatusType;
use Eccube\Form\Type\Master\PrefType;
use Eccube\Form\Type\PriceType;
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
                'label' => 'searchcustomer.label.multi',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('customer_status', CustomerStatusType::class, [
                'label' => 'searchcustomer.label.status',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'placeholder' => false,
                'data' => $this->customerStatusRepository->findBy([
                    'id' => [
                        CustomerStatus::PROVISIONAL,
                        CustomerStatus::REGULAR,
                    ],
                ]),
            ])
            ->add('sex', SexType::class, [
                'label' => 'searchcustomer.label.sex',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('birth_month', ChoiceType::class, [
                'label' => 'searchcustomer.label.birth_month',
                'required' => false,
                'choices' => array_combine($months, $months),
            ])
            ->add('birth_start', BirthdayType::class, [
                'label' => 'searchcustomer.label.birth_date_start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('birth_end', BirthdayType::class, [
                'label' => 'searchcustomer.label.birth_date_end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('pref', PrefType::class, [
                'label' => 'searchcustomer.label.prefecture',
                'required' => false,
            ])
            ->add('tel', TextType::class, [
                'label' => 'searchcustomer.label.tel',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^[\d-]+$/u",
                        'message' => 'form.type.admin.nottelstyle',
                    ]),
                ],
            ])
            ->add('buy_product_name', TextType::class, [
                'label' => 'searchcustomer.label.purchased_product_name',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('buy_total_start', PriceType::class, [
                'label' => 'searchcustomer.label.purchese_price_start',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_price_len']]),
                ],
            ])
            ->add('buy_total_end', PriceType::class, [
                'label' => 'searchcustomer.label.purchese_price_end',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_price_len']]),
                ],
            ])
            ->add('buy_times_start', IntegerType::class, [
                'label' => 'searchcustomer.label.number_of_purchases_start',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_int_len']]),
                ],
            ])
            ->add('buy_times_end', IntegerType::class, [
                'label' => 'searchcustomer.label.number_of_purchases_end',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_int_len']]),
                ],
            ])
            ->add('create_date_start', DateType::class, [
                'label' => 'searchcustomer.label.create_date_start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('create_date_end', DateType::class, [
                'label' => 'searchcustomer.label.create_date_end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('update_date_start', DateType::class, [
                'label' => 'searchcustomer.label.update_date_start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('update_date_end', DateType::class, [
                'label' => 'searchcustomer.label.update_date_end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('last_buy_start', DateType::class, [
                'label' => 'searchcustomer.label.last_purchase_start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            ->add('last_buy_end', DateType::class, [
                'label' => 'searchcustomer.label.last_purchase_end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])

            // FIXME 未使用
            ->add('company_name', TextType::class, [
                'label' => 'searchcustomer.label.company_name',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('buy_product_code', TextType::class, [
                'label' => 'searchcustomer.label.purchased_product_code',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_search_customer';
    }
}
