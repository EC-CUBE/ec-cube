<?php

namespace Eccube;

class ControllerResolver extends \Silex\ControllerResolver
{
    /**
     * @inheritdoc
     */
    protected function instantiateController($class)
    {
        $object = parent::instantiateController($class);
        if ($object instanceof \Eccube\Controller\AbstractController) {
            $object->setApp($this->app);
        }
        return $object;
    }
}
