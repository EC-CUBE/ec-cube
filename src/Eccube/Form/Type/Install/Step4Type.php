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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

class Step4Type extends AbstractType
{
    public $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('database', 'choice', array(
                'label' => 'DBの種類',
                'choices' => array(
                    'pgsql' => 'PostgreSQL',
                    'mysql' => 'MySQL',
                ),
                'expanded' => false,
                'multiple' => false,
            ))
            ->add('database_host', 'text', array(
                'label' => 'DBサーバのIPアドレス',
            ))
            ->add('database_port', 'text', array(
                'label' => 'DBサーバのポート番号',
                'required' => false,
            ))
            ->add('database_name', 'text', array(
                'label' => 'DB名',
            ))
            ->add('database_user', 'text', array(
                'label' => 'DBユーザ名',
            ))
            ->add('database_password', 'password', array(
                'label' => 'パスワード',
            ))
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();
                try {
                    $dsn = $data['database']
                        . ':host=' . $data['database_host']
                        . ';dbname=' . $data['database_name'];
                    if (!empty($data['port'])) {
                        $dsn .= ';port=' . $data['database_port'];
                    }
                    new \PDO(
                        $dsn,
                        $data['database_user'],
                        $data['database_password']
                    );
                } catch (\PDOException $e) {
                    $form['database_name']->addError(new FormError('データベースに接続できませんでした。'));
                }
            });

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'install_step4';
    }
}
