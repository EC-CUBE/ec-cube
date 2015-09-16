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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KanaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // ひらがなをカタカナに変換する
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function ($event) {
            $data = $event->getData();
            foreach ($data as &$value) {
                $value = mb_convert_kana($value, 'CV', 'utf-8');
            }
            $event->setData($data);
        }, 255);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'inherit_data' => true,
            'lastname_options' => array(
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => "/^[ァ-ヶｦ-ﾟー]+$/u",
                    )),
                ),
            ),
            'firstname_options' => array(
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => "/^[ァ-ヶｦ-ﾟー]+$/u",
                    )),
                ),
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'name';
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'kana';
    }
}
