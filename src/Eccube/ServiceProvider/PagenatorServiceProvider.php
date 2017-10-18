<?php

namespace Eccube\ServiceProvider;

use Eccube\EventListener\PaginatorListener;
use Knp\Component\Pager\Paginator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PagenatorServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['paginator'] = $app->protect(function () {
            $paginator = new Paginator();
            $paginator->subscribe(new PaginatorListener());

            return $paginator;
        });
    }
}
