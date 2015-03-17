<?php

namespace Eccube\Form\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilder;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;
use \Symfony\Component\Validator\ExecutionContextInterface;

class CustomerType extends AbstractType
{
	public $app;

	public function __construct (\Eccube\Application $app)
	{
		$this->app = $app;
	}

	public function getName()
	{
		return 'customer';
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
			->add('email', 'email', array('constraints' => array(
				new Assert\NotBlank(),
				new Assert\Email(),
				new Assert\Callback(function($email, ExecutionContextInterface $context) use ($app) {
						$customers = $app['orm.em']
							->getRepository('Eccube\\Entity\\Customer')
							->findBy(array(
							       'email' => $email,
							       'del_flg' => '0',
							)
						);
						if (count($customers) > 0) {
				            $context->addViolation('入力されたメールアドレスは既に使用されています。');
						}
	        		}),
			 )))
			->add('password', 'repeated')
		;
	}
}