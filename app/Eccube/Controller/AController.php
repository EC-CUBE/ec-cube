<?php
namespace Eccube2\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
        return $app->forward('/test/new', $request, ['param_init' => $id]);
    }
}
