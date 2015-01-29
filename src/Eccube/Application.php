<?php

namespace Eccube;

use Symfony\Component\HttpFoundation\Request as BaseRequest;

class Application extends \Silex\Application
{
    /** @var Application app */
    protected static $app;

    /**
     * Alias
     * 
     * @return object 
     */
    public static function alias($name)
    {
        $args = func_get_args();
        array_shift($args);
        $obj = static::$app[$name];

        if (is_callable($obj)) {
            return call_user_func_array($obj, $args);
        } else {
            return $obj;
        }
    }

    public function __construct(array $values = array())
    {
        $app = $this;
        static::$app = $this;
        ini_set('error_reporting', E_ALL | ~E_STRICT);

        parent::__construct($values);

        $this->register(new \Silex\Provider\ServiceControllerServiceProvider());
        $this->register(new \Silex\Provider\TwigServiceProvider());
        $this->register(new \Silex\Provider\UrlGeneratorServiceProvider());

        $this->register(new ServiceProvider\EccubeServiceProvider());
        $this->mount('', new ControllerProvider\FrontControllerProvider());
        $this->mount('/admin', new ControllerProvider\AdminControllerProvider());

        $this['callback_resolver'] = $this->share(function () use ($app) {
            return new CallbackResolver($app);
        });

        // テスト実装
        $this->register(new Plugin\ProductReview\ProductReview());
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
