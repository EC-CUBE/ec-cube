<?php

namespace Eccube\Controller\Admin;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CustomerController
{
    public function index(Application $app)
    {
        $Customers = array();

        $form = $app['form.factory']
            ->createBuilder('customer_search')
            ->getForm();

        $showResult = false;

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);

            if ($form->isValid()) {
                $showResult = true;

                $qb = $app['orm.em']
                    ->getRepository('Eccube\Entity\Customer')
                    ->getQueryBuilderBySearchData($form->getData());
                $query = $qb->getQuery();
                $Customers = $query->getResult();
            }

        }

        return $app['view']->render('Admin/Customer/index.twig', array(
            'form' => $form->createView(),
            'showResult' => $showResult,
            'Customers' => $Customers,
        ));
    }


    public function resend(Application $app, $customerId)
    {
        $Customer = $app['orm.em']->getRepository('Eccube\Entity\Customer')
            ->find($customerId);

        if ($Customer) {
            $message = $app['mail.message']
                ->setFrom(array('sample@example.com'))
                ->setTo(array($Customer->getEmail()))
                ->setBcc($app['config']['mail_cc'])
                ->setSubject('[EC-CUBE3] 会員登録が完了しました。')
                ->setBody($app['view']->render('Mail/entry_complete.twig', array(
                    'customer' => $Customer
                )));
            $app['mailer']->send($message);

            $app['session']->getFlashBag()->add('admin.customer.complete', 'admin.customer.resend.complete');
        }

        return $app->redirect($app['url_generator']->generate('admin_customer'));
    }

    public function delete(Application $app, $customerId)
    {
        $Customer = $app['orm.em']->getRepository('Eccube\Entity\Customer')
            ->find($customerId);

        if ($Customer) {
            $Customer->setDelFlg(1);
            $app['orm.em']->persist($Customer);
            $app['orm.em']->flush();

            $app['session']->getFlashBag()->add('admin.customer.complete', 'admin.customer.delete.complete');
        }

        return $app['request'];
        // return $app->redirect($app['url_generator']->generate('admin_customer'));
    }

}