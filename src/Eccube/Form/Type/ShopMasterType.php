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


namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
            ->add('company_name', 'text', array(
                'label' => '会社名',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['stext_len'],
                    )),
                )
            ))
            ->add('company_kana', 'text', array(
                'label' => '会社名(フリガナ)',
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
            ->add('shop_name', 'text', array(
                'label' => '店名',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $config['stext_len'],
                    )),
                )
            ))
            ->add('shop_kana', 'text', array(
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
            ->add('shop_name_eng', 'text', array(
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
            ->add('zip', 'zip', array(
                'required' => false,
                'zip01_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Regex(array('pattern' => '/^\d{3}$/'))
                    ),
                ),
                'zip02_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Regex(array('pattern' => '/^\d{4}$/'))
                    ),
                ),
            ))
            ->add('address', 'address', array(
                'required' => false,
                'addr01_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'max' => $config['mtext_len'],
                        )),
                    ),
                ),
                'addr02_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'max' => $config['mtext_len'],
                        )),
                    ),
                ),
            ))
            ->add('tel', 'tel', array(
                'required' => false,
            ))
            ->add('fax', 'tel', array(
                'required' => false,
            ))
            ->add('business_hour', 'text', array(
                'label' => '店舗営業時間',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['stext_len'],
                    ))
                )
            ))
            ->add('email01', 'email', array(
                'label' => '商品注文受付メールアドレス',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ),
            ))
            ->add('email02', 'email', array(
                'label' => '問い合わせ受付メールアドレス',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ),
            ))
            ->add('email03', 'email', array(
                'label' => 'メール送信元メールアドレス',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ),
            ))
            ->add('email04', 'email', array(
                'label' => '送信エラー受付メールアドレス',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ),
            ))
            ->add('good_traded', 'textarea', array(
                'label' => '取り扱い商品',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['lltext_len'],
                    )),
                ),
            ))
            ->add('message', 'textarea', array(
                'label' => 'メッセージ',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['lltext_len'],
                    )),
                ),
            ))

            // 送料設定
            ->add('delivery_free_amount', 'money', array(
                'label' => '送料無料条件(金額)',
                'required' => false,
                'currency' => 'JPY',
                'precision' => '0',
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['price_len'],
                    )),
                ),
            ))
            ->add('delivery_free_quantity', 'integer', array(
                'label' => '送料無料条件(数量)',
                'required' => false,
            ))
            ->add('option_product_delivery_fee', 'choice', array(
                'label' => '商品ごとの送料設定を有効にする',
                'choices' => array(
                    '0' => '無効',
                    '1' => '有効',
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('option_delivery_fee', 'choice', array(
                'label' => '配送業者ごとの配送料を加算する',
                'choices' => array(
                    '0' => '無効',
                    '1' => '有効',
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('option_multiple_shipping', 'choice', array(
                'label' => '複数配送を有効にする',
                'choices' => array(
                    '0' => '無効',
                    '1' => '有効',
                ),
                'expanded' => true,
                'multiple' => false,
            ))

            // 会員設定
            ->add('option_customer_activate', 'choice', array(
                'label' => '仮会員を有効にする',
                'choices' => array(
                    '0' => '無効',
                    '1' => '有効',
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('option_mypage_order_status_display', 'choice', array(
                'label' => 'マイページに注文状況を表示する',
                'choices' => array(
                    '0' => '無効',
                    '1' => '有効',
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('option_favorite_product', 'choice', array(
                'label' => 'お気に入り商品機能を利用する',
                'choices' => array(
                    '0' => '無効',
                    '1' => '有効',
                ),
                'expanded' => true,
                'multiple' => false,
            ))

            // 商品設定
            ->add('nostock_hidden', 'choice', array(
                'label' => '在庫切れ商品を非表示にする',
                'choices' => array(
                    '0' => '無効',
                    '1' => '有効',
                ),
                'expanded' => true,
                'multiple' => false,
            ))

            // 地図設定
            ->add('latitude', 'number', array(
                'label' => '緯度',
                'required' => false,
                'precision' => 6,
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => '/^-?([0-8]?[0-9]\.?[0-9]{0,6}|90\.?0{0,6})$/',
                        'message' => 'admin.shop.latitude.invalid'))
                )
            ))
            ->add('longitude', 'number', array(
                'label' => '経度',
                'required' => false,
                'precision' => 6,
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => '/^-?((1?[0-7]?|[0-9]?)[0-9]\.?[0-9]{0,6}|180\.?0{0,6})$/',
                        'message' => 'admin.shop.longitude.invalid'))
                ),
            ))

            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\BaseInfo',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shop_master';
    }
}
