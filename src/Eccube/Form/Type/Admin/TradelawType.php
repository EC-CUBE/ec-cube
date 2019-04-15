<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
            ->add('law_company', 'text', array(
                'label' => '販売業者',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_manager', 'text', array(
                'label' => '運営責任者',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_zip', 'zip', array(
                'required' => true,
            ))
            ->add('law_address', 'address', array(
                'label' => '所在地',
                'required' => true,
                'pref_name' => 'law_pref',
                'addr01_name' => 'law_addr01',
                'addr02_name' => 'law_addr02',
            ))
            ->add('law_tel', 'tel', array(
                'label' => 'TEL',
                'required' => true,
            ))
            ->add('law_fax', 'tel', array(
                'label' => 'FAX',
                'required' => false,
            ))
            ->add('law_email', 'email', array(
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
            ->add('law_url', 'text', array(
                'label' => 'URL',
                'required' => true,
                'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Url(),
                ),
            ))
            ->add('law_term01', 'textarea', array(
                'label' => '商品代金以外の必要料金',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_term02', 'textarea', array(
                'label' => '注文方法',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_term03', 'textarea', array(
                'label' => '支払方法',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_term04', 'textarea', array(
                'label' => '支払期限',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_term05', 'textarea', array(
                'label' => '引き渡し時期',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('law_term06', 'textarea', array(
                'label' => '返品・交換について',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ));
    }

    public function getName()
    {
        return 'tradelaw';
    }
}
