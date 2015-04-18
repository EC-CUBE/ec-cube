<?php

namespace Eccube\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController
{

    private $title;

    public function __construct()
    {
        $this->title = 'お問い合わせ';
    }

    public function index(Application $app, Request $request)
    {
        return $app['twig']->render('Product/index.twig', array(
            'title' => $this->title,
        ));
    }

    public function detail(Application $app, Request $request, $productId)
    {
        /* @var $product \Eccube\Entity\Product */
        $Product = $app['eccube.repository.product']->get($productId);
        if ($Product->getStatus() !== 1) {
            throw new NotFoundHttpException();
        }

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('add_cart', null, array(
            'product' => $Product,
        ));

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                
            }
        }

        return $app['twig']->render('Product/detail.twig', array(
            'title' => $this->title,
            'form' => $form->createView(),
            'Product' => $Product,
        ));
    }

}
