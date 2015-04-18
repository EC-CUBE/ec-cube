<?php

namespace Eccube\Controller\Block;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class LoginHeaderController
{
	function index(Application $app, Request $request)
	{
        $email = $request->cookies->get('email');

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $app['form.factory']
            ->createNamedBuilder('', 'customer_login')
            ->getForm();

        $disableLogout = $this->isDisableLogoutPage($app);

        return $app['view']->render('Block/login_header.twig', array(
            'error' => $app['security.last_error']($request),
            'disableLogout' => $disableLogout,
            'email' => $email,
            'form' => $form->createView(),
        ));
	}

    public function isDisableLogoutPage(Application $app)
    {
        $uri = str_replace($app['config']['root'], '', $app['request']->server->get('REDIRECT_URL'));
        $disableLogout = $app['orm.em']->getRepository('Eccube\Entity\Master\DisableLogout')
            ->findBy(array(
                'name' => $uri,
            ));
        $disableLogout = false;
        if (count($disableLogout) > 0) {
            $disableLogout = true;
        }

        return $disableLogout;
    }
}