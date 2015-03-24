<?php

namespace Eccube\Controller;

use Eccube\Application;
use Eccube\Entity\BlocPosition;

class BlocController
{

    public function Index(Application $app)
    {
        $position = $app['request']->get('position');

        $blocs = array();

        if ($app['eccube.layout']) {
            foreach ($app['eccube.layout']->getBlocPositions() as $blocPositions) {
                if ($blocPositions->getTargetId() == constant("Eccube\Entity\BlocPosition::" . $position)) {
                    $blocs[] = $blocPositions->getBloc();
                }
            }
        }

        return $app['twig']->render('bloc.twig', array(
            'blocs' => $blocs,
        ));
    }


}