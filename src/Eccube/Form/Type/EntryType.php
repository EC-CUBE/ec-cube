<?php

namespace Eccube\Form\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\Extension\Core\Type;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;

class EntryType extends AbstractType
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
            ->add('name', 'name', array(
                'options' => array(
                    'attr' => array(
                        'maxlength' => $app['config']['stext_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
            ))
            ->add('kana', 'name', array(
                'options' => array(
                    'attr' => array(
                        'maxlength' => $app['config']['stext_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
            ))
            ->add('company_name', 'text', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                ),
            ))
            ->add('zip', 'zip', array(
                'required' => false,
            ))
            ->add('address', 'address', array(
                'help' => 'form.contact.address.help',
                'required' => false,
            ))
            ->add('tel', 'tel', array(
                'required' => false,
            ))
            ->add('fax', 'tel', array(
                'required' => false,
            ))
            ->add('email', 'email', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(),
                )
            ))
            ->add('sex', 'sex', array(
                'required' => false,
            ))
            ->add('job', 'job', array(
                'required' => false,
            ))
            ->add('birth', 'birthday', array(
                'required' => false,
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('password', 'repeated')
            ->add('reminder', 'reminder', array(
                'required' => true,
            ))
            ->add('reminder_answer', 'text', array(
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len']
                    ))
                )
            ))
            ->add('mailmaga_flg', 'mailmagazinetype', array(
                'required' => false,
            ))
            ->add('save', 'submit', array('label' => 'この内容で登録する'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'entry';
    }

}
