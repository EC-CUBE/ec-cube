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
        $form = $app['form.factory']
              ->createBuilder('contact')
              ->getForm();
        $form->handleRequest($app['request']);
        if ($app['request']->getMethod() === 'POST' && $form->isValid()) {

            $data = $form->getData();

            switch ($app['request']->get('mode')) {
                case 'confirm' :
                    return $app['twig']->render('Contact/confirm.twig', array(
                        'title' => $this->title,
                        'form' => $form->createView(),
                    ));
                    break;
                case 'complete':
                    // TODO: 後でEventとして実装する
                    $message = $app['mail.message']
                        ->setSubject('[EC-CUBE3] お問い合わせを受け付けました。')
                        ->setFrom(array('sample@example.com'))
                        ->setCc(array('shinichi_takahashi@lockon.co.jp'))
                        ->setTo(array($data['email']))
                        ->setBody($data['contents']);
                    $app['mailer']->send($message);

                    return $app->redirect($app['url_generator']->generate('contact_complete'));
                    break;
            }

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