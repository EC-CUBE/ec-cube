<?php

namespace Eccube\Form\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilder;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;

class CustomerType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name01', 'text', array('constraints' => array(new Assert\NotBlank() )));
		$builder->add('name02', 'text', array('constraints' => array(new Assert\NotBlank() )));
		$builder->add('kana01', 'text', array('constraints' => array(new Assert\NotBlank() )));
		$builder->add('kana02', 'text', array('constraints' => array(new Assert\NotBlank() )));
		$builder->add('email', 'email', array('constraints' => array(new Assert\NotBlank(), new Assert\Email() )));
		$builder->add('password', 'repeated');
	}

	public function getName()
	{
		return 'customer';
	}
}