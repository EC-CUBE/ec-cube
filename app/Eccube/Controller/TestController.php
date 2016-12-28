<?php
namespace Eccube2\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/products")
 */
class TestController
{
    /**
     * @Method("GET")
     * @Route("/detail/{id}", requirements={"id" = "\d+"})
     * //@Security("has_role('ROLE_ADMIN')")
     */
    public function testMethod(\Eccube\Application $app, Request $request, $product)
    {
        return new Response("test Method: ".$id);
    }
}
