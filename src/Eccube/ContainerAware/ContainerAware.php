<?php

namespace Eccube\ContainerAware;

use Eccube\Application;

abstract class ContainerAware implements ContainerAwareInterface
{
    /**
     * @var \Eccube\Application app
     */
    protected $app;

    /**
     * Set Application container.
     * 
     * @param \Eccube\Application $app
     */
    public function setContainer(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get Appliction container.
     * 
     * @return \Eccube\Application application
     */
    public function getContainer()
    {
        return $this->app;
    }
}
