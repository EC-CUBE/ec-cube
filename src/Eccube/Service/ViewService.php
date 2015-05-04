<?php

namespace Eccube\Service;

use Eccube\Event\RenderEvent;

class ViewService
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function render($name, array $context = array())
    {
        $template = $this->app['twig']->loadTemplate($name);
        $compiledSource = $template->render($context);

        $event = new RenderEvent($compiledSource);

        $route = str_replace('_', '.', $this->app['request']->attributes->get('_route'));
        $this->app['eccube.event.dispatcher']->dispatch('eccube.event.render.' . $route . '.before', $event);

        $source = $event->getSource();

        return $source;
    }
}
