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

use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * @FormType
 */
class Step4Type extends AbstractType
{
    /**
     * @Inject("request_stack")
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $database = array();
        if (extension_loaded('pdo_pgsql')) {
            $database['pdo_pgsql'] = 'PostgreSQL';
        }
        if (extension_loaded('pdo_mysql')) {
            $database['pdo_mysql'] = 'MySQL';
        }
        if (extension_loaded('pdo_sqlite')) {
            $database['pdo_sqlite'] = 'SQLite(開発者用)';
        }

        $builder
            ->add('database', ChoiceType::class, array(
                'label' => 'データベースの種類',
                'choices' => array_flip($database),
                'expanded' => false,
                'multiple' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('database_host', TextType::class, array(
                'label' => 'データベースのホスト名',
                'required' => false,
            ))
            ->add('database_port', TextType::class, array(
                'label' => 'ポート番号',
                'required' => false,
            ))
            ->add('database_name', TextType::class, array(
                'label' => 'データベース名',
                'constraints' => array(
                    new Assert\Callback(array($this, 'validate')),
                ),
            ))
            ->add('database_user', TextType::class, array(
                'label' => 'ユーザ名',
                'constraints' => array(
                    new Assert\Callback(array($this, 'validate')),
                ),
            ))
            ->add('database_password', PasswordType::class, array(
                'label' => 'パスワード',
                'required' => false,
            ))
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();
                try {
                    $config = new \Doctrine\DBAL\Configuration();
                    if ($data['database'] == 'pdo_sqlite') {
                        $connectionParams = array(
                            'driver' => $data['database'],
                            'path' => __DIR__.'/../../../../../app/config/eccube/eccube.db'
                        );

                    } else {
                        $connectionParams = array(
                            'dbname' => $data['database_name'],
                            'user' => $data['database_user'],
                            'password' => $data['database_password'],
                            'host' => $data['database_host'],
                            'driver' => $data['database'],
                            'port' => $data['database_port'],
                        );
                    }
                    // todo MySQL, PostgreSQLのバージョンチェックも欲しい.DBALで接続すればエラーになる？
                    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
                    $conn->connect();
                } catch (\Exception $e) {
                    $form['database']->addError(new FormError('データベースに接続できませんでした。' . $e->getMessage()));
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'install_step4';
    }

    public function validate($data, ExecutionContext $context, $param = null)
    {
        $parameters = $this->requestStack->getCurrentRequest()->get('install_step4');
        if ($parameters['database'] != 'pdo_sqlite'){
            $context->getValidator()->validate($data, array(
                new Assert\NotBlank()
            ));
        }
    }
}
