<?php

namespace Acme\Controller;

use Eccube\Application;
use Eccube\Entity\BaseInfo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class EntityFormController
{
    /**
     * @Route("/entity-form")
     */
    public function index(Application $app, Request $request)
    {
        $BaseInfo = new BaseInfo();

        $builder = $app->form(
            $BaseInfo,
            [
                'data_class' => BaseInfo::class,
            ]
        );

        $form = $builder->getForm();
        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // do stuff.
        }

        $template = '
            {{ form_start(form) }}
            {{ form_row(form) }}
            {{ form_end(form) }}
        ';

        $params = ['form' => $form->createView()];

        return $app['twig']
            ->createTemplate($template)
            ->render($params);
    }
}
