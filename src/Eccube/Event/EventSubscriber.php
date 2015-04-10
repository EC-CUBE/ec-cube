<?php

namespace Eccube\Event;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class EventSubscriber
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function subscribe()
    {
        $basePath = __DIR__ . '/../../../app/plugin';
        
        $finder = Finder::create()
            ->in($basePath)
            ->directories()
            ->depth(0);

        foreach ($finder as $dir) {
            $vendor = $dir->getFilename();
            $config = Yaml::parse($dir->getRealPath() . '/config.yml');
            $plugin = $config['name'];

            $class = '\\Plugin\\' . $vendor . '\\' . $plugin ;
            $subscriber = new $class($this->app);
            $this->app['eccube.event.dispatcher']->addSubscriber($subscriber);
        }
    }
}