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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\ZipType;
use Eccube\Form\Type\TelType;


class TradelawType extends AbstractType
{
    protected $config;

    public function __construct ($config)
    {
        $this->config = $config;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('law_company', TextType::class, array(
                'label' => '販売業者',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_manager', TextType::class, array(
                'label' => '運営責任者',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_zip', ZipType::class, array(
                'required' => true,
            ))
            ->add('law_address', AddressType::class, array(
                'label' => '所在地',
                'required' => true,
                'pref_name' => 'law_pref',
                'addr01_name' => 'law_addr01',
                'addr02_name' => 'law_addr02',
            ))
            ->add('law_tel', TelType::class, array(
                'label' => 'TEL',
                'required' => true,
            ))
            ->add('law_fax', TelType::class, array(
                'label' => 'FAX',
                'required' => false,
            ))
            ->add('law_email', EmailType::class, array(
                'label' => 'メールアドレス',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(array('strict' => true)),
                    new Assert\Regex(array(
                        'pattern' => '/^[[:graph:][:space:]]+$/i',
                        'message' => 'form.type.graph.invalid',
                    )),
                ),
            ))
            ->add('law_url', TextType::class, array(
                'label' => 'URL',
                'required' => true,
                'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Url(),
                ),
            ))
            ->add('law_term01', TextareaType::class, array(
                'label' => '商品代金以外の必要料金',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_term02', TextareaType::class, array(
                'label' => '注文方法',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_term03', TextareaType::class, array(
                'label' => '支払方法',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_term04', TextareaType::class, array(
                'label' => '支払期限',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_term05', TextareaType::class, array(
                'label' => '引き渡し時期',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_term06', TextareaType::class, array(
                'label' => '返品・交換について',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ));
    }

    public function getBlockPrefix()
    {
        return 'tradelaw';
    }
}
