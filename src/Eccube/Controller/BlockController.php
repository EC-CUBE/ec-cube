<?php

namespace Eccube\Controller;

use Eccube\Application;
use Eccube\Entity\BlocPosition;

class BlockController
{

    public function index(Application $app)
    {
        $position = $app['request']->get('position');

        $blocks = array();

        if ($app['eccube.layout']) {
            foreach ($app['eccube.layout']->getBlocPositions() as $blocPositions) {
                if ($blocPositions->getTargetId() == constant("Eccube\Entity\BlocPosition::" . $position)) {
                    $blocks[] = $blocPositions->getBloc();
                }
            }
        }

        return $app['twig']->render('bloc.twig', array(
            'blocks' => $blocks,
        ));
    }

}
