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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

class Step4Type extends AbstractType
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * Step4Type constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(
        RequestStack $requestStack
    ) {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $database = [];
        if (extension_loaded('pdo_pgsql')) {
            $database['pdo_pgsql'] = 'step4.label.postgresql';
        }
        if (extension_loaded('pdo_mysql')) {
            $database['pdo_mysql'] = 'step4.label.mysql';
        }
        if (extension_loaded('pdo_sqlite')) {
            $database['pdo_sqlite'] = 'step4.label.sqllite';
        }

        $builder
            ->add('database', ChoiceType::class, [
                'label' => trans('step4.label.database'),
                'choices' => array_flip($database),
                'expanded' => false,
                'multiple' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('database_host', TextType::class, [
                'label' => trans('step4.label.database_host_name'),
                'required' => false,
            ])
            ->add('database_port', TextType::class, [
                'label' => trans('step4.label.port_num'),
                'required' => false,
            ])
            ->add('database_name', TextType::class, [
                'label' => trans('step4.label.db_name'),
                'constraints' => [
                    new Assert\Callback([$this, 'validate']),
                ],
            ])
            ->add('database_user', TextType::class, [
                'label' => trans('step4.label.user_name'),
                'constraints' => [
                    new Assert\Callback([$this, 'validate']),
                ],
            ])
            ->add('database_password', PasswordType::class, array(
                'label' => trans('step4.label.pass'),
                'required' => false,
            ))
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();

                if ($data['database'] === 'pdo_sqlite') {
                    // sqliteはdbが作成されてしまうため, 接続チェックは行わない
                    return;
                }
                try {
                    $config = new \Doctrine\DBAL\Configuration();
                    $connectionParams = array(
                        'dbname' => $data['database_name'],
                        'user' => $data['database_user'],
                        'password' => $data['database_password'],
                        'host' => $data['database_host'],
                        'driver' => $data['database'],
                        'port' => $data['database_port'],
                    );
                    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
                    $conn->connect();

                    // todo MySQL, PostgreSQLのバージョンチェックも欲しい.DBALで接続すればエラーになる？
                    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
                    $conn->connect();
                } catch (\Exception $e) {
                    $form['database']->addError(new FormError(trans('setp4.text.error.database_connection') . $e->getMessage()));
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
            $context->getValidator()->validate($data, [
                new Assert\NotBlank()
            ]);
        }
    }
}
