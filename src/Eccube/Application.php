<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Monolog\Logger;

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
            $config_file = __DIR__ . '/../../app/config/eccube/config.yml';
            if (file_exists($config_file)) {
                $config = Yaml::parse($config_file);
            } else {
                $config = array();
            }

            $constant_file = __DIR__ . '/../../app/config/eccube/constant.yml';
            $constant_dist = __DIR__ . '/../../app/config/eccube/constant.yml.dist';

            if (file_exists($constant_file)) {
                $config_constant = Yaml::parse($constant_file);
            } elseif (file_exists($constant_dist)) {
                $config_constant = Yaml::parse($constant_dist);
            } else {
                $config_constant = array();
            }

            return array_merge($config_constant, $config);
        });

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
        $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
            $translator->addResource('yaml', __DIR__.'/Resource/locale/ja.yml', 'ja');

            return $translator;
        }));

        // インストールされてなければこれこまで読み込む
        if (!file_exists(__DIR__ . '/../../app/config/eccube/config.yml')) {
            $app->mount('', new ControllerProvider\InstallControllerProvider());
            $app->register(new ServiceProvider\EccubeServiceProvider());
            $app->error(function (\Exception $e, $code) use ($app) {
                if ($code === 404) {
                    return $app->redirect($app['url_generator']->generate('install'));
                } elseif ($app['debug']) {
                    return;
                }

                return new Response('エラーが発生しました.');
            });

            return;
        }

        // Mail
        $this['swiftmailer.option'] = $this['config']['mail'];
        // $this->register(new \Silex\Provider\SwiftmailerServiceProvider());
        if ($app['env'] === 'dev' || $app['env'] === 'test') {
            if (isset($this['config']['delivery_address'])) {
                $this['delivery_address'] = $this['config']['delivery_address'];
            }
        }
        $this->register(new ServiceProvider\EccubeSwiftmailerServiceProvider());

        $this['mail.message'] = function () {
            return \Swift_Message::newInstance();
        };

        // ORM
        $this->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => $this['config']['database']
        ));
        $this->register(new \Saxulum\DoctrineOrmManagerRegistry\Silex\Provider\DoctrineOrmManagerRegistryProvider());

        //Doctrine ORM
        $this->register(new \Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), array(
            "orm.proxies_dir" => __DIR__ . '/../../app/cache/doctrine',
            'orm.em.options' => array(
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
            ),
        ));

        $this->register(new ServiceProvider\EccubeServiceProvider());
        $this->register(new ServiceProvider\LegacyServiceProvider());

       // EventDispatcher
        $app['eccube.event.dispatcher'] = $app->share(function () {
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
                    foreach ($config['orm.path'] as $path) {
                        $pathes[] = $basePath . '/' . $config['name'] . $path;
                    }
                    $app['orm.em.options'] = $app->extend('orm.em.options', function ($options) use ($config, $pathes) {
                        $options['mappings'][] = array(
                            'type' => 'yml',
                            'namespace' => 'Plugin\\' . $config['name'] . '\\Entity',
                            'path' => $pathes,
                        );
                    });
                }
            }
        }

        // hook point
        $this->before(function (Request $request, Application $app) {
            $app['eccube.event.dispatcher']->dispatch('eccube.event.app.before');
        }, \Silex\Application::EARLY_EVENT);

        $this->before(function (Request $request, \Silex\Application $app) {
            $event = $app->parseController($request) . '.before';
            $app['eccube.event.dispatcher']->dispatch($event);
        });

        $this->after(function (Request $request, Response $response) use ($app) {
            $event = $app->parseController($request) . '.after';
            $app['eccube.event.dispatcher']->dispatch($event);
        });

        $this->after(function (Request $request, Response $response) use ($app) {
            $app['eccube.event.dispatcher']->dispatch('eccube.event.app.after');
        }, \Silex\Application::LATE_EVENT);

        $this->finish(function (Request $request, Response $response) use ($app) {
            $event = $app->parseController($request) . '.finish';
            $app['eccube.event.dispatcher']->dispatch($event);
        });

        // Security
        $app['colnum'] = 1;
        $this->register(new \Silex\Provider\SecurityServiceProvider(), array(
             'security.firewalls' => array(
                'admin' => array(
                    'pattern' => '^/admin',
                    'form' => array(
                        'login_path' => '/admin/login',
                        'check_path' => '/admin/login_check',
                        'username_parameter' =>  'login_id',
                        'password_parameter' => 'password',
                        'with_csrf' => true,
                        'use_forward' => true,
                    ),
                    'logout' => array(
                        'logout_path' => '/admin/logout',
                        'target_url' => '/admin/',
                    ),
                    'users' => $app['eccube.repository.member'],
                    'anonymous' => true,
                ),
                'customer' => array(
                    'pattern' => '^/',
                    'form' => array(
                        'login_path' => '/mypage/login',
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
            array('^/admin/login', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/admin', 'ROLE_ADMIN'),
            array('^/mypage/login', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/mypage/refusal_complete', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/mypage', 'ROLE_USER'),
        );
        $app['eccube.password_encoder'] = $app->share(function ($app) {
            return new \Eccube\Security\Core\Encoder\PasswordEncoder($app['config']);
        });
        $app['security.encoder_factory'] = $app->share(function ($app) {
            return new \Symfony\Component\Security\Core\Encoder\EncoderFactory(array(
                'Eccube\Entity\Customer' => $app['eccube.password_encoder'],
                'Eccube\Entity\Member' => $app['eccube.password_encoder'],
            ));
        });
        $app['eccube.event_listner.security'] = $app->share(function ($app) {
            return new \Eccube\EventListner\SecurityEventListner($app['orm.em']);
        });
        $app['user'] = $app->share(function ($app) {
            $token = $app['security']->getToken();

            return ($token !== null) ? $token->getUser() : null;
        });

        $app['filesystem'] = function () {
            return new \Symfony\Component\Filesystem\Filesystem();
        };

        $app->register(new \Silex\Provider\MonologServiceProvider(), array(
            'monolog.logfile' => __DIR__ . '/../../app/log/site.log',
        ));

        // Silex Web Profiler
        if ($app['env'] === 'dev') {
            $app->register(new \Silex\Provider\WebProfilerServiceProvider(), array(
                'profiler.cache_dir' => __DIR__ . '/../../app/cache/profiler',
                'profiler.mount_prefix' => '/_profiler',
            ));
            $app->register(new \Saxulum\SaxulumWebProfiler\Provider\SaxulumWebProfilerProvider());
        }

        $app->mount('', new ControllerProvider\FrontControllerProvider());
        $app->mount('/admin', new ControllerProvider\AdminControllerProvider());
        $app->error(function (\Exception $e, $code) use ($app) {
            if ($app['debug']) {
                return;
            }

            switch ($code) {
                case 404:
                    break;
                default:
                    break;
            }

            return new Response('エラーが発生しました.');
        });

        $this['callback_resolver'] = $this->share(function () use ($app) {
            return new LegacyCallbackResolver($app);
        });

        $app['eccube.layout'] = null;
        $this->before(function (Request $request, \Silex\Application $app) {
            $url = str_replace($app['config']['root'], '', $app['request']->server->get('REDIRECT_URL'));
            if (substr($url, -1) === '/' || $url === '') {
                $url .= 'index.php';
            }
            if ($url === '/index.php') {
                $url = 'index.php';
            }

            // anywhere指定のもの以外を取得
            $qb = $app['orm.em']->createQueryBuilder()
                ->select('p, bp, b')
                ->from('Eccube\Entity\PageLayout', 'p')
                ->leftJoin('p.BlocPositions', 'bp', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.page_id = bp.page_id')
                ->innerJoin('bp.Bloc', 'b')
                ->andWhere('p.device_type_id = :device_type_id AND p.url = :url AND bp.anywhere != 1')
                ->addOrderBy('bp.target_id', 'ASC')
                ->addOrderBy('bp.bloc_row', 'ASC');
            try {
                $result = $qb->getQuery()
                    ->setParameters(array(
                        'device_type_id'    => 10,
                        'url'               => $url,
                    ))
                    ->getSingleResult()
                ;
                // anywhere指定のものをマージ
                $AnywhereBlocPositions = $app['orm.em']
                    ->getRepository('Eccube\Entity\BlocPosition')
                    ->findBy(array(
                        'device_type_id' => 10,
                        'anywhere' => 1,
                    ))
                ;
                // TODO: 無理やり計算して無理やりいれている
                $BlocPositions = $result->getBlocPositions();
                foreach ($AnywhereBlocPositions as $AnywhereBlocPosition) {
                    $result->addBlocPosition($AnywhereBlocPosition);
                }
                $hasLeftNavi = false;
                $hasRightNavi = false;
                foreach ($BlocPositions as $BlocPosition) {
                    if ($BlocPosition->getTargetId() == 1) {
                        $hasLeftNavi = true;
                    }
                    if ($BlocPosition->getTargetId() == 3) {
                        $hasRightNavi = true;
                    }
                }
                $colnum = 1;
                if ($hasLeftNavi) {
                    $colnum ++;
                    $app['hasLeftNavi'] = true;
                }
                if ($hasRightNavi) {
                    $colnum ++;
                }
                $app['colnum'] = $colnum;

            } catch (\Doctrine\ORM\NoResultException $e) {
                $result = null;
                $app['colnum'] = 1;
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

    public function boot()
    {
        parent::boot();

        $app = $this;

        // constant 上書き
        $app['config'] = $app->share($app->extend("config", function ($config, \Silex\Application $app) {
            $constant_file = __DIR__ . '/../../app/config/eccube/constant.yml';
            if (is_readable($constant_file)) {
                $config_constant = Yaml::parse($constant_file);
            } else {
                $config_constant = $app['eccube.repository.master.constant']->getAll($config);
                if ($config_constant) {
                    file_put_contents($constant_file, Yaml::dump($config_constant));
                }
            }

            return array_merge($config_constant, $config);
        }));

        $app['dispatcher']->addListener(\Symfony\Component\Security\Http\SecurityEvents::INTERACTIVE_LOGIN, array($app['eccube.event_listner.security'], 'onInteractiveLogin'));
    }

    public function addSuccess($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->add('eccube.' . $namespace . '.success', $message);
    }

    public function addError($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->add('eccube.' . $namespace . '.error', $message);
    }

    public function addDanger($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->add('eccube.' . $namespace . '.danger', $message);
    }

    public function addWarning($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->add('eccube.' . $namespace . '.warning', $message);
    }

    public function addInfo($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->add('eccube.' . $namespace . '.info', $message);
    }

    /*
     * 以下のコードの著作権について
     *
     * (c) Fabien Potencier <fabien@symfony.com>
     *
     * For the full copyright and license information, please view the silex
     * LICENSE file that was distributed with this source code.
     */
    /** FormTrait */
    /**
     * Creates and returns a form builder instance
     *
     * @param mixed $data    The initial data for the form
     * @param array $options Options for the form
     *
     * @return FormBuilder
     */
    public function form($data = null, array $options = array())
    {
        return $this['form.factory']->createBuilder('form', $data, $options);
    }

    /** MonologTrait */
    /**
     * Adds a log record.
     *
     * @param string $message The log message
     * @param array  $context The log context
     * @param int    $level   The logging level
     *
     * @return bool Whether the record has been processed
     */
    public function log($message, array $context = array(), $level = Logger::INFO)
    {
        return $this['monolog']->addRecord($level, $message, $context);
    }

    /** SecurityTrait */
    /**
     * Gets a user from the Security Context.
     *
     * @return mixed
     *
     * @see TokenInterface::getUser()
     */
    public function user()
    {
        if (null === $token = $this['security']->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        return $user;
    }

    /**
     * Encodes the raw password.
     *
     * @param UserInterface $user     A UserInterface instance
     * @param string        $password The password to encode
     *
     * @return string The encoded password
     *
     * @throws \RuntimeException when no password encoder could be found for the user
     */
    public function encodePassword(UserInterface $user, $password)
    {
        return $this['security.encoder_factory']->getEncoder($user)->encodePassword($password, $user->getSalt());
    }

    /** SwiftmailerTrait */
    /**
     * Sends an email.
     *
     * @param \Swift_Message $message          A \Swift_Message instance
     * @param array          $failedRecipients An array of failures by-reference
     *
     * @return int The number of sent messages
     */
    public function mail(\Swift_Message $message, &$failedRecipients = null)
    {
        return $this['mailer']->send($message, $failedRecipients);
    }

    /** TranslationTrait */
    /**
     * Translates the given message.
     *
     * @param string $id         The message id
     * @param array  $parameters An array of parameters for the message
     * @param string $domain     The domain for the message
     * @param string $locale     The locale
     *
     * @return string The translated string
     */
    public function trans($id, array $parameters = array(), $domain = 'messages', $locale = null)
    {
        return $this['translator']->trans($id, $parameters, $domain, $locale);
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string $id         The message id
     * @param int    $number     The number to use to find the indice of the message
     * @param array  $parameters An array of parameters for the message
     * @param string $domain     The domain for the message
     * @param string $locale     The locale
     *
     * @return string The translated string
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = 'messages', $locale = null)
    {
        return $this['translator']->transChoice($id, $number, $parameters, $domain, $locale);
    }

    /** TwigTrait */
    /**
     * Renders a view and returns a Response.
     *
     * To stream a view, pass an instance of StreamedResponse as a third argument.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A Response instance
     *
     * @return Response A Response instance
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $twig = $this['twig'];

        if ($response instanceof StreamedResponse) {
            $response->setCallback(function () use ($twig, $view, $parameters) {
                $twig->display($view, $parameters);
            });
        } else {
            if (null === $response) {
                $response = new Response();
            }
            $response->setContent($this['view']->render($view, $parameters));
        }

        return $response;
    }

    /**
     * Renders a view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     *
     * @return Response A Response instance
     */
    public function renderView($view, array $parameters = array())
    {
        return $this['view']->render($view, $parameters);
    }

    /** UrlGeneratorTrait */
    /**
     * Generates a path from the given parameters.
     *
     * @param string $route      The name of the route
     * @param mixed  $parameters An array of parameters
     *
     * @return string The generated path
     */
    public function path($route, $parameters = array())
    {
        return $this['url_generator']->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * Generates an absolute URL from the given parameters.
     *
     * @param string $route      The name of the route
     * @param mixed  $parameters An array of parameters
     *
     * @return string The generated URL
     */
    public function url($route, $parameters = array())
    {
        return $this['url_generator']->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
