<?php

function trans($id, array $parameters = array(), $domain = null, $locale = null)
{
    $app = \Eccube\Application::getInstance();
    if (isset($app['translator'])) {
        $app['translator']->trans($id, $parameters, $domain, $locale);
    }
}

function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
{
    $app = \Eccube\Application::getInstance();
    if (isset($app['translator'])) {
        $app['translator']->transChoice($id, $number, $parameters, $domain, $locale);
    }

}
