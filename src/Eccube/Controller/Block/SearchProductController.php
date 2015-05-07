<?php

namespace Eccube\Controller\Block;

use Eccube\Application;

class SearchProductController
{
    public function index(Application $app)
    {
        $form = $app['form.factory']
            ->createNamedBuilder('', 'search_product')
            ->getForm();

        return $app['view']->render('Block/search_products.twig', array(
            'form' => $form->createView(),
        ));
    }
}
