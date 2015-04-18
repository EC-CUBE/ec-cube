<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AgreeMentType extends AbstractType
{

    public $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min'=> 0, 'max' => $app['config']['smtext_len']))),
            ))
            ->add('text', 'textarea', array(
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('save', 'submit', array('label' => 'この内容で登録する'));
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'agreement';
    }
}
