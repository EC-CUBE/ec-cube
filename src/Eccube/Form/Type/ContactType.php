<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
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
            ->add('name', 'name', array(
                'options' => array(
                    'attr' => array(
                        'maxlength' => $this->config['stext_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
            ))
            ->add('kana', 'name', array(
                'options' => array(
                    'attr' => array(
                        'maxlength' => $this->config['stext_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
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
            ->add('email', 'email', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ),
            ))
            ->add('contents', 'textarea', array(
                'help' => 'form.contact.contents.help',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'contact';
    }
}
