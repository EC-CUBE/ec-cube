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
use Symfony\Component\Validator\Constraints as Assert;

class ShopMasterType extends AbstractType
{
    public $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder
            ->add('company_name', 'text', array(
                'label' => '会社名',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('company_kana', 'text', array(
                'label' => '会社名(フリガナ)',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('shop_name', 'text', array(
                'label' => '店名',
                'required' => true,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('shop_kana', 'text', array(
                'label' => '店名(フリガナ)',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('shop_name_eng', 'text', array(
                'label' => '店名(英語表記)',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['mtext_len'],
                    ))
                )
            ))
            ->add('zip', 'zip', array(
                'required' => false,
            ))
            ->add('address', 'address', array(
                'required' => false,
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
                        'max' => $app['config']['mtext_len'],
                    ))
                )
            ))
            ->add('email01', 'email', array(
                'label' => '商品注文受付メールアドレス',
                'required' => false,
            ))
            ->add('email02', 'email', array(
                'label' => '問い合わせ受付メールアドレス',
                'required' => false,
            ))
            ->add('email03', 'email', array(
                'label' => 'メール送信元メールアドレス',
                'required' => false,
            ))
            ->add('email04', 'email', array(
                'label' => '送信エラー受付メールアドレス',
                'required' => false,
            ))
            ->add('good_traded', 'textarea', array(
                'label' => '取扱商品',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['lltext_len'],
                    ))
                )
            ))
            ->add('message', 'textarea', array(
                'label' => 'メッセージ',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['lltext_len'],
                    ))
                )
            ))
            ->add('regular_holiday_ids', 'choice', array(
                'label' => '定休日',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'empty_value' => false,
                'choices' => array(
                    '日', '月', '火', '水', '木', '金', '土',
                ),
            ))
            ->add('free_rule', 'money', array(
                'label' => '送料無料条件',
                'required' => false,
                'currency' => 'JPY',
                'precision' => '0',
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['price_len'],
                    ))
                )
            ))
            ->add('downloadable_days', 'integer', array(
                'label' => 'ダウンロード可能日数',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['download_days_len'],
                    ))
                )
            ))
            ->add('downloadable_days_unlimited', 'checkbox', array(
                'label' => 'ダウンロード無制限',
                'required' => false,
            ))
            ->add('latitude', 'number', array(
                'label' => '緯度',
                'required' => false,
                'precision' => 6,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('longitude', 'number', array(
                'label' => '軽度',
                'required' => false,
                'precision' => 6,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('save', 'submit', array('label' => 'この内容で登録する'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shop_master';
    }
}
