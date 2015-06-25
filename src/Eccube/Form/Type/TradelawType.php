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

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\Extension\Core\Type;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;

class TradelawType extends AbstractType
{
    public $app;

    public function __construct (\Silex\Application $app)
    {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('law_company', 'text', array(
                'label' => '販売業者',
                'required' => true
            ))
            ->add('law_manager', 'text', array(
                'label' => '運営責任者',
                'required' => true
            ))
            ->add('law_zip', 'zip', array(
                'label' => '郵便番号',
                'zip01_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('min' => 3, 'max' => 3))
                    ),
                ),
                'zip02_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('min' => 4, 'max' => 4))
                    ),
                ),
            ))
            ->add('law_address', 'address', array(
                'label' => '所在地',
                'options' => array(
                    'attr' => array(
                        'maxlength' => $this->app['config']['stext_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
                'pref_name' => 'law_pref',
                'addr01_name' => 'law_addr01',
                'addr02_name' => 'law_addr02',
            ))
            ->add('law_tel', 'tel', array(
                'label' => 'TEL',
                'tel01_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('min' => 2, 'max' => 3)),
                        new Assert\Regex(array('pattern' => '/\A\d+\z/')),
                    ),
                ),
                'tel02_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('min' => 2, 'max' => 4)),
                        new Assert\Regex(array('pattern' => '/\A\d+\z/')),
                    ),
                ),
                'tel03_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('min' => 2, 'max' => 4)),
                        new Assert\Regex(array('pattern' => '/\A\d+\z/')),
                    ),
                ),
                'tel01_name' => 'law_tel01',
                'tel02_name' => 'law_tel02',
                'tel03_name' => 'law_tel03',
            ))
            ->add('law_fax', 'fax', array(
                'label' => 'FAX',
                'required' => false,
                'fax01_name' => 'law_fax01',
                'fax02_name' => 'law_fax02',
                'fax03_name' => 'law_fax03',
            ))
            ->add('law_email', 'email', array(
                'label' => 'メールアドレス',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(),
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
                'required' => true
            ))
            ->add('law_term02', 'textarea', array(
                'label' => '注文方法',
                'required' => true
            ))
            ->add('law_term03', 'textarea', array(
                'label' => '支払方法',
                'required' => true
            ))
            ->add('law_term04', 'textarea', array(
                'label' => '支払期限',
                'required' => true
            ))
            ->add('law_term05', 'textarea', array(
                'label' => '引き渡し時期',
                'required' => true
            ))
            ->add('law_term06', 'textarea', array(
                'label' => '返品・交換について',
                'required' => true
            ))
            ->add('law_term07', 'textarea', array(
                'label' => 'その他１',
            ))
            ->add('law_term08', 'textarea', array(
                'label' => 'その他２',
            ))
            ->add('law_term09', 'textarea', array(
                'label' => 'その他３',
            ))
            ->add('law_term10', 'textarea', array(
                'label' => 'その他４',
            ))
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    public function getName()
    {
        return 'tradelaw';
    }

}
