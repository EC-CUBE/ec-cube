<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class MainEditType extends AbstractType
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
            ->add('name', 'text', array(
                'label' => '名称',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('filename', 'text', array(
                'label' => 'URL',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('header_chk', 'checkbox', array(
                'label' => 'ヘッダチェック',
                'required' => false,
            ))
            ->add('footer_chk', 'checkbox', array(
                'label' => 'フッタチェック',
                'required' => true,
            ))
            ->add('tpl_data', 'textarea', array(
                'label' => 'TPLデータ',
                'required' => true,
                'constraints' => array()
            ))
            ->add('author', 'text', array(
                'label' => 'meta タグ:author',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('description', 'text', array(
                'label' => 'meta タグ:description',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('keyword', 'text', array(
                'label' => 'meta タグ:keyword',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('meta_robots', 'text', array(
                'label' => 'meta タグ:robots',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('save', 'submit', array('label' => 'この内容で登録する'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'main_edit';
    }
}
