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

use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * @FormType
 */
class Step3Type extends AbstractType
{
    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject("validator")
     * @var RecursiveValidator
     */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shop_name', TextType::class, array(
                'label' => 'あなたの店名',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $this->appConfig['stext_len'],
                    )),
                ),
            ))
            ->add('email', EmailType::class, array(
                'label' => 'メールアドレス（受注メールなどの宛先になります）',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(array('strict' => true)),
                ),
            ))
            ->add('login_id', TextType::class, array(
                'label' => '管理画面ログインID（半角英数字'.$this->appConfig['id_min_len'].'～'.$this->appConfig['id_max_len'].'文字）',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => $this->appConfig['id_min_len'],
                        'max' => $this->appConfig['id_max_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[[:graph:][:space:]]+$/i',
                        'message' => 'form.type.graph.invalid',
                    )),
                ),
            ))
            ->add('login_pass', PasswordType::class, array(
                'label' => '管理画面パスワード（半角英数字'.$this->appConfig['password_min_len'].'～'.$this->appConfig['password_max_len'].'文字）',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => $this->appConfig['password_min_len'],
                        'max' => $this->appConfig['password_max_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[[:graph:][:space:]]+$/i',
                        'message' => 'form.type.graph.invalid',
                    )),
                ),
            ))
            ->add('admin_dir', TextType::class, array(
                'label' => '管理画面のディレクトリ名（半角英数字'.$this->appConfig['id_min_len'].'～'.$this->appConfig['id_max_len'].'文字）',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => $this->appConfig['id_min_len'],
                        'max' => $this->appConfig['id_max_len'],
                    )),
                    new Assert\Regex(array('pattern' => '/\A\w+\z/')),
                ),
            ))
            ->add('admin_force_ssl', CheckboxType::class, array(
                'label' => 'サイトへのアクセスを、SSL（https）経由に制限します',
                'required' => false,
            ))
            ->add('admin_allow_hosts', TextareaType::class, array(
                'label' => '管理画面へのアクセスを、以下のIPに制限します',
                'help' => '複数入力する場合は、IPとIPの間に改行をいれてください',
                'required' => false,
            ))
            ->add('trusted_proxies_connection_only', CheckboxType::class, array(
                'label' => 'サイトが信頼されたロードバランサー、プロキシサーバからのみアクセスされる',
                'required' => false,
            ))
            ->add('trusted_proxies', TextareaType::class, array(
                'label' => 'ロードバランサー、プロキシサーバのIP',
                'help' => '複数入力する場合は、IPとIPの間に改行をいれてください（X-Forwarded-Proto、X-Forwarded-Host、X-Forwarded-Portヘッダーに対応してる必要があります）',
                'required' => false,
            ))
            ->add('mail_backend', ChoiceType::class, array(
                'label' => 'メーラーバックエンド',
                'choices' => array(
                    'mail（PHPの組み込み関数 mail() を使用してメールを送信）' => 'mail',
                    'SMTP（SMTPサーバに直接接続してメールを送信）' => 'smtp',
                    'sendmail（sendmailプログラムによりメールを送信）' => 'sendmail',
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('smtp_host', TextType::class, array(
                'label' => 'SMTPホスト',
                'help' => 'メーラーバックエンドがSMTPの場合のみ指定',
                'required' => false,
            ))
            ->add('smtp_port', TextType::class, array(
                'label' => 'SMTPポート',
                'help' => 'メーラーバックエンドがSMTPの場合のみ指定',
                'required' => false,
            ))
            ->add('smtp_username', TextType::class, array(
                'label' => 'SMTPユーザー',
                'help' => 'メーラーバックエンドがSMTPかつSMTP-AUTH使用時のみ指定',
                'required' => false,
            ))
            ->add('smtp_password', PasswordType::class, array(
                'label' => 'SMTPパスワード',
                'help' => 'メーラーバックエンドがSMTPかつSMTP-AUTH使用時のみ指定',
                'required' => false,
            ))
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();

                $ips = preg_split("/\R/", $data['admin_allow_hosts'], null, PREG_SPLIT_NO_EMPTY);

                foreach($ips as $ip) {
                    $errors = $this->validator->validate($ip, array(
                            new Assert\Ip(),
                        )
                    );
                    if ($errors->count() != 0) {
                        $form['admin_allow_hosts']->addError(new FormError($ip . 'はIPv4アドレスではありません。'));
                    }
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'install_step3';
    }
}
