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

use Eccube\Annotation\FormType;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\TelType;
use Eccube\Form\Type\ZipType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @FormType
 */
class TradelawType extends AbstractType
{
    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('law_company', TextType::class, array(
                'tradelaw.label.seller' => '販売業者',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_manager', TextType::class, array(
                'label' => 'tradelaw.label.operation_director',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_zip', ZipType::class, array(
                'required' => true,
            ))
            ->add('law_address', AddressType::class, array(
                'label' => 'tradelaw.label.company_address',
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
                'label' => 'tradelaw.label.email',
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
                'label' => 'tradelaw.label.required_fee',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_term02', TextareaType::class, array(
                'label' => 'radelaw.label.how_to_order',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_term03', TextareaType::class, array(
                'label' => 'tradelaw.label.howo_to_pay',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_term04', TextareaType::class, array(
                'label' => 'tradelaw.label.payment_due',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_term05', TextareaType::class, array(
                'label' => 'tradelaw.label.delivery_timing',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_term06', TextareaType::class, array(
                'label' => 'tradelaw.label.return_exchange_policy',
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
