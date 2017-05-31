<?php

namespace Eccube\Routing;

use Symfony\Component\Routing\Router;

class EccubeRouter extends Router
{
    protected $requireHttps = false;

    protected $adminPrefix = 'admin';

    protected $userDataPrefix = 'user_data';

    public function setRequireHttps($requireHttps)
    {
        $this->requireHttps = (bool)$requireHttps;
    }

    public function setAdminPrefix($adminPrefix)
    {
        $this->adminPrefix = $adminPrefix;
    }

    public function setUserDataPrefix($userDataPrefix)
    {
        $this->userDataPrefix = $userDataPrefix;
    }

    public function getRouteCollection()
    {
        $collection = parent::getRouteCollection();

        if ($this->requireHttps) {
            $collection->setSchemes('https');
        }

        $collection->addRequirements(
            [
                '_admin' => $this->adminPrefix,
                '_user_data' => $this->userDataPrefix,
            ]
        );

        $collection->addDefaults(
            [
                '_admin' => $this->adminPrefix,
                '_user_data' => $this->userDataPrefix,
            ]
        );

        return $collection;
    }
}
