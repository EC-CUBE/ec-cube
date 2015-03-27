<?php

namespace Eccube;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;

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

        $this->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => array(__DIR__ . '/View'),
            'twig.form.templates' => array('Form/form_layout.twig'),
        ));
        $this->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        $this->register(new \Silex\Provider\FormServiceProvider());
        $this->register(new \Silex\Provider\ValidatorServiceProvider());

        $this->register(new \Silex\Provider\TranslationServiceProvider(), array(
            'locale' => 'ja',
        ));
        $app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
            $translator->addResource('yaml', __DIR__.'/Resource/locale/ja.yml', 'ja');

            return $translator;
        }));

        $this->register(new \Silex\Provider\SwiftmailerServiceProvider());
        $this->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => $this['config']['database']
        ));
        $this->register(new \Saxulum\DoctrineOrmManagerRegistry\Silex\Provider\DoctrineOrmManagerRegistryProvider());
        $this['mail.message'] = function() {
            return \Swift_Message::newInstance();
        };

        $this->register(new \Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), array(
            "orm.proxies_dir" => __DIR__ . '/../../app/cache/doctrine',
            'orm.em.options' => array(
                'mappings' => array(
                    array(
                        'type' => 'simple_yml',
                        'namespace' => 'Eccube\Entity',
                        'path' => __DIR__ . '/Resource/doctrine',
                    ),
                ),
            ),
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
            return new \Eccube\Framework\Security\Core\Encoder\CustomerPasswordEncoder($app['config']);
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

        // Silex Web Profiler
        $app->register(new \Silex\Provider\WebProfilerServiceProvider(), array(
            'profiler.cache_dir' => __DIR__ . '/../../app/cache/profiler',
            'profiler.mount_prefix' => '/_profiler', // this is the default
        ));

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
            $result = null;
            try {
                $result = $qb->getQuery()
                    ->setParameters(array(
                        'device_type_id'    => 10,
                        'url'               => $url,
                    ))
                    ->getSingleResult();
            } catch (\Doctrine\ORM\NoResultException $e) {
            }

            $app['eccube.layout'] = $result;
        });
        // テスト実装
        $this->register(new Plugin\ProductReview\ProductReview());
    }

}
