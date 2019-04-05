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


namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RepeatedPasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function __construct($config = array('password_min_len' => 8, 'password_max_len' => '32'))
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'type' => 'text', // type password だと入力欄を空にされてしまうので、widgetで対応
            'required' => true,
            'error_bubbling' => false,
            'invalid_message' => 'form.member.password.invalid',
            'options' => array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => $this->config['password_min_len'],
                        'max' => $this->config['password_max_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[[:graph:][:space:]]+$/i',
                        'message' => 'form.type.graph.invalid',
                    )),
                ),
            ),
            'first_options' => array(
                'attr' => array(
                    'placeholder' => '半角英数字記号'.$this->config['password_min_len'].'～'.$this->config['password_max_len'].'文字',
                ),
            ),
            'second_options' => array(
                'attr' => array(
                    'placeholder' => 'form.member.repeated.confirm',
                ),
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'repeated';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'repeated_password';
    }
}
