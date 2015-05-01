<?php

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
