<?php

namespace Eccube\Form\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilder;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;
use \Symfony\Component\Validator\ExecutionContextInterface;

class ContactType extends AbstractType
{

    public $app;

    public function __construct (\Eccube\Application $app)
    {
        $this->app = $app;
    }

    public function getName()
    {
        return 'contact';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder
            ->add('name01', 'text', array('constraints' => array(
                new Assert\NotBlank(),
            )))
            ->add('name02', 'text', array('constraints' => array(
                new Assert\NotBlank(),
            )))
            ->add('kana01', 'text', array('constraints' => array(
                new Assert\NotBlank(),
            )))
            ->add('kana02', 'text', array('constraints' => array(
                new Assert\NotBlank(),
            )))
            ->add('zip01', 'text', array('constraints' => array(
                new Assert\NotBlank(),
                new Assert\Length(array('min' => 3, 'max' => 3)),
            )))
            ->add('zip02', 'text', array('constraints' => array(
                new Assert\NotBlank(),
                new Assert\Length(array('min' => 4, 'max' => 4)),
            )))
            ->add('pref', 'pref', array('constraints' => array(
                new Assert\NotBlank()
            )))
            ->add('addr01', 'text', array('constraints' => array(
                new Assert\NotBlank(),
            )))
            ->add('addr02', 'text', array('constraints' => array(
                new Assert\NotBlank(),
            )))
            ->add('tel01', 'text', array('constraints' => array(
                new Assert\NotBlank(),
                new Assert\Length(array('min' => 2, 'max' => 3)),
            )))
            ->add('tel02', 'text', array('constraints' => array(
                new Assert\NotBlank(),
                new Assert\Length(array('min' => 2, 'max' => 4))
            )))
            ->add('tel03', 'text', array('constraints' => array(
                new Assert\NotBlank(),
                new Assert\Length(array('min' => 2, 'max' => 4))
            )))
            ->add('email', 'email', array('constraints' => array(
                new Assert\NotBlank(),
                new Assert\Email(),
            )))
            ->add('contents', 'textarea', array('constraints' => array(
                new Assert\NotBlank(),
            )))
        ;
    }
}
