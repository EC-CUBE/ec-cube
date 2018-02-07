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
use Eccube\Form\EventListener\ConvertKanaListener;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\PriceType;
use Eccube\Form\Type\TelType;
use Eccube\Form\Type\ZipType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
 *
 * @package Eccube\Form\Type\Admin
 */
class ShopMasterType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * ShopMasterType constructor.
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
                'label' => '会社名',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ]
            ])
            ->add('shop_name', TextType::class, [
                'label' => '店名',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ]
            ])
            ->add('shop_name_eng', TextType::class, [
                'label' => '店名(英語表記)',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_mtext_len'],
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[[:graph:][:space:]]+$/i'
                    ]),
                ]
            ])
            ->add('zip', ZipType::class, [
                'required' => false,
            ])
            ->add('address', AddressType::class, [
                'required' => false,
            ])
            ->add('tel', TelType::class, [
                'required' => false,
            ])
            ->add('fax', TelType::class, [
                'required' => false,
            ])
            ->add('business_hour', TextType::class, [
                'label' => '店舗営業時間',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ])
                ]
            ])
            ->add('email01', EmailType::class, [
                'label' => '送信元メールアドレス(From)',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(['strict' => true]),
                ],
            ])
            ->add('email02', EmailType::class, [
                'label' => '問い合わせ受付メールアドレス(From, ReplyTo)',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(['strict' => true]),
                ],
            ])
            ->add('email03', EmailType::class, [
                'label' => '返信受付メールアドレス(ReplyTo)',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(['strict' => true]),
                ],
            ])
            ->add('email04', EmailType::class, [
                'label' => '送信エラー受付メールアドレス(ReturnPath)',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(['strict' => true]),
                ],
            ])
            ->add('good_traded', TextareaType::class, [
                'label' => '取り扱い商品',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_lltext_len'],
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'メッセージ',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_lltext_len'],
                    ]),
                ],
            ])

            // 送料設定
            ->add('delivery_free_amount', PriceType::class, [
                'label' => '送料無料条件(金額)',
                'required' => false,
            ])
            ->add('delivery_free_quantity', IntegerType::class, [
                'label' => '送料無料条件(数量)',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid'
                    ]),
                ],
            ])
            ->add('option_product_delivery_fee', ChoiceType::class, [
                'label' => '商品ごとの送料設定を有効にする',
                'choices' => array_flip([
                    '0' => '無効',
                    '1' => '有効',
                ]),
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('option_multiple_shipping', ChoiceType::class, [
                'label' => '複数配送を有効にする',
                'choices' => array_flip([
                    '0' => '無効',
                    '1' => '有効',
                ]),
                'expanded' => true,
                'multiple' => false,
            ])

            // 会員設定
            ->add('option_customer_activate', ChoiceType::class, [
                'label' => '仮会員を有効にする',
                'choices' => array_flip([
                    '0' => '無効',
                    '1' => '有効',
                ]),
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('option_mypage_order_status_display', ChoiceType::class, [
                'label' => 'マイページに注文状況を表示する',
                'choices' => array_flip([
                    '0' => '無効',
                    '1' => '有効',
                ]),
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('option_remember_me', ChoiceType::class, [
                'label' => '自動ログイン機能を有効にする',
                'choices' => array_flip([
                    '0' => '無効',
                    '1' => '有効',
                ]),
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('option_favorite_product', ChoiceType::class, [
                'label' => 'お気に入り商品機能を利用する',
                'choices' => array_flip([
                    '0' => '無効',
                    '1' => '有効',
                ]),
                'expanded' => true,
                'multiple' => false,
            ])

            // 商品設定
            ->add('option_nostock_hidden', ChoiceType::class, [
                'label' => '在庫切れ商品を非表示にする',
                'choices' => array_flip([
                    '0' => '無効',
                    '1' => '有効',
                ]),
                'expanded' => true,
                'multiple' => false,
            ])
            // ポイント設定
            ->add('option_point', ChoiceType::class, [
                'label' => 'ポイント機能を利用する',
                'choices' => array_flip([
                    '0' => '無効',
                    '1' => '有効',
                ]),
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('basic_point_rate', NumberType::class, [
                'required' => false,
                'label' => 'ポイント付与率',
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid'
                    ]),
                    new Assert\Range([
                        'min' => 1,
                        'max' => 100,
                    ]),
                ],
            ])
            ->add('point_conversion_rate', NumberType::class, [
                'required' => false,
                'label' => 'ポイント換算レート',
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid'
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
                    'label' => '会社名(フリガナ)',
                    'required' => false,
                    'constraints' => [
                        new Assert\Regex([
                            'pattern' => "/^[ァ-ヶｦ-ﾟー]+$/u",
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
                    'label' => '店名(フリガナ)',
                    'required' => false,
                    'constraints' => [
                        new Assert\Length([
                            'max' => $this->eccubeConfig['eccube_stext_len'],
                        ]),
                        new Assert\Regex([
                            'pattern' => "/^[ァ-ヶｦ-ﾟー]+$/u",
                        ]),
                    ]
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
