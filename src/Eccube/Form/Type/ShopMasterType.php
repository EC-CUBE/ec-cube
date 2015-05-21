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
    public $app;

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
            ->add('message', 'textarea', array(
                'label' => 'メッセージ',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['lltext_len'],
                    )),
                )
            ))
            ->add('free_rule', 'money', array(
                'label' => '送料無料条件',
                'required' => false,
                'currency' => 'JPY',
                'precision' => '0',
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['price_len'],
                    )),
                )
            ))
            ->add('latitude', 'number', array(
                'label' => '緯度',
                'required' => false,
                'precision' => 6,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['stext_len'],
                    )),
                )
            ))
            ->add('longitude', 'number', array(
                'label' => '経度',
                'required' => false,
                'precision' => 6,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['stext_len'],
                    )),
                )
            ))
            ->add('downloadable_days_unlimited', 'checkbox', array(
                'label' => 'ダウンロード無制限',
                'required' => false,
            ))
            ->add('downloadable_days', 'integer', array(
                'label' => 'ダウンロード可能日数',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['download_days_len'],
                    )),
                )
            ))
            ->add('deliv_free_amount', 'integer', array(
                'label' => '送料無料条件(数量)',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('use_multiple_shipping', 'choice', array(
                'label' => '複数配送を有効にする',
                'choices' => array(
                    '0' => '無効',
                    '1' => '有効',
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('forgot_mail', 'choice', array(
                'label' => 'パスワードリマインダ利用時にメールを送信する',
                'choices' => array(
                    '0' => '無効',
                    '1' => '有効',
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('mypage_order_status_disp_flg', 'choice', array(
                'label' => 'マイページに注文状況を表示する',
                'choices' => array(
                    '0' => '無効',
                    '1' => '有効',
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('nostock_hidden', 'choice', array(
                'label' => '在庫切れ商品を非表示にする',
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
            ->add('option_product_deliv_fee', 'choice', array(
                'label' => '商品ごとの送料設定を有効にする',
                'choices' => array(
                    '0' => '無効',
                    '1' => '有効',
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('option_product_tax_rule', 'choice', array(
                'label' => '商品ごとの税率設定',
                'choices' => array(
                    '0' => '無効',
                    '1' => '有効',
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('point_rule', 'choice', array(
                'label' => 'ポイントの計算ルール(1:四捨五入、2:切り捨て、3:切り上げ )',
                'choices' => array(
                    '0' => '無効',
                    '1' => '有効',
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('use_point', 'choice', array(
                'label' => '1ポイント当たりの値段(円)',
                'choices' => array(
                    '0' => '無効',
                    '1' => '有効',
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $downloadable_days_unlimited = $form['downloadable_days_unlimited']->getData();
                $downloadable_days = $form['downloadable_days']->getData();

                if (empty($downloadable_days) && empty($downloadable_days_unlimited)) {
                    $form['downloadable_days']->addError(new FormError('admin.shop.download.invalid'));
                }
            })
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
