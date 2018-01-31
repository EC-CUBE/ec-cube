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

use Eccube\Common\EccubeConfig;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Step3Type extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(ValidatorInterface $validator, EccubeConfig $eccubeConfig)
    {
        $this->validator = $validator;
        $this->eccubeConfig = $eccubeConfig;
    }

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
                        'max' => $this->eccubeConfig['stext_len'],
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
                'label' => '管理画面ログインID（半角英数字'.$this->eccubeConfig['id_min_len'].'～'.$this->eccubeConfig['id_max_len'].'文字）',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => $this->eccubeConfig['id_min_len'],
                        'max' => $this->eccubeConfig['id_max_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[[:graph:][:space:]]+$/i',
                        'message' => 'form.type.graph.invalid',
                    )),
                ),
            ))
            ->add('login_pass', PasswordType::class, array(
                'label' => '管理画面パスワード（半角英数字'.$this->eccubeConfig['password_min_len'].'～'.$this->eccubeConfig['password_max_len'].'文字）',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => $this->eccubeConfig['password_min_len'],
                        'max' => $this->eccubeConfig['password_max_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[[:graph:][:space:]]+$/i',
                        'message' => 'form.type.graph.invalid',
                    )),
                ),
            ))
            ->add('admin_dir', TextType::class, array(
                'label' => '管理画面のディレクトリ名（半角英数字'.$this->eccubeConfig['id_min_len'].'～'.$this->eccubeConfig['id_max_len'].'文字）',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => $this->eccubeConfig['id_min_len'],
                        'max' => $this->eccubeConfig['id_max_len'],
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
                'required' => false,
            ))
            ->add('smtp_host', TextType::class, array(
                'label' => 'SMTPホスト',
                'required' => false,
            ))
            ->add('smtp_port', TextType::class, array(
                'label' => 'SMTPポート',
                'required' => false,
            ))
            ->add('smtp_username', TextType::class, array(
                'label' => 'SMTPユーザー',
                'required' => false,
            ))
            ->add('smtp_password', PasswordType::class, array(
                'label' => 'SMTPパスワード',
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
