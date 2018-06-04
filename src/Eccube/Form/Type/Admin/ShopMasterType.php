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
use Eccube\Form\EventListener\ConvertKanaListener;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\PriceType;
use Eccube\Form\Type\TelType;
use Eccube\Form\Type\ToggleSwitchType;
use Eccube\Form\Type\ZipType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ShopMasterType
 */
class ShopMasterType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * ShopMasterType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company_name', TextType::class, [
                'label' => 'common.label.company_name',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('shop_name', TextType::class, [
                'label' => 'common.label.shop_name',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('shop_name_eng', TextType::class, [
                'label' => 'common.label.shop_name_en',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_mtext_len'],
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[[:graph:][:space:]]+$/i',
                    ]),
                ],
            ])
            ->add('zip', ZipType::class, [
                'required' => false,
            ])
            ->add('address', AddressType::class, [
                'label' => 'common.label.address',
                'required' => false,
            ])
            ->add('tel', TelType::class, [
                'label' => 'common.label.phone_number',
                'required' => false,
            ])
            ->add('fax', TelType::class, [
                'label' => 'common.label.fax_number',
                'required' => false,
            ])
            ->add('business_hour', TextType::class, [
                'label' => 'common.label.business_hour',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('email01', EmailType::class, [
                'label' => 'common.label.email_from',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(['strict' => true]),
                ],
            ])
            ->add('email02', EmailType::class, [
                'label' => 'common.label.email_for_inquiries',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(['strict' => true]),
                ],
            ])
            ->add('email03', EmailType::class, [
                'label' => 'common.label.email_reply_to',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(['strict' => true]),
                ],
            ])
            ->add('email04', EmailType::class, [
                'label' => 'common.label.email_return_path',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(['strict' => true]),
                ],
            ])
            ->add('good_traded', TextareaType::class, [
                'label' => 'common.label.good_traded',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_lltext_len'],
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'shopmaster.label.message', // 削除予定
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_lltext_len'],
                    ]),
                ],
            ])
            // 送料設定
            ->add('delivery_free_amount', PriceType::class, [
                'label' => 'common.label.option_delivery_fee_free_amount',
                'required' => false,
            ])

            ->add('delivery_free_quantity', IntegerType::class, [
                'label' => 'common.label.option_delivery_free_quantity',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid',
                    ]),
                ],
            ])
            ->add('option_product_delivery_fee', ToggleSwitchType::class)
            // 会員設定
            ->add('option_customer_activate', ToggleSwitchType::class)
            // マイページに注文状況を表示する
            ->add('option_mypage_order_status_display', ToggleSwitchType::class)
            // 自動ログイン
            ->add('option_remember_me', ToggleSwitchType::class)
            // お気に入り商品設定
            ->add('option_favorite_product', ToggleSwitchType::class)
            // 在庫切れ商品を非表示にする
            ->add('option_nostock_hidden', ToggleSwitchType::class, [
                'label_off' => 'common.label.display',
                'label_on' => 'common.label.hide',
            ])
            // 個別税率設定
            ->add('option_product_tax_rule', ToggleSwitchType::class)
            // ポイント設定
            ->add('option_point', ToggleSwitchType::class)
            ->add('basic_point_rate', NumberType::class, [
                'required' => false,
                'label' => 'common.label.basic_point_rate', // TODO 未翻訳
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid',
                    ]),
                    new Assert\Range([
                        'min' => 1,
                        'max' => 100,
                    ]),
                ],
            ])
            ->add('point_conversion_rate', NumberType::class, [
                'required' => false,
                'label' => 'common.label.point_conversion_rate', // TODO 未翻訳
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid',
                    ]),
                    new Assert\Range([
                        'min' => 1,
                        'max' => 100,
                    ]),
                ],
            ])
        ;

        $builder->add(
            $builder
                ->create('company_kana', TextType::class, [
                    'label' => 'common.label.company_name_kana',
                    'required' => false,
                    'constraints' => [
                        new Assert\Regex([
                            'pattern' => '/^[ァ-ヶｦ-ﾟー]+$/u',
                        ]),
                        new Assert\Length([
                            'max' => $this->eccubeConfig['eccube_stext_len'],
                        ]),
                    ],
                ])
                ->addEventSubscriber(new ConvertKanaListener('CV'))
        );

        $builder->add(
            $builder
                ->create('shop_kana', TextType::class, [
                    'label' => 'common.label.shop_name_kana',
                    'required' => false,
                    'constraints' => [
                        new Assert\Length([
                            'max' => $this->eccubeConfig['eccube_stext_len'],
                        ]),
                        new Assert\Regex([
                            'pattern' => '/^[ァ-ヶｦ-ﾟー]+$/u',
                        ]),
                    ],
                ])
                ->addEventSubscriber(new ConvertKanaListener('CV'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \Eccube\Entity\BaseInfo::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'shop_master';
    }
}
