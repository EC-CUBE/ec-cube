<?php

namespace Plugin\HogePlugin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class IndexController
{
    /**
     * @Route("/hogeplugin/hello")
     * @Template("HogePlugin/Resource/template/index.twig")
     */
    public function index(Request $request)
    {
        dump($request);

        return [];
    }
}
