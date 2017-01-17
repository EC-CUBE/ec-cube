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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

// deprecated 3.1で削除予定
class OrderMailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('template', 'mail_template', array(
                'label' => 'テンプレート',
                'required' => true,
                'mapped' => false,
            ))
            ->add('subject', TextType::class, array(
                'label' => 'タイトル',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('header', TextareaType::class, array(
                'label' => 'ヘッダー',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('footer', TextareaType::class, array(
                'label' => 'フッター',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('footer', TextareaType::class, array(
                'label' => 'フッター',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'order_mail';
    }
}
