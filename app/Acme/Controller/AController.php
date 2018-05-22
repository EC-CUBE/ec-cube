<?php

namespace Acme\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/test")
 * @Template("test/index.twig")
 */
class AController
{
    /**
     * @Route("/initialize/{id}", requirements={"id" = "\d+"})
     */
    public function initialize(Application $app, Request $request, $id = 0)
    {
        dump('A: initialize');
        $t = new \Eccube\Entity\Csv();
        $t->setDispName($id + 100000);
        $app['request_scope']->set('csv', $t);

        return $app->forward('/test/new', ['param_init' => $id]);
    }
}
