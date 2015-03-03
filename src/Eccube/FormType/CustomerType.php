<?php

namespace Eccube\FormType;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilder;
use \Symfony\Component\Form\FormBuilderInterface;

class CustomerType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name01');
		$builder->add('name02');
		$builder->add('kana01');
		$builder->add('kana02');
		$builder->add('email', 'email');
		$builder->add('password', 'repeated');
	}

	public function getName()
	{
		return 'customer';
	}
}