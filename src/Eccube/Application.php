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

use Eccube\Application\ApplicationTrait;
use Eccube\Common\Constant;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class Application extends ApplicationTrait
{
    protected static $instance;

    protected $initialized = false;
    protected $initializedPlugin = false;

    public static function getInstance(array $values = array())
    {
        if (!is_object(self::$instance)) {
            self::$instance = new Application($values);
        }

        return self::$instance;
    }

    public static function clearInstance()
    {
        self::$instance = null;
    }

    final public function __clone()
    {
        throw new \Exception('Clone is not allowed against '.get_class($this));
    }

    public function __construct(array $values = array())
    {
        parent::__construct($values);

        if (is_null(self::$instance)) {
            self::$instance = $this;
        }

        // load config
        $this->initConfig();

        // init monolog
        $this->initLogger();
    }

    public function initConfig()
    {
        // load config
        $this['config'] = $this->share(function() {
            $ymlPath = __DIR__.'/../../app/config/eccube';
            $distPath = __DIR__.'/../../src/Eccube/Resource/config';

            $config = array();
            $config_yml = $ymlPath.'/config.yml';
            if (file_exists($config_yml)) {
                $config = Yaml::parse(file_get_contents($config_yml));
            }

            $config_dist = array();
            $config_yml_dist = $distPath.'/config.yml.dist';
            if (file_exists($config_yml_dist)) {
                $config_dist = Yaml::parse(file_get_contents($config_yml_dist));
            }

            $config_path = array();
            $path_yml = $ymlPath.'/path.yml';
            if (file_exists($path_yml)) {
                $config_path = Yaml::parse(file_get_contents($path_yml));
            }

            $config_constant = array();
            $constant_yml = $ymlPath.'/constant.yml';
            if (file_exists($constant_yml)) {
                $config_constant = Yaml::parse(file_get_contents($constant_yml));
                $config_constant = empty($config_constant) ? array() : $config_constant;
            }

            $config_constant_dist = array();
            $constant_yml_dist = $distPath.'/constant.yml.dist';
            if (file_exists($constant_yml_dist)) {
                $config_constant_dist = Yaml::parse(file_get_contents($constant_yml_dist));
            }

            $configAll = array_replace_recursive($config_constant_dist, $config_dist, $config_constant, $config_path, $config);

            $database = array();
            $yml = $ymlPath.'/database.yml';
            if (file_exists($yml)) {
                $database = Yaml::parse(file_get_contents($yml));
            }

            $mail = array();
            $yml = $ymlPath.'/mail.yml';
            if (file_exists($yml)) {
                $mail = Yaml::parse(file_get_contents($yml));
            }
            $configAll = array_replace_recursive($configAll, $database, $mail);

            $config_log = array();
            $yml = $ymlPath.'/log.yml';
            if (file_exists($yml)) {
                $config_log = Yaml::parse(file_get_contents($yml));
            }
            $config_log_dist = array();
            $log_yml_dist = $distPath.'/log.yml.dist';
            if (file_exists($log_yml_dist)) {
                $config_log_dist = Yaml::parse(file_get_contents($log_yml_dist));
            }

            $configAll = array_replace_recursive($configAll, $config_log_dist, $config_log);

            $config_nav = array();
            $yml = $ymlPath.'/nav.yml';
            if (file_exists($yml)) {
                $config_nav = array('nav' => Yaml::parse(file_get_contents($yml)));
            }
            $config_nav_dist = array();
            $nav_yml_dist = $distPath.'/nav.yml.dist';
            if (file_exists($nav_yml_dist)) {
                $config_nav_dist = array('nav' => Yaml::parse(file_get_contents($nav_yml_dist)));
            }

            $configAll = array_replace_recursive($configAll, $config_nav_dist, $config_nav);

            return $configAll;
        });
    }

    public function initLogger()
    {
        $app = $this;
        $this->register(new ServiceProvider\EccubeMonologServiceProvider($app));
        $this['monolog.logfile'] = __DIR__.'/../../app/log/site.log';
        $this['monolog.name'] = 'eccube';
    }

    public function initialize()
    {
        if ($this->initialized) {
            return;
        }

        // init locale
        $this->initLocale();

        // init session
        $this->initSession();

        // init twig
        $this->initRendering();

        // init provider
        $this->register(new \Silex\Provider\HttpFragmentServiceProvider());
        $this->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        $this->register(new \Silex\Provider\FormServiceProvider());
        $this->register(new \Silex\Provider\SerializerServiceProvider());
        $this->register(new \Eccube\ServiceProvider\ValidatorServiceProvider());

        $app = $this;
        $this->error(function(\Exception $e, $code) use ($app) {
            if ($app['debug']) {
                return;
            }

            switch ($code) {
                case 403:
                    $title = 'アクセスできません。';
                    $message = 'お探しのページはアクセスができない状況にあるか、移動もしくは削除された可能性があります。';
                    break;
                case 404:
                    $title = 'ページがみつかりません。';
                    $message = 'URLに間違いがないかご確認ください。';
                    break;
                default:
                    $title = 'システムエラーが発生しました。';
                    $message = '大変お手数ですが、サイト管理者までご連絡ください。';
                    break;
            }

            return $app->render('error.twig', array(
                'error_title' => $title,
                'error_message' => $message,
            ));
        });

        // init mailer
        $this->initMailer();

        // init doctrine orm
        $this->initDoctrine();

        // Set up the DBAL connection now to check for a proper connection to the database.
        $this->checkDatabaseConnection();

        // init security
        $this->initSecurity();

        // init ec-cube service provider
        $this->register(new ServiceProvider\EccubeServiceProvider());

        // mount controllers
        $this->register(new \Silex\Provider\ServiceControllerServiceProvider());
        $this->mount('', new ControllerProvider\FrontControllerProvider());
        $this->mount('/'.trim($this['config']['admin_route'], '/').'/', new ControllerProvider\AdminControllerProvider());
        Request::enableHttpMethodParameterOverride(); // PUTやDELETEできるようにする

        $this->initialized = true;
    }

    public function initLocale()
    {

        // timezone
        if (!empty($this['config']['timezone'])) {
            date_default_timezone_set($this['config']['timezone']);
        }

        $this->register(new \Silex\Provider\TranslationServiceProvider(), array(
            'locale' => $this['config']['locale'],
        ));
        $this['translator'] = $this->share($this->extend('translator', function($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());

            $r = new \ReflectionClass('Symfony\Component\Validator\Validator');
            $file = dirname($r->getFilename()).'/Resources/translations/validators.'.$app['locale'].'.xlf';
            if (file_exists($file)) {
                $translator->addResource('xliff', $file, $app['locale'], 'validators');
            }

            $file = __DIR__.'/Resource/locale/validator.'.$app['locale'].'.yml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale'], 'validators');
            }

            $file = __DIR__.'/Resource/locale/message.'.$app['locale'].'.yml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale']);
            }

            return $translator;
        }));
    }

    public function initSession()
    {
        $this->register(new \Silex\Provider\SessionServiceProvider(), array(
            'session.storage.save_path' => $this['config']['root_dir'].'/app/cache/eccube/session',
            'session.storage.options' => array(
                'name' => 'eccube',
                'cookie_path' => $this['config']['root_urlpath'] ?: '/',
                'cookie_secure' => $this['config']['force_ssl'],
                'cookie_lifetime' => $this['config']['cookie_lifetime'],
                'cookie_httponly' => true,
                // cookie_domainは指定しない
                // http://blog.tokumaru.org/2011/10/cookiedomain.html
            ),
        ));
    }

    public function initRendering()
    {
        $this->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.form.templates' => array('Form/form_layout.twig'),
        ));
        $this['twig'] = $this->share($this->extend('twig', function(\Twig_Environment $twig, \Silex\Application $app) {
            $twig->addExtension(new \Eccube\Twig\Extension\EccubeExtension($app));
            $twig->addExtension(new \Twig_Extension_StringLoader());

            return $twig;
        }));

        $this->before(function(Request $request, \Silex\Application $app) {
            // フロント or 管理画面ごとにtwigの探索パスを切り替える.
            $app['twig'] = $app->share($app->extend('twig', function(\Twig_Environment $twig, \Silex\Application $app) {
                $paths = array();

                // 互換性がないのでprofiler とproduction 時のcacheを分離する

                $app['admin'] = false;
                $app['front'] = false;

                if (isset($app['profiler'])) {
                    $cacheBaseDir = __DIR__.'/../../app/cache/twig/profiler/';
                } else {
                    $cacheBaseDir = __DIR__.'/../../app/cache/twig/production/';
                }
                $pathinfo = rawurldecode($app['request']->getPathInfo());
                if (strpos($pathinfo, '/'.trim($app['config']['admin_route'], '/')) === 0) {
                    if (file_exists(__DIR__.'/../../app/template/admin')) {
                        $paths[] = __DIR__.'/../../app/template/admin';
                    }
                    $paths[] = $app['config']['template_admin_realdir'];
                    $paths[] = __DIR__.'/../../app/Plugin';
                    $cache = $cacheBaseDir.'admin';
                    $app['admin'] = true;
                } else {
                    if (file_exists($app['config']['template_realdir'])) {
                        $paths[] = $app['config']['template_realdir'];
                    }
                    $paths[] = $app['config']['template_default_realdir'];
                    $paths[] = __DIR__.'/../../app/Plugin';
                    $cache = $cacheBaseDir.$app['config']['template_code'];
                    $app['front'] = true;
                }
                $twig->setCache($cache);
                $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem($paths));

                return $twig;
            }));

            // 管理画面のIP制限チェック.
            $pathinfo = rawurldecode($app['request']->getPathInfo());
            if (strpos($pathinfo, '/'.trim($app['config']['admin_route'], '/')) === 0) {
                // IP制限チェック
                $allowHost = $app['config']['admin_allow_host'];
                if (count($allowHost) > 0) {
                    if (array_search($app['request']->getClientIp(), $allowHost) === false) {
                        throw new \Exception();
                    }
                }
            }
        }, self::EARLY_EVENT);

        // twigのグローバル変数を定義.
        $app = $this;
        $this->on(\Symfony\Component\HttpKernel\KernelEvents::CONTROLLER, function(\Symfony\Component\HttpKernel\Event\FilterControllerEvent $event) use ($app) {
            // ショップ基本情報
            $BaseInfo = $app['eccube.repository.base_info']->get();
            $app['twig']->addGlobal('BaseInfo', $BaseInfo);

            $pathinfo = rawurldecode($app['request']->getPathInfo());
            if (strpos($pathinfo, '/'.trim($app['config']['admin_route'], '/')) === 0) {
                // 管理画面
                // 管理画面メニュー
                $menus = array('', '', '');
                $app['twig']->addGlobal('menus', $menus);

                $Member = $app->user();
                if (is_object($Member)) {
                    // ログインしていれば管理者のロールを取得
                    $AuthorityRoles = $app['eccube.repository.authority_role']->findBy(array('Authority' => $Member->getAuthority()));

                    $roles = array();
                    foreach ($AuthorityRoles as $AuthorityRole) {
                        // 管理画面でメニュー制御するため相対パス全てをセット
                        $roles[] = $app['request']->getBaseUrl().'/'.$app['config']['admin_route'].$AuthorityRole->getDenyUrl();
                    }

                    $app['twig']->addGlobal('AuthorityRoles', $roles);
                }

            } else {
                // フロント画面
                $request = $event->getRequest();
                $route = $request->attributes->get('_route');

                // ユーザ作成画面
                if ($route === trim($app['config']['user_data_route'])) {
                    $params = $request->attributes->get('_route_params');
                    $route = $params['route'];
                    // プレビュー画面
                } elseif ($request->get('preview')) {
                    $route = 'preview';
                }

                try {
                    $DeviceType = $app['eccube.repository.master.device_type']
                        ->find(\Eccube\Entity\Master\DeviceType::DEVICE_TYPE_PC);
                    $PageLayout = $app['eccube.repository.page_layout']->getByUrl($DeviceType, $route);
                } catch (\Doctrine\ORM\NoResultException $e) {
                    $PageLayout = $app['eccube.repository.page_layout']->newPageLayout($DeviceType);
                }

                $app['twig']->addGlobal('PageLayout', $PageLayout);
                $app['twig']->addGlobal('title', $PageLayout->getName());
            }
        });
    }

    public function initMailer()
    {

        // メール送信時の文字エンコード指定(デフォルトはUTF-8)
        if (isset($this['config']['mail']['charset_iso_2022_jp']) && is_bool($this['config']['mail']['charset_iso_2022_jp'])) {
            if ($this['config']['mail']['charset_iso_2022_jp'] === true) {
                \Swift::init(function() {
                    \Swift_DependencyContainer::getInstance()
                        ->register('mime.qpheaderencoder')
                        ->asAliasOf('mime.base64headerencoder');
                    \Swift_Preferences::getInstance()->setCharset('iso-2022-jp');
                });
            }
        }

        $this->register(new \Silex\Provider\SwiftmailerServiceProvider());
        $this['swiftmailer.options'] = $this['config']['mail'];

        if (isset($this['config']['mail']['spool']) && is_bool($this['config']['mail']['spool'])) {
            $this['swiftmailer.use_spool'] = $this['config']['mail']['spool'];
        }
        // デフォルトはsmtpを使用
        $transport = $this['config']['mail']['transport'];
        if ($transport == 'sendmail') {
            $this['swiftmailer.transport'] = \Swift_SendmailTransport::newInstance();
        } elseif ($transport == 'mail') {
            $this['swiftmailer.transport'] = \Swift_MailTransport::newInstance();
        }
    }

    public function initDoctrine()
    {
        $this->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'dbs.options' => array(
                'default' => $this['config']['database']
        )));
        $this->register(new \Saxulum\DoctrineOrmManagerRegistry\Silex\Provider\DoctrineOrmManagerRegistryProvider());

        // プラグインのmetadata定義を合わせて行う.
        $pluginBasePath = __DIR__.'/../../app/Plugin';
        $finder = Finder::create()
            ->in($pluginBasePath)
            ->directories()
            ->depth(0);

        $ormMappings = array();
        $ormMappings[] = array(
            'type' => 'yml',
            'namespace' => 'Eccube\Entity',
            'path' => array(
                __DIR__.'/Resource/doctrine',
                __DIR__.'/Resource/doctrine/master',
            ),
        );

        foreach ($finder as $dir) {

            $file = $dir->getRealPath().'/config.yml';

            if (file_exists($file)) {
                $config = Yaml::parse(file_get_contents($file));
            } else {
                $code = $dir->getBaseName();
                $this['monolog']->warning("skip {$code} orm.path loading. config.yml not found.", array('path' => $file));
                continue;
            }

            // Doctrine Extend
            if (isset($config['orm.path']) && is_array($config['orm.path'])) {
                $paths = array();
                foreach ($config['orm.path'] as $path) {
                    $paths[] = $pluginBasePath.'/'.$config['code'].$path;
                }
                $ormMappings[] = array(
                    'type' => 'yml',
                    'namespace' => 'Plugin\\'.$config['code'].'\\Entity',
                    'path' => $paths,
                );
            }
        }

        $this->register(new \Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), array(
            'orm.proxies_dir' => __DIR__.'/../../app/cache/doctrine',
            'orm.em.options' => array(
                'mappings' => $ormMappings,
            ),
        ));
    }

    public function initSecurity()
    {
        $this->register(new \Silex\Provider\SecurityServiceProvider());
        $this->register(new \Silex\Provider\RememberMeServiceProvider());

        $this['security.firewalls'] = array(
            'admin' => array(
                'pattern' => "^/{$this['config']['admin_route']}",
                'form' => array(
                    'login_path' => "/{$this['config']['admin_route']}/login",
                    'check_path' => "/{$this['config']['admin_route']}/login_check",
                    'username_parameter' => 'login_id',
                    'password_parameter' => 'password',
                    'with_csrf' => true,
                    'use_forward' => true,
                ),
                'logout' => array(
                    'logout_path' => "/{$this['config']['admin_route']}/logout",
                    'target_url' => "/{$this['config']['admin_route']}/",
                ),
                'users' => $this['orm.em']->getRepository('Eccube\Entity\Member'),
                'anonymous' => true,
            ),
            'customer' => array(
                'pattern' => '^/',
                'form' => array(
                    'login_path' => '/mypage/login',
                    'check_path' => '/login_check',
                    'username_parameter' => 'login_email',
                    'password_parameter' => 'login_pass',
                    'with_csrf' => true,
                    'use_forward' => true,
                ),
                'logout' => array(
                    'logout_path' => '/logout',
                    'target_url' => '/',
                ),
                'remember_me' => array(
                    'key' => sha1($this['config']['auth_magic']),
                    'name' => 'eccube_rememberme',
                    // lifetimeはデフォルトの1年間にする
                    // 'lifetime' => $this['config']['cookie_lifetime'],
                    'path' => $this['config']['root_urlpath'] ?: '/',
                    'secure' => $this['config']['force_ssl'],
                    'httponly' => true,
                    'always_remember_me' => false,
                    'remember_me_parameter' => 'login_memory',
                ),
                'users' => $this['orm.em']->getRepository('Eccube\Entity\Customer'),
                'anonymous' => true,
            ),
        );

        $this['security.access_rules'] = array(
            array("^/{$this['config']['admin_route']}/login", 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array("^/{$this['config']['admin_route']}", 'ROLE_ADMIN'),
            array('^/mypage/login', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/mypage/withdraw_complete', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/mypage/change', 'IS_AUTHENTICATED_FULLY'),
            array('^/mypage', 'ROLE_USER'),
        );

        $this['eccube.password_encoder'] = $this->share(function($app) {
            return new \Eccube\Security\Core\Encoder\PasswordEncoder($app['config']);
        });
        $this['security.encoder_factory'] = $this->share(function($app) {
            return new \Symfony\Component\Security\Core\Encoder\EncoderFactory(array(
                'Eccube\Entity\Customer' => $app['eccube.password_encoder'],
                'Eccube\Entity\Member' => $app['eccube.password_encoder'],
            ));
        });
        $this['eccube.event_listner.security'] = $this->share(function($app) {
            return new \Eccube\EventListener\SecurityEventListener($app['orm.em']);
        });
        $this['user'] = function($app) {
            $token = $app['security']->getToken();

            return ($token !== null) ? $token->getUser() : null;
        };

        // ログイン時のイベントを設定.
        $this['dispatcher']->addListener(\Symfony\Component\Security\Http\SecurityEvents::INTERACTIVE_LOGIN, array($this['eccube.event_listner.security'], 'onInteractiveLogin'));

        // Voterの設定
        $app = $this;
        $this['authority_voter'] = $this->share(function($app) {
            return new \Eccube\Security\Voter\AuthorityVoter($app);
        });

        $app['security.voters'] = $app->extend('security.voters', function($voters) use ($app) {
            $voters[] = $app['authority_voter'];

            return $voters;
        });

        $this['security.access_manager'] = $this->share(function($app) {
            return new \Symfony\Component\Security\Core\Authorization\AccessDecisionManager($app['security.voters'], 'unanimous');
        });

    }

    public function initializePlugin()
    {
        if ($this->initializedPlugin) {
            return;
        }

        // setup event dispatcher
        $this->initPluginEventDispatcher();

        // load plugin
        $this->loadPlugin();

        $this->initializedPlugin = true;
    }

    public function initPluginEventDispatcher()
    {
        // EventDispatcher
        $this['eccube.event.dispatcher'] = $this->share(function() {
            return new EventDispatcher();
        });

        // hook point
        $this->before(function(Request $request, \Silex\Application $app) {
            $app['eccube.event.dispatcher']->dispatch('eccube.event.app.before');
        }, self::EARLY_EVENT);

        $this->before(function(Request $request, \Silex\Application $app) {
            $event = 'eccube.event.controller.'.$request->attributes->get('_route').'.before';
            $app['eccube.event.dispatcher']->dispatch($event);
        });

        $this->after(function(Request $request, Response $response, \Silex\Application $app) {
            $event = 'eccube.event.controller.'.$request->attributes->get('_route').'.after';
            $app['eccube.event.dispatcher']->dispatch($event);
        });

        $this->after(function(Request $request, Response $response, \Silex\Application $app) {
            $app['eccube.event.dispatcher']->dispatch('eccube.event.app.after');
        }, self::LATE_EVENT);

        $this->finish(function(Request $request, Response $response, \Silex\Application $app) {
            $event = 'eccube.event.controller.'.$request->attributes->get('_route').'.finish';
            $app['eccube.event.dispatcher']->dispatch($event);
        });

        $app = $this;
        $this->on(\Symfony\Component\HttpKernel\KernelEvents::RESPONSE, function(\Symfony\Component\HttpKernel\Event\FilterResponseEvent $event) use ($app) {
            $route = $event->getRequest()->attributes->get('_route');
            $app['eccube.event.dispatcher']->dispatch('eccube.event.render.'.$route.'.before', $event);
        });

        // Request Event
        $this->on(\Symfony\Component\HttpKernel\KernelEvents::REQUEST, function(\Symfony\Component\HttpKernel\Event\GetResponseEvent $event) use ($app) {

            if (\Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
                return;
            }

            $route = $event->getRequest()->attributes->get('_route');

            if (is_null($route)) {
                return;
            }

            $app['monolog']->debug('KernelEvents::REQUEST '.$route);

            // 全体
            $app['eccube.event.dispatcher']->dispatch('eccube.event.app.request', $event);

            if (strpos($route, 'admin') === 0) {
                // 管理画面
                $app['eccube.event.dispatcher']->dispatch('eccube.event.admin.request', $event);
            } else {
                // フロント画面
                $app['eccube.event.dispatcher']->dispatch('eccube.event.front.request', $event);
            }

            // ルーティング単位
            $app['eccube.event.dispatcher']->dispatch("eccube.event.route.{$route}.request", $event);

        }, 30); // Routing(32)が解決しし, 認証判定(8)が実行される前のタイミング.

        // Controller Event
        $this->on(\Symfony\Component\HttpKernel\KernelEvents::CONTROLLER, function(\Symfony\Component\HttpKernel\Event\FilterControllerEvent $event) use ($app) {

            if (\Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
                return;
            }


            $route = $event->getRequest()->attributes->get('_route');

            if (is_null($route)) {
                return;
            }

            $app['monolog']->debug('KernelEvents::CONTROLLER '.$route);

            // 全体
            $app['eccube.event.dispatcher']->dispatch('eccube.event.app.controller', $event);

            if (strpos($route, 'admin') === 0) {
                // 管理画面
                $app['eccube.event.dispatcher']->dispatch('eccube.event.admin.controller', $event);
            } else {
                // フロント画面
                $app['eccube.event.dispatcher']->dispatch('eccube.event.front.controller', $event);
            }

            // ルーティング単位
            $app['eccube.event.dispatcher']->dispatch("eccube.event.route.{$route}.controller", $event);
        });

        // Response Event
        $this->on(\Symfony\Component\HttpKernel\KernelEvents::RESPONSE, function(\Symfony\Component\HttpKernel\Event\FilterResponseEvent $event) use ($app) {

            if (\Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
                return;
            }

            $route = $event->getRequest()->attributes->get('_route');

            if (is_null($route)) {
                return;
            }

            $app['monolog']->debug('KernelEvents::RESPONSE '.$route);

            // ルーティング単位
            $app['eccube.event.dispatcher']->dispatch("eccube.event.route.{$route}.response", $event);

            if (strpos($route, 'admin') === 0) {
                // 管理画面
                $app['eccube.event.dispatcher']->dispatch('eccube.event.admin.response', $event);
            } else {
                // フロント画面
                $app['eccube.event.dispatcher']->dispatch('eccube.event.front.response', $event);
            }

            // 全体
            $app['eccube.event.dispatcher']->dispatch('eccube.event.app.response', $event);
        });

        // Exception Event
        $this->on(\Symfony\Component\HttpKernel\KernelEvents::EXCEPTION, function(\Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event) use ($app) {

            if (\Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
                return;
            }

            $route = $event->getRequest()->attributes->get('_route');

            if (is_null($route)) {
                return;
            }

            $app['monolog']->debug('KernelEvents::EXCEPTION '.$route);

            // ルーティング単位
            $app['eccube.event.dispatcher']->dispatch("eccube.event.route.{$route}.exception", $event);

            if (strpos($route, 'admin') === 0) {
                // 管理画面
                $app['eccube.event.dispatcher']->dispatch('eccube.event.admin.exception', $event);
            } else {
                // フロント画面
                $app['eccube.event.dispatcher']->dispatch('eccube.event.front.exception', $event);
            }

            // 全体
            $app['eccube.event.dispatcher']->dispatch('eccube.event.app.exception', $event);
        });

        // Terminate Event
        $this->on(\Symfony\Component\HttpKernel\KernelEvents::TERMINATE, function(\Symfony\Component\HttpKernel\Event\PostResponseEvent $event) use ($app) {

            $route = $event->getRequest()->attributes->get('_route');

            if (is_null($route)) {
                return;
            }

            $app['monolog']->debug('KernelEvents::TERMINATE '.$route);

            // ルーティング単位
            $app['eccube.event.dispatcher']->dispatch("eccube.event.route.{$route}.terminate", $event);

            if (strpos($route, 'admin') === 0) {
                // 管理画面
                $app['eccube.event.dispatcher']->dispatch('eccube.event.admin.terminate', $event);
            } else {
                // フロント画面
                $app['eccube.event.dispatcher']->dispatch('eccube.event.front.terminate', $event);
            }

            // 全体
            $app['eccube.event.dispatcher']->dispatch('eccube.event.app.terminate', $event);
        });
    }

    public function loadPlugin()
    {
        // プラグインディレクトリを探索.
        $basePath = __DIR__.'/../../app/Plugin';
        $finder = Finder::create()
            ->in($basePath)
            ->directories()
            ->depth(0);

        $finder->sortByName();

        // ハンドラ優先順位をdbから持ってきてハッシュテーブルを作成
        $priorities = array();
        $handlers = $this['orm.em']
            ->getRepository('Eccube\Entity\PluginEventHandler')
            ->getHandlers();
        foreach ($handlers as $handler) {
            if ($handler->getPlugin()->getEnable() && !$handler->getPlugin()->getDelFlg()) {

                $priority = $handler->getPriority();
            } else {
                // Pluginがdisable、削除済みの場合、EventHandlerのPriorityを全て0とみなす
                $priority = \Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_DISABLED;
            }
            $priorities[$handler->getPlugin()->getClassName()][$handler->getEvent()][$handler->getHandler()] = $priority;
        }

        // プラグインをロードする.
        // config.yml/event.ymlの定義に沿ってインスタンスの生成を行い, イベント設定を行う.
        foreach ($finder as $dir) {
            //config.ymlのないディレクトリは無視する
            $path = $dir->getRealPath();
            $code = $dir->getBaseName();
            try {
                $this['eccube.service.plugin']->checkPluginArchiveContent($path);
            } catch (\Eccube\Exception\PluginException $e) {
                $this['monolog']->warning("skip {$code} config loading. config.yml not foud or invalid.", array(
                    'path' =>  $path,
                    'original-message' => $e->getMessage()
                ));
                continue;
            }
            $config = $this['eccube.service.plugin']->readYml($dir->getRealPath().'/config.yml');

            $plugin = $this['orm.em']
                ->getRepository('Eccube\Entity\Plugin')
                ->findOneBy(array('code' => $config['code']));

            // const
            if (isset($config['const'])) {
                $this['config'] = $this->share($this->extend('config', function($eccubeConfig) use ($config) {
                    $eccubeConfig[$config['code']] = array(
                        'const' => $config['const'],
                    );

                    return $eccubeConfig;
                }));
            }

            if ($plugin && $plugin->getEnable() == Constant::DISABLED) {
                // プラグインが無効化されていれば読み込まない
                continue;
            }

            // Type: Event
            if (isset($config['event'])) {
                $class = '\\Plugin\\'.$config['code'].'\\'.$config['event'];
                $eventExists = true;

                if (!class_exists($class)) {
                    $this['monolog']->warning("skip {$code} loading. event class not foud.", array(
                        'class' =>  $class,
                    ));
                    $eventExists = false;
                }

                if ($eventExists && file_exists($dir->getRealPath().'/event.yml')) {

                    $subscriber = new $class($this);

                    foreach (Yaml::parse(file_get_contents($dir->getRealPath().'/event.yml')) as $event => $handlers) {
                        foreach ($handlers as $handler) {
                            if (!isset($priorities[$config['event']][$event][$handler[0]])) { // ハンドラテーブルに登録されていない（ソースにしか記述されていない)ハンドラは一番後ろにする
                                $priority = \Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_LATEST;
                            } else {
                                $priority = $priorities[$config['event']][$event][$handler[0]];
                            }
                            // 優先度が0のプラグインは登録しない
                            if (\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_DISABLED != $priority) {
                                $this['eccube.event.dispatcher']->addListener($event, array($subscriber, $handler[0]), $priority);
                            }
                        }
                    }
                }
            }
            // Type: ServiceProvider
            if (isset($config['service'])) {
                foreach ($config['service'] as $service) {
                    $class = '\\Plugin\\'.$config['code'].'\\ServiceProvider\\'.$service;
                    if (!class_exists($class)) {
                        $this['monolog']->warning("skip {$code} loading. service provider class not foud.", array(
                            'class' =>  $class,
                        ));
                        continue;
                    }
                    $this->register(new $class($this));
                }
            }
        }
    }

    /**
     *
     * データベースの接続を確認
     * 成功 : trueを返却
     *　失敗 : \Doctrine\DBAL\DBALExceptionエラーが発生( 接続に失敗した場合 )、エラー画面を表示しdie()
     * 備考 : app['debug']がtrueの際は処理を行わない
     * @return boolean true
     *
     */
    protected function checkDatabaseConnection()
    {
        if ($this['debug']) {
            return;
        }
        try {
            $this['db']->connect();
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this['monolog']->error($e->getMessage());
            $this['twig.path'] = array(__DIR__.'/Resource/template/exception');
            $html = $this['twig']->render('error.twig', array(
                'error_title' => 'データーベース接続エラー',
                'error_message' => 'データーベースを確認してください',
            ));
            $response = new Response();
            $response->setContent($html);
            $response->setStatusCode('500');
            $response->headers->set('Content-Type', 'text/html');
            $response->send();
            die();
        }
        return true;
    }
}
