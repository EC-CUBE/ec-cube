<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
            $database['pdo_pgsql'] = 'install.database_pgsql';
        }
        if (extension_loaded('pdo_mysql')) {
            $database['pdo_mysql'] = 'install.database_mysql';
        }
        if (extension_loaded('pdo_sqlite')) {
            $database['pdo_sqlite'] = 'install.database_sqlite';
        }

        $builder
            ->add('database', ChoiceType::class, [
                'label' => trans('install.database_type'),
                'choices' => array_flip($database),
                'expanded' => false,
                'multiple' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('database_host', TextType::class, [
                'label' => trans('install.database_host'),
                'required' => false,
            ])
            ->add('database_port', TextType::class, [
                'label' => trans('install.database_port'),
                'required' => false,
            ])
            ->add('database_name', TextType::class, [
                'label' => trans('install.database_name'),
                'constraints' => [
                    new Assert\Callback([$this, 'validate']),
                ],
            ])
            ->add('database_user', TextType::class, [
                'label' => trans('install.database_user'),
                'constraints' => [
                    new Assert\Callback([$this, 'validate']),
                ],
            ])
            ->add('database_password', PasswordType::class, [
                'label' => trans('install.database_password'),
                'required' => false,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();

                if ($data['database'] === 'pdo_sqlite') {
                    // sqliteはdbが作成されてしまうため, 接続チェックは行わない
                    return;
                }
                try {
                    $config = new \Doctrine\DBAL\Configuration();
                    $connectionParams = [
                        'dbname' => $data['database_name'],
                        'user' => $data['database_user'],
                        'password' => $data['database_password'],
                        'host' => $data['database_host'],
                        'driver' => $data['database'],
                        'port' => $data['database_port'],
                    ];
                    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
                    $conn->connect();

                    // todo MySQL, PostgreSQLのバージョンチェックも欲しい.DBALで接続すればエラーになる？
                    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
                    $conn->connect();
                } catch (\Exception $e) {
                    $form['database']->addError(new FormError(trans('install.database_connection_error').$e->getMessage()));
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
        if ($parameters['database'] != 'pdo_sqlite') {
            $context->getValidator()->validate($data, [
                new Assert\NotBlank(),
            ]);
        }
    }
}
