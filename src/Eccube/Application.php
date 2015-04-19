<?php

namespace Eccube;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcher;


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

        // set env
        if (!isset($app['env']) || empty($app['env'])) {
            $app['env'] = 'prod';
        }
        if ($app['env'] === 'dev' || $app['env'] === 'test') {
            $app['debug'] = true;
        }

        // load config
        $this['config'] = $app->share(function () {
            $config = Yaml::parse(__DIR__ .'/../../app/config/eccube/config.yml');
            return $config;
        });

        // constant 上書き
        $app['config'] = $app->share($app->extend("config", function ($config, \Silex\Application $app) {
            $constant_file = __DIR__ .'/../../app/config/eccube/constant.yml';
            if (is_readable($constant_file)) {
                $config_constant = Yaml::parse(__DIR__ .'/../../app/config/eccube/constant.yml');
            } else {
                $config_constant = $app['eccube.repository.master.constant']->getAll();
                file_put_contents($constant_file, Yaml::dump($config_constant));
            }

            return array_merge($config_constant, $config);
        }));

        $this->register(new \Silex\Provider\ServiceControllerServiceProvider());
        $this->register(new \Silex\Provider\SessionServiceProvider());

        $this->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => array(
                __DIR__ . '/View',
                __DIR__ . '/../../app/plugin/',
            ),
            'twig.form.templates' => array('Form/form_layout.twig'),
            'twig.options' => array('cache' => __DIR__ . '/../../app/cache/twig'),
        ));
        $app['twig'] = $app->share($app->extend("twig", function (\Twig_Environment $twig, \Silex\Application $app) {
            $twig->addExtension(new \Eccube\Twig\Extension\EccubeExtension($app));

            return $twig;
        }));
        $this->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        $this->register(new \Silex\Provider\FormServiceProvider());
        $this->register(new \Silex\Provider\ValidatorServiceProvider());

        $this->register(new \Silex\Provider\TranslationServiceProvider(), array(
            'locale' => 'ja',
        ));
        $app['translator'] = $app->share($app->extend('translator', function($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
            $translator->addResource('yaml', __DIR__.'/Resource/locale/ja.yml', 'ja');

            return $translator;
        }));

        // インストールされてなければこれこまで読み込む
        if (!is_array($this['config'])) {
            $this->mount('', new ControllerProvider\FrontControllerProvider());
            $this->register(new ServiceProvider\EccubeServiceProvider());
            return ;
        }


        // Mail
        $this['swiftmailer.option'] = $this['config']['mail'];
        $this->register(new \Silex\Provider\SwiftmailerServiceProvider());
        $this['mail.message'] = function() {
            return \Swift_Message::newInstance();
        };

        // ORM
        $this->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => $this['config']['database']
        ));
        $this->register(new \Saxulum\DoctrineOrmManagerRegistry\Silex\Provider\DoctrineOrmManagerRegistryProvider());

        $ormOptions = array(
            'mappings' => array(
                array(
                    'type' => 'yml',
                    'namespace' => 'Eccube\Entity',
                    'path' => array(
                        __DIR__ . '/Resource/doctrine',
                        __DIR__ . '/Resource/doctrine/master',
                    ),
                ),
            ),
        );

       // EventDispatcher
        $app['eccube.event.dispatcher'] = $app->share(function() {
            return new EventDispatcher();
        });

        // EventSubscriber
        $basePath = __DIR__ . '/../../app/plugin';
        $finder = Finder::create()
            ->in($basePath)
            ->directories()
            ->depth(0);

        // Plugin events / service
        foreach ($finder as $dir) {
            $config = Yaml::parse($dir->getRealPath() . '/config.yml');
            
            if ($config['enable'] === true) {

                // Type: Event
                if (isset($config['event'])) {
                    $class = '\\Plugin\\' . $config['name'] . '\\' . $config['event'];
                    $subscriber = new $class($app);
                    $app['eccube.event.dispatcher']->addSubscriber($subscriber);
                }

                // Type: ServiceProvider
                if (isset($config['service'])) {
                    foreach ($config['service'] as $service) {
                        $class = '\\Plugin\\' . $config['name'] . '\\ServiceProvider\\' . $service;
                        $app->register(new $class($app));
                    }
                }

                // Doctrine Extend
                if (isset($config['orm.path'])) {
                    $pathes = array();
                    foreach($config['orm.path'] as $path) {
                        $pathes[] = $basePath . '/' . $config['name'] . $path;
                    }
                    $ormOptions['mappings'][] = array(
                        'type' => 'yml',
                        'namespace' => 'Plugin\\' . $config['name'] . '\\Entity',
                        'path' => $pathes,
                    );
                }
            }
        }

        // hook point
        $this->before(function (Request $request, Application $app) {
            $app['eccube.event.dispatcher']->dispatch('eccube.event.app.before');
        }, \Silex\Application::EARLY_EVENT);

        $this->before(function(Request $request, \Silex\Application $app) {
            $event = $app->parseController($request) . '.before';
            $app['eccube.event.dispatcher']->dispatch($event);
        });

        $this->after(function(Request $request, Response $response) use ($app) {
            $event = $app->parseController($request) . '.after';
            $app['eccube.event.dispatcher']->dispatch($event);
        });

        $this->after(function (Request $request, Response $response) use ($app) {
            $app['eccube.event.dispatcher']->dispatch('eccube.event.app.after');
        }, \Silex\Application::LATE_EVENT);

        $this->finish(function(Request $request, Response $response) use ($app) {
            $event = $app->parseController($request) . '.finish';
            $app['eccube.event.dispatcher']->dispatch($event);
        });

        //Doctrine ORM
        $this->register(new \Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), array(
            "orm.proxies_dir" => __DIR__ . '/../../app/cache/doctrine',
            'orm.em.options' => $ormOptions,
        ));

        $this->register(new ServiceProvider\EccubeServiceProvider());

        // Security
        $this->register(new \Silex\Provider\SecurityServiceProvider(), array(
             'security.firewalls' => array(
                'customer' => array(
                    'pattern' => '^/',
                    'form' => array(
                        'login_path' => '/mypage/login.php',
                        'check_path' => '/login_check',
                        'username_parameter' =>  'login_email',
                        'password_parameter' => 'login_pass',
                        'with_csrf' => true,
                        'use_forward' => true,
                    ),
                    'logout' => array(
                        'logout_path' => '/logout',
                        'target_url' => '/',
                    ),
                    'users' => $app['eccube.repository.customer'],
                    'anonymous' => true,
                ),
            ),
        ));
        $app['security.access_rules'] = array(
            array('^/mypage/login.php', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/mypage/', 'ROLE_USER'),
        );
        $app['eccube.encoder.customer'] = $app->share(function ($app) {
            return new \Eccube\Security\Core\Encoder\CustomerPasswordEncoder($app['config']);
        });
        $app['security.encoder_factory'] = $app->share(function ($app) {
            return new \Symfony\Component\Security\Core\Encoder\EncoderFactory(array(
                'Eccube\Entity\Customer' => $app['eccube.encoder.customer'],
            ));
        });
        $app['user'] = $app->share(function($app) {
            $token = $app['security']->getToken();

            return ($token !== null) ? $token->getUser() : null;
        });

        $app['filesystem'] = function() {
            return new \Symfony\Component\Filesystem\Filesystem();
        };

        $app->register(new \Silex\Provider\MonologServiceProvider(), array(
            'monolog.logfile' => __DIR__ . '/../../app/log/site.log',
        ));

        // Silex Web Profiler
        if ($app['env'] === 'dev') {
            $app->register(new \Silex\Provider\WebProfilerServiceProvider(), array(
                'profiler.cache_dir' => __DIR__ . '/../../app/cache/profiler',
                'profiler.mount_prefix' => '/_profiler', // this is the default
            ));
            $app->register(new \Saxulum\SaxulumWebProfiler\Provider\SaxulumWebProfilerProvider());
        }

        $this->mount('', new ControllerProvider\FrontControllerProvider());
        $this->mount('/admin', new ControllerProvider\AdminControllerProvider());

        $this['callback_resolver'] = $this->share(function () use ($app) {
            return new CallbackResolver($app);
        });


        $app['eccube.layout'] = null;
        $this->before(function (Request $request, \Silex\Application $app) {
            $url = str_replace($app['config']['root'], '', $app['request']->server->get('REDIRECT_URL'));
            if (substr($url, -1) === '/') {
                $url .= 'index.php';
            }

            $qb = $app['orm.em']->createQueryBuilder()
                ->select('p, bp, b')
                ->from('Eccube\Entity\PageLayout', 'p')
                ->leftJoin('p.BlocPositions', 'bp', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.page_id = bp.page_id OR bp.anywhere = 1')
                ->innerJoin('bp.Bloc', 'b')
                ->andWhere('p.device_type_id = :device_type_id AND p.url = :url')
                ->addOrderBy('bp.target_id', 'ASC')
                ->addOrderBy('bp.bloc_row', 'ASC');
            try {
                $result = $qb->getQuery()
                    ->setParameters(array(
                        'device_type_id'    => 10,
                        'url'               => $url,
                    ))
                    ->getSingleResult();
            } catch (\Doctrine\ORM\NoResultException $e) {
                $result = null;
            }

            $app['eccube.layout'] = $result;
        });

        if ($app['env'] === 'test') {
            $app['session.test'] = true;
            $app['exception_handler']->disable();
        }
    }

    public function parseController(Request $request)
    {
        $route = str_replace('_', '.', $request->attributes->get('_route'));
        return 'eccube.event.controller.' . $route;
    }
}
