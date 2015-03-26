<?php

namespace Eccube\Controller;

use Eccube\Application;

class AbstractController
{
	public function __construct()
	{
	}

    protected function getBoundForm(Application $app, $type)
    {
        $form = $app['form.factory']
            ->createBuilder($app['eccube.form.type.' . $type], $app['eccube.entity.' . $type])
            ->getForm();
        $form->handleRequest($app['request']);

        return $form;
    }
    
    public function Index(Application $app)
    {
        return $app['twig']->render($this->getViewFilePath($app));
    }
    
    private function getViewFilePath(Application $app)
    {
        $classString = $app['request']->get('_route');
        if(strpos($classString, "_") !== false){
            $classString = str_replace("_", "/", $classString);
        }else{
            $classString .= "/index";
        }
        return ucwords($classString).".twig";
    }
}