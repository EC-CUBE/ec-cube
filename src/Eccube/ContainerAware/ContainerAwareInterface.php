<?php

namespace Eccube\ContainerAware;

use Eccube\Application;

interface ContainerAwareInterface
{
    /**
     * Set Application container.
     * 
     * @param \Eccube\Application $app
     */
    public function setContainer(Application $app);

    /**
     * Get Appliction container.
     * 
     * @return \Eccube\Application application
     */
    public function getContainer();
}
