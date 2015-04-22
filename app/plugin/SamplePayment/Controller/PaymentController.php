<?php

namespace Plugin\SamplePayment\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class PaymentController
{
    public function index(Application $app, Request $request)
    {
        $form = $app['form.factory']
            ->createBuilder('sample.payment')
            ->getForm();

        $sample = $app['eccube.plugin.service.payment']->sample();

        return $app['view']->render('SamplePayment/View/index.twig', compact('sample'));
    }
}