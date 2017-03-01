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

use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\TelType;
use Eccube\Form\Type\ZipType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ShopMasterType extends AbstractType
{
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $config = $this->config;

        $builder
            ->add('company_name', TextType::class, array(
                'label' => '会社名',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['stext_len'],
                    )),
                )
            ))
            ->add('shop_name', TextType::class, array(
                'label' => '店名',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $config['stext_len'],
                    )),
                )
            ))
            ->add('shop_name_eng', TextType::class, array(
                'label' => '店名(英語表記)',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['mtext_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[[:graph:][:space:]]+$/i'
                    )),
                )
            ))
            ->add('zip', ZipType::class, array(
                'required' => false,
            ))
            ->add('address', AddressType::class, array(
                'required' => false,
            ))
            ->add('tel', TelType::class, array(
                'required' => false,
            ))
            ->add('fax', TelType::class, array(
                'required' => false,
            ))
            ->add('business_hour', TextType::class, array(
                'label' => '店舗営業時間',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['stext_len'],
                    ))
                )
            ))
            ->add('email01', EmailType::class, array(
                'label' => '送信元メールアドレス(From)',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(array('strict' => true)),
                ),
            ))
            ->add('email02', EmailType::class, array(
                'label' => '問い合わせ受付メールアドレス(From, ReplyTo)',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(array('strict' => true)),
                ),
            ))
            ->add('email03', EmailType::class, array(
                'label' => '返信受付メールアドレス(ReplyTo)',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(array('strict' => true)),
                ),
            ))
            ->add('email04', EmailType::class, array(
                'label' => '送信エラー受付メールアドレス(ReturnPath)',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(array('strict' => true)),
                ),
            ))
            ->add('good_traded', TextareaType::class, array(
                'label' => '取り扱い商品',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['lltext_len'],
                    )),
                ),
            ))
            ->add('message', TextareaType::class, array(
                'label' => 'メッセージ',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['lltext_len'],
                    )),
                ),
            ))

            // 送料設定
            ->add('delivery_free_amount', MoneyType::class, array(
                'label' => '送料無料条件(金額)',
                'required' => false,
                'currency' => 'JPY',
                'scale' => 0,
                'grouping' => true,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['price_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid'
                    )),
                ),
            ))
            ->add('delivery_free_quantity', IntegerType::class, array(
                'label' => '送料無料条件(数量)',
                'required' => false,
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid'
                    )),
                ),
            ))
            ->add('option_product_delivery_fee', ChoiceType::class, array(
                'label' => '商品ごとの送料設定を有効にする',
                'choices' => array_flip(array(
                    '0' => '無効',
                    '1' => '有効',
                )),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('option_multiple_shipping', ChoiceType::class, array(
                'label' => '複数配送を有効にする',
                'choices' => array_flip(array(
                    '0' => '無効',
                    '1' => '有効',
                )),
                'expanded' => true,
                'multiple' => false,
            ))

            // 会員設定
            ->add('option_customer_activate', ChoiceType::class, array(
                'label' => '仮会員を有効にする',
                'choices' => array_flip(array(
                    '0' => '無効',
                    '1' => '有効',
                )),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('option_mypage_order_status_display', ChoiceType::class, array(
                'label' => 'マイページに注文状況を表示する',
                'choices' => array_flip(array(
                    '0' => '無効',
                    '1' => '有効',
                )),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('option_remember_me', ChoiceType::class, array(
                'label' => '自動ログイン機能を有効にする',
                'choices' => array_flip(array(
                    '0' => '無効',
                    '1' => '有効',
                )),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('option_favorite_product', ChoiceType::class, array(
                'label' => 'お気に入り商品機能を利用する',
                'choices' => array_flip(array(
                    '0' => '無効',
                    '1' => '有効',
                )),
                'expanded' => true,
                'multiple' => false,
            ))

            // 商品設定
            ->add('nostock_hidden', ChoiceType::class, array(
                'label' => '在庫切れ商品を非表示にする',
                'choices' => array_flip(array(
                    '0' => '無効',
                    '1' => '有効',
                )),
                'expanded' => true,
                'multiple' => false,
            ))

            // 地図設定
            ->add('latitude', NumberType::class, array(
                'label' => '緯度',
                'required' => false,
                'scale' => 6,
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => '/^-?([0-8]?[0-9]\.?[0-9]{0,6}|90\.?0{0,6})$/',
                        'message' => 'admin.shop.latitude.invalid'))
                )
            ))
            ->add('longitude', NumberType::class, array(
                'label' => '経度',
                'required' => false,
                'scale' => 6,
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => '/^-?((1?[0-7]?|[0-9]?)[0-9]\.?[0-9]{0,6}|180\.?0{0,6})$/',
                        'message' => 'admin.shop.longitude.invalid'))
                ),
            ))
        ;

        $builder->add(
            $builder
                ->create('company_kana', TextType::class, array(
                    'label' => '会社名(フリガナ)',
                    'required' => false,
                    'constraints' => array(
                        new Assert\Regex(array(
                            'pattern' => "/^[ァ-ヶｦ-ﾟー]+$/u",
                        )),
                        new Assert\Length(array(
                            'max' => $config['stext_len'],
                        )),
                    ),
                ))
                ->addEventSubscriber(new \Eccube\EventListener\ConvertKanaListener('CV'))
        );

        $builder->add(
            $builder
                ->create('shop_kana', TextType::class, array(
                    'label' => '店名(フリガナ)',
                    'required' => false,
                    'constraints' => array(
                        new Assert\Length(array(
                            'max' => $config['stext_len'],
                        )),
                        new Assert\Regex(array(
                            'pattern' => "/^[ァ-ヶｦ-ﾟー]+$/u",
                        )),
                    )
                ))
                ->addEventSubscriber(new \Eccube\EventListener\ConvertKanaListener('CV'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\BaseInfo',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'shop_master';
    }
}
