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

use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\Master\CustomerStatusType;
use Eccube\Form\Type\Master\JobType;
use Eccube\Form\Type\Master\SexType;
use Eccube\Form\Type\NameType;
use Eccube\Form\Type\TelType;
use Eccube\Form\Type\ZipType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

// deprecated 3.1で削除予定
/**
 * @FormType
 */
class CustomerType extends AbstractType
{
    /**
     * @Inject("config")
     * @var array
     */
    protected $eccubeConfig;

    /**
     * @var \Eccube\Application $app
     * @Inject(Application::class)
     */
    protected $app;

    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder
            ->add('name', NameType::class, array(
                'options' => array(
                    'attr' => array(
                        'maxlength' => $this->eccubeConfig['eccube_stext_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('max' => $this->eccubeConfig['eccube_stext_len'])),
                    ),
                ),
            ))
            ->add('kana', NameType::class, array(
                'options' => array(
                    'attr' => array(
                        'maxlength' => $this->eccubeConfig['eccube_stext_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('max' => $this->eccubeConfig['eccube_stext_len'])),
                        new Assert\Regex(array(
                            'pattern' => "/^[ァ-ヶｦ-ﾟー]+$/u",
                        )),
                    ),
                ),
            ))
            ->add('company_name', TextType::class, array(
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ))
                ),
            ))
            ->add('zip', ZipType::class, array(
                'required' => false,
            ))
            ->add('address', AddressType::class, array(
                'help' => 'form.contact.address.help',
                'required' => false,
            ))
            ->add('tel', TelType::class, array(
                'required' => false,
            ))
            ->add('fax', TelType::class, array(
                'required' => false,
            ))
            ->add('email', EmailType::class, array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(array('strict' => true)),
                )
            ))
            ->add('sex', SexType::class, array(
                'required' => false,
            ))
            ->add('job', JobType::class, array(
                'required' => false,
            ))
            ->add('birth', BirthdayType::class, array(
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['birth_max']),
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
                'constraints' => array(
                    new Assert\LessThanOrEqual(array(
                        'value' => date('Y-m-d'),
                        'message' => 'form.type.select.selectisfuturedate',
                    )),
                ),
            ))
            ->add('password', RepeatedType::class)
            ->add('status', CustomerStatusType::class, array(
                'required' => false,
            ))
            ->add('note', TextareaType::class, array(
                'required' => false,
            ))
            ->add('save', SubmitType::class, array('label' => 'この内容で登録する'));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'customer';
    }
}
