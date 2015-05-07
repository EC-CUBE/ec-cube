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

class InstallType extends AbstractType
{
    public $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder
            ->add('http_url', 'text', array(
                'label' => '設置URL',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('path', 'text', array(
                'label' => 'ドキュメントルートからのパス',
            ))
            ->add('db_type', 'choice', array(
                'choices' => array(
                    'mysql' => 'MySQL',
                    'pgsql' => 'PostgreSQL',
                ),
                'label' => 'データベース：種類',
            ))
            ->add('db_server', 'text', array(
                'label' => 'データベース：IPアドレス',
            ))
            ->add('db_name', 'text', array(
                'label' => 'データベース：データベース名',
            ))
            ->add('db_user', 'text', array(
                'label' => 'データベース：ユーザー名',
            ))
            ->add('db_pass', 'password', array(
                'label' => 'データベース：パスワード',
            ))
            ->add('install', 'submit');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'install';
    }
}
