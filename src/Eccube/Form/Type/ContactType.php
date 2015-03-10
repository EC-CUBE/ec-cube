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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;
        $builder->add('name01', 'text', array('constraints' => array(new Assert\NotBlank() )));
        $builder->add('name02', 'text', array('constraints' => array(new Assert\NotBlank() )));
        $builder->add('kana01', 'text', array('constraints' => array(new Assert\NotBlank() )));
        $builder->add('kana02', 'text', array('constraints' => array(new Assert\NotBlank() )));
        $builder->add('zip01', 'text', array('constraints' => array(
            new Assert\NotBlank(),
            new Assert\Length(array('min' => 3, 'max' => 3))
        )));
        $builder->add('zip02', 'text', array('constraints' => array(
            new Assert\NotBlank(),
            new Assert\Length(array('min' => 4, 'max' => 4))
        )));
        $builder->add('pref', 'pref', array(
            'constraints' => array(new Assert\NotBlank()),
        ));
        $builder->add('addr01', 'text', array('constraints' => array(new Assert\NotBlank() )));
        $builder->add('addr02', 'text', array('constraints' => array(new Assert\NotBlank() )));
        $builder->add('tel01', 'text', array('constraints' => array(
            new Assert\NotBlank(),
            new Assert\Length(array('min' => 2, 'max' => 3))
        )));
        $builder->add('tel02', 'text', array('constraints' => array(
            new Assert\NotBlank(),
            new Assert\Length(array('min' => 2, 'max' => 4))
        )));
        $builder->add('tel03', 'text', array('constraints' => array(
            new Assert\NotBlank(),
            new Assert\Length(array('min' => 2, 'max' => 4))
        )));
        $builder->add('email', 'email', array('constraints' => array(new Assert\NotBlank(), new Assert\Email() )));
        $builder->add('contents', 'textarea', array('constraints' => array(new Assert\NotBlank() )));
    }

    public function getName()
    {
        return 'contact';
    }
}
