<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes
        ->add('oauth2_authorize', '/%eccube_admin_route%/authorize')
        ->controller(['league.oauth2_server.controller.authorization', 'indexAction'])

        ->add('oauth2_token', '/token')
        ->controller(['league.oauth2_server.controller.token', 'indexAction'])
        ->methods(['POST'])
    ;
};
