<?php

namespace Eccube;

use Symfony\Component\HttpFoundation\Request as BaseRequest;

class Application extends \Silex\Application
{

    public function __construct(array $values = array())
    {
        $app = $this;
        ini_set('error_reporting', E_ALL | ~E_STRICT);

        parent::__construct($values);

        $app->register(new \Silex\Provider\ServiceControllerServiceProvider());
        $app->register(new \Silex\Provider\TwigServiceProvider());
        $this->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        $app->register(new \Silex\Provider\WebProfilerServiceProvider(), array(
            'profiler.cache_dir' => __DIR__ . '/../../app/cache/profiler',
            'profiler.mount_prefix' => '/_profiler', // this is the default
        ));

        $this->register(new ServiceProvider\EccubeServiceProvider());
        $this->mount('', new ControllerProvider\FrontControllerProvider());
        $this->mount('/admin', new ControllerProvider\AdminControllerProvider());

        $this['callback_resolver'] = $this->share(function () use ($app) {
            return new CallbackResolver($app);
        });
    }

    /**
     * Handles the request and delivers the response.
     *
     * @param Request|null $request Request to process
     */
    public function run(BaseRequest $request = null)
    {
        if (null === $request) {
            $request = Request::createFromGlobals();
        }

        parent::run($request);
    }

}
