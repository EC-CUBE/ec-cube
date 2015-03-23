<?php

namespace Eccube;

use Symfony\Component\HttpFoundation\Request as BaseRequest;
use Symfony\Component\Yaml\Yaml;
use Silex\Provider;

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

        // load config
        $this['config'] = function () {
            $config = Yaml::parse(__DIR__ .'/../../app/config/eccube/config.yml');
            return $config;
        };
        $this['swiftmailer.option'] = $this['config']['mail'];

        $this->register(new \Silex\Provider\ServiceControllerServiceProvider());
        $this->register(new \Silex\Provider\SessionServiceProvider());
       //  $this->register(new \Silex\Provider\SecurityServiceProvider(), array(
       //      'security.firewalls' => ''
       // ));
        $this->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/View',
        ));
        $this->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        $this->register(new \Silex\Provider\FormServiceProvider());
        $this->register(new \Silex\Provider\ValidatorServiceProvider());
        $this->register(new \Silex\Provider\TranslationServiceProvider());
        $this->register(new \Silex\Provider\SwiftmailerServiceProvider());
        $this->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => $this['config']['database']
        ));
        $this->register(new \Saxulum\DoctrineOrmManagerRegistry\Silex\Provider\DoctrineOrmManagerRegistryProvider());
        $this['mail.message'] = function() {
            return \Swift_Message::newInstance();
        };

        // Silex Web Profiler
        $app->register(new Provider\WebProfilerServiceProvider(), array(
            'profiler.cache_dir' => __DIR__.'/../../app/cache/profiler',
            'profiler.mount_prefix' => '/_profiler', // this is the default
        ));

        $this->register(new \Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), array(
            "orm.proxies_dir" => __DIR__ . '/../../app/cache/doctrine',
            'orm.em.options' => array(
                'mappings' => array(
                    array(
                        'type' => 'simple_yml',
                        'namespace' => 'Eccube\Entity',
                        'path' => __DIR__ . '/Resource',
                    ),
                ),
            ),
        ));

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
