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


namespace Eccube\Form\Type\Install;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\Extension\Core\Type;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;

class Step3Type extends AbstractType
{
    public $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shop_name', 'text', array(
                'label' => 'あなたの店名',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('email', 'email', array(
                'label' => 'メールアドレス（受注メールなどの宛先になります）',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ),
            ))
            ->add('login_id', 'text', array(
                'label' => '管理画面ログインID（半角英数字4～50文字）',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => 4,
                        'max' => 50,
                    )),
                ),
            ))
            ->add('login_pass', 'password', array(
                'label' => '管理画面パスワード（半角英数字4～50文字）',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => 4,
                        'max' => 50,
                    )),
                ),
            ))
            ->add('admin_dir', 'text', array(
                'label' => '管理画面のディレクトリ名（半角英数字4～50文字）',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => 4,
                        'max' => 50,
                    )),
                    new Assert\Regex(array('pattern' => '/\A\w+\z/')),
                ),
            ))
            ->add('admin_force_ssl', 'checkbox', array(
                'label' => 'サイトへのアクセスを、SSL（https）経由に制限します',
                'required' => false,
            ))
            ->add('admin_allow_hosts', 'textarea', array(
                'label' => '管理画面へのアクセスを、以下のIPに制限します',
                'help' => '複数入力する場合は、IPとIPの間に改行をいれてください',
                'required' => false,
            ))
            ->add('mail_backend', 'choice', array(
                'label' => 'メーラーバックエンド',
                'choices' => array(
                    'mail' => 'mail（PHPの組み込み関数 mail() を使用してメールを送信）',
                    'smtp' => 'SMTP（SMTPサーバに直接接続してメールを送信）',
                    'sendmail' => 'sendmail（sendmailプログラムによりメールを送信）',
                ),
                'expanded' => true,
                'multiple' => false,
                'data' => 'mail',
            ))
            ->add('smtp_host', 'text', array(
                'label' => 'SMTPホスト',
                'help' => 'メーラーバックエンドがSMTPの場合のみ指定',
                'required' => false,
            ))
            ->add('smtp_port', 'text', array(
                'label' => 'SMTPポート',
                'help' => 'メーラーバックエンドがSMTPの場合のみ指定',
                'required' => false,
            ))
            ->add('smtp_username', 'text', array(
                'label' => 'SMTPユーザー',
                'help' => 'メーラーバックエンドがSMTPかつSMTP-AUTH使用時のみ指定',
                'required' => false,
            ))
            ->add('smtp_password', 'password', array(
                'label' => 'SMTPパスワード',
                'help' => 'メーラーバックエンドがSMTPかつSMTP-AUTH使用時のみ指定',
                'required' => false,
            ))
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'install_step3';
    }
}
