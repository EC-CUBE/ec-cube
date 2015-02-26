<?php

namespace Eccube\Controller;

use Eccube\Application;
use Symfony\Component\Validator\Constraints as Assert;

class ContactController
{
	private $title;

	public function __construct()
	{
		$this->title = 'お問い合わせ';
	}

	public function Index(Application $app)
	{
            $form = $app['form.factory']->createBuilder()
            ->add('name01', 'text', array(
            	'constraints' => array(new Assert\NotBlank())
            ))
            ->add('name02', 'text', array(
            	'constraints' => array(new Assert\NotBlank())
            ))
            ->add('kana01', 'text', array(
            	'constraints' => array(new Assert\NotBlank())
            ))
            ->add('kana02', 'text', array(
            	'constraints' => array(new Assert\NotBlank())
            ))
            ->add('zip01', 'text', array(
            	'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3)))
            ))
            ->add('zip02', 'text', array(
            	'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 4)))
            ))
            ->add('pref')
            ->add('addr01', 'text', array(
            	'constraints' => array(new Assert\NotBlank())
            ))
            ->add('addr02', 'text', array(
            	'constraints' => array(new Assert\NotBlank())
            ))
            ->add('tel01', 'text', array(
            	'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 2)))
            ))
            ->add('tel02', 'text', array(
            	'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3)))
            ))
            ->add('tel03', 'text', array(
            	'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3)))
            ))
            ->add('email', 'email', array(
            	'constraints' => array(new Assert\NotBlank(), new Assert\Email())
            ))
            ->add('email02', 'email', array(
            	'constraints' => array(new Assert\NotBlank(), new Assert\Email())
            ))
            ->add('contents', 'textarea', array(
            	'constraints' => array(new Assert\NotBlank())
            ))
            ->getForm();

            $form->handleRequest($app['request']);

            if ($form->isValid()) {
                  $data = $form->getData();
                  $message = $app['mail.message']
                        ->setSubject('[EC-CUBE3] お問い合わせを受け付けました。')
                        ->setFrom(array('sample@example.com'))
                        ->setCc(array('shinichi_takahashi@lockon.co.jp'))
                        ->setTo(array($data['email']))
                        ->setBody(array($data['contents']));
                  $app['mailer']->send($message);

                  return $app->redirect('complete.php');
            }

		return $app['twig']->render('Contact/index.twig', array(
			'title' => $this->title,
			'form' => $form->createView(),
		));
	}

	public function Complete(Application $app)
	{
		return $app['twig']->render('Contact/complete.twig', array(
			'title' => $this->title,
		));
	}
}