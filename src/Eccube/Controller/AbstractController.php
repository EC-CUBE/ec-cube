<?php

namespace Eccube\Controller;

class AbstractController
{
    /**
     * @var \Eccube\Application
     */
    protected $app;

    /**
     * set application.
     *
     * @param \Eccube\Application $app
     */
    public function setApp(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * shortcut method "$app->redirect($app['url_generator']->generate(...))"
     *
     * @param $name
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirect($name, $parameters = array())
    {
        return $this->app->redirect($this->url($name, $parameters));
    }

    /**
     * shortcut method "$app['url_generator']->generate(...)"
     *
     * @param $name
     * @param array $parameters
     * @return mixed
     */
    protected function url($name, $parameters = array())
    {
        return $this->app['url_generator']->generate($name, $parameters);
    }

    /**
     * shortcut method "$app['view']->render(...)"
     *
     * @param $name
     * @param array $parameters
     * @return mixed
     */
    protected function render($name, $parameters = array())
    {
        return $this->app['view']->render($name, $parameters);
    }
}
