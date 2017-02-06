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
use Eccube\Doctrine\ORM\Mapping\Driver\YamlDriver;
use Eccube\EventListener\TransactionListener;
use Eccube\Plugin\ConfigManager as PluginConfigManager;
use Sergiors\Silex\Provider\AnnotationsServiceProvider;
use Sergiors\Silex\Provider\DoctrineCacheServiceProvider;
use Sergiors\Silex\Provider\RoutingServiceProvider;
use Sergiors\Silex\Provider\SensioFrameworkExtraServiceProvider;
use Sergiors\Silex\Provider\TemplatingServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Yaml\Yaml;

class Application extends \Silex\Application
{
    use \Silex\Application\FormTrait;
    use \Silex\Application\UrlGeneratorTrait;
    use \Silex\Application\MonologTrait;
    use \Silex\Application\SwiftmailerTrait;
    use \Silex\Application\SecurityTrait;
    use \Silex\Application\TranslationTrait;
    use \Eccube\Application\ApplicationTrait;
    use \Eccube\Application\SecurityTrait;
    use \Eccube\Application\TwigTrait;

    protected static $instance;

    protected $initialized = false;
    protected $initializedPlugin = false;
    protected $testMode = false;

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

    /**
     * Application::runが実行されているか親クラスのプロパティから判定
     *
     * @return bool
     */
    public function isBooted()
    {
        return $this->booted;
    }

    public function initConfig()
    {
        // load config
        $this['config'] = function() {
            $configAll = array();
            $this->parseConfig('constant', $configAll)
                ->parseConfig('path', $configAll)
                ->parseConfig('config', $configAll)
                ->parseConfig('database', $configAll)
                ->parseConfig('mail', $configAll)
                ->parseConfig('log', $configAll)
                ->parseConfig('nav', $configAll, true)
                ->parseConfig('doctrine_cache', $configAll)
                ->parseConfig('http_cache', $configAll)
                ->parseConfig('session_handler', $configAll);

            return $configAll;
        };
    }

    public function initLogger()
    {
        $app = $this;
        $this->register(new ServiceProvider\LogServiceProvider($app));
    }

    public function initialize()
    {
        if ($this->initialized) {
            return;
        }

        // init locale
        $this->initLocale();

        // init session
        if (!$this->isSessionStarted()) {
            $this->initSession();
        }

        // init twig
        $this->initRendering();

        // init provider
        $this->register(new \Silex\Provider\HttpCacheServiceProvider(), array(
            'http_cache.cache_dir' => __DIR__.'/../../app/cache/http/',
        ));
        $this->register(new \Silex\Provider\HttpFragmentServiceProvider());
        $this->register(new \Silex\Provider\RoutingServiceProvider());
        $this->register(new \Silex\Provider\FormServiceProvider());
        $this->register(new \Silex\Provider\SerializerServiceProvider());
        $this->register(new \Silex\Provider\ValidatorServiceProvider());

        $this->error(function (\Exception $e, Request $request, $code) {
            if ($this['debug']) {
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

            return $this->render('error.twig', array(
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

        $this->register(new \Sergiors\Silex\Provider\RoutingServiceProvider());
        $this->register(new \Sergiors\Silex\Provider\DoctrineCacheServiceProvider());
        $this->register(new \Sergiors\Silex\Provider\TemplatingServiceProvider());
        $this->register(new \Sergiors\Silex\Provider\AnnotationsServiceProvider());
        $this->register(new \Sergiors\Silex\Provider\SensioFrameworkExtraServiceProvider(),
        [
            'request' => [
                'auto_convert' => true
            ]
        ]);

        // init ec-cube service provider
        $this->register(new ServiceProvider\EccubeServiceProvider());

        // mount controllers
        $this->register(new \Silex\Provider\ServiceControllerServiceProvider());
        $this->mount('', new ControllerProvider\FrontControllerProvider());
        $this->mount('/'.trim($this['config']['admin_route'], '/').'/', new ControllerProvider\AdminControllerProvider());
        Request::enableHttpMethodParameterOverride(); // PUTやDELETEできるようにする

        if (php_sapi_name() !== 'cli') {
            $this->extend('routes', function ($routes, \Silex\Application $app) {
                $loader = $this['sensio_framework_extra.routing.loader.annot_dir'];
                $collection = $loader->load(__DIR__.'/Controller');
                $extends = $loader->load($this['config']['root_dir'].'/app/Eccube');
                $collection->addCollection($extends);

                return $collection;
            });
        }

        // init http cache
        $this->initCacheRequest();

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
            'translator.cache_dir' => $this['debug'] ? null : $this['config']['root_dir'].'/app/cache/translator',
            'locale_fallbacks' => ['ja', 'en'],
        ));
        $this->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());

            $file = __DIR__.'/Resource/locale/validator.'.$app['locale'].'.yml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale'], 'validators');
            }

            $file = __DIR__.'/Resource/locale/message.'.$app['locale'].'.yml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale']);
            }

            return $translator;
        });
    }

    public function initSession()
    {
        $this->register(new \Silex\Provider\SessionServiceProvider(), array(
            'session.storage.save_path' => $this['config']['root_dir'].'/app/cache/eccube/session',
            'session.storage.options' => array(
                'name' => $this['config']['cookie_name'],
                'cookie_path' => $this['config']['root_urlpath'] ?: '/',
                'cookie_secure' => $this['config']['force_ssl'],
                'cookie_lifetime' => $this['config']['cookie_lifetime'],
                'cookie_httponly' => true,
                // cookie_domainは指定しない
                // http://blog.tokumaru.org/2011/10/cookiedomain.html
            ),
        ));

        $options = $this['config']['session_handler'];

        if ($options['enabled']) {
            // @see http://silex.sensiolabs.org/doc/providers/session.html#custom-session-configurations
            $this['session.storage.handler'] = null;
            ini_set('session.save_handler', $options['save_handler']);
            ini_set('session.save_path', $options['save_path']);
        }
    }

    public function initRendering()
    {
        $this->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.form.templates' => array('Form/form_layout.twig'),
        ));
        $this->extend('twig', function (\Twig_Environment $twig, \Silex\Application $app) {
            $twig->addExtension(new \Eccube\Twig\Extension\EccubeExtension($app));
            $twig->addExtension(new \Twig_Extension_StringLoader());

            return $twig;
        });

        $this->before(function (Request $request, \Silex\Application $app) {
            $app['admin'] = false;
            $app['front'] = false;
            $pathinfo = rawurldecode($request->getPathInfo());
            if (strpos($pathinfo, '/'.trim($app['config']['admin_route'], '/').'/') === 0) {
                $app['admin'] = true;
            } else {
                $app['front'] = true;
            }
            if ($app->isAdminRequest()) {
                if (file_exists(__DIR__.'/../../app/template/admin')) {
                    $paths[] = __DIR__.'/../../app/template/admin';
                }
                $paths[] = $app['config']['template_admin_realdir'];
                $paths[] = __DIR__.'/../../app/Plugin';
                // $cache = $cacheBaseDir.'admin';

            } else {
                if (file_exists($app['config']['template_realdir'])) {
                    $paths[] = $app['config']['template_realdir'];
                }
                $paths[] = $app['config']['template_default_realdir'];
                $paths[] = __DIR__.'/../../app/Plugin';
                // $cache = $cacheBaseDir.$app['config']['template_code'];
                $app['front'] = true;
            }
            // $twig->setCache($cache);
            $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem($paths));

            // フロント or 管理画面ごとにtwigの探索パスを切り替える.
            if (!$app->offsetExists('twig')) {

                $app->extend('twig', function (\Twig_Environment $twig, \Silex\Application $app) {
                        $paths = array();

                        // 互換性がないのでprofiler とproduction 時のcacheを分離する
                        if (isset($app['profiler'])) {
                            $cacheBaseDir = __DIR__.'/../../app/cache/twig/profiler/';
                        } else {
                            $cacheBaseDir = __DIR__.'/../../app/cache/twig/production/';
                        }

                        if ($app->isAdminRequest()) {
                            if (file_exists(__DIR__.'/../../app/template/admin')) {
                                $paths[] = __DIR__.'/../../app/template/admin';
                            }
                            $paths[] = $app['config']['template_admin_realdir'];
                            $paths[] = __DIR__.'/../../app/Plugin';
                            $cache = $cacheBaseDir.'admin';

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
                    }
                );
            }
            // 管理画面のIP制限チェック.
            if ($app->isAdminRequest()) {
                // IP制限チェック
                $allowHost = $app['config']['admin_allow_host'];
                if (count($allowHost) > 0) {
                    if (array_search($app['request']->getClientIp(), $allowHost) === false) {
                        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
                    }
                }
            }
        }, self::EARLY_EVENT);

        // twigのグローバル変数を定義.
        $this->on(\Symfony\Component\HttpKernel\KernelEvents::CONTROLLER, function (\Symfony\Component\HttpKernel\Event\FilterControllerEvent $event) {
            // TODO ログイン時のイベントを設定.
            $this['dispatcher']->addListener(\Symfony\Component\Security\Http\SecurityEvents::INTERACTIVE_LOGIN, array($this['eccube.event_listner.security'], 'onInteractiveLogin'));
            // 未ログイン時にマイページや管理画面以下にアクセスするとSubRequestで実行されるため,
            // $event->isMasterRequest()ではなく、グローバル変数が初期化済かどうかの判定を行う
            if (isset($this['twig_global_initialized']) && $this['twig_global_initialized'] === true) {
                return;
            }
            // ショップ基本情報
            $BaseInfo = $this['eccube.repository.base_info']->get();
            $this['twig']->addGlobal('BaseInfo', $BaseInfo);

            if ($this->isAdminRequest()) {
                // 管理画面
                // 管理画面メニュー
                $menus = array('', '', '');
                $this['twig']->addGlobal('menus', $menus);

                $Member = $this->user();
                if (is_object($Member)) {
                    // ログインしていれば管理者のロールを取得
                    $AuthorityRoles = $this['eccube.repository.authority_role']->findBy(array('Authority' => $Member->getAuthority()));

                    $roles = array();
                    foreach ($AuthorityRoles as $AuthorityRole) {
                        // 管理画面でメニュー制御するため相対パス全てをセット
                        $roles[] = $this['request']->getBaseUrl().'/'.$this['config']['admin_route'].$AuthorityRole->getDenyUrl();
                    }

                    $this['twig']->addGlobal('AuthorityRoles', $roles);
                }

            } else {
                // フロント画面
                $request = $event->getRequest();
                $route = $request->attributes->get('_route');

                // ユーザ作成画面
                if ($route === 'user_data') {
                    $params = $request->attributes->get('_route_params');
                    $route = $params['route'];
                    // プレビュー画面
                } elseif ($request->get('preview')) {
                    $route = 'preview';
                }

                try {
                    $DeviceType = $this['eccube.repository.master.device_type']
                        ->find(\Eccube\Entity\Master\DeviceType::DEVICE_TYPE_PC);
                    $PageLayout = $this['eccube.repository.page_layout']->getByUrl($DeviceType, $route);
                } catch (\Doctrine\ORM\NoResultException $e) {
                    $PageLayout = $this['eccube.repository.page_layout']->newPageLayout($DeviceType);
                }

                $this['twig']->addGlobal('PageLayout', $PageLayout);
                $this['twig']->addGlobal('title', $PageLayout->getName());
            }

            $this['twig_global_initialized'] = true;
        });
    }

    public function initMailer()
    {

        // メール送信時の文字エンコード指定(デフォルトはUTF-8)
        if (isset($this['config']['mail']['charset_iso_2022_jp']) && is_bool($this['config']['mail']['charset_iso_2022_jp'])) {
            if ($this['config']['mail']['charset_iso_2022_jp'] === true) {
                \Swift::init(function () {
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
        if (!$this->offsetExists('dbs')) {
            $this->register(new \Silex\Provider\DoctrineServiceProvider(), array(
                'dbs.options' => array(
                    'default' => $this['config']['database']
                )
            ));
        }
        if (!$this->offsetExists('orm.ems')) {
            $this->register(new \Saxulum\DoctrineOrmManagerRegistry\Provider\DoctrineOrmManagerRegistryProvider());
        }

        // プラグインのmetadata定義を合わせて行う.
        $pluginConfigs = PluginConfigManager::getPluginConfigAll($this['debug']);
        $ormMappings = array();
        $ormMappings[] = array(
            'type' => 'yml',
            'namespace' => 'Eccube\Entity',
            'path' => array(
                __DIR__.'/Resource/doctrine',
                __DIR__.'/Resource/doctrine/master',
            ),
        );
        // ここを有効にすると本体の Entity でもアノテーションが使える
        // が、 Yaml との共存はできない模様...
        // $ormMappings[] = array(
        //     'type' => 'annotation',
        //     'namespace' => 'Eccube\Entity',
        //     'path' => array(
        //         __DIR__.'/Entity',
        //         __DIR__.'/Entity/master',
        //     ),
        //     'use_simple_annotation_reader' => false,
        // );

        // ここを有効にすると本体の Entity でもアノテーションが使える
        // が、 Yaml との共存はできない模様...
        $ormMappings[] = array(
            'type' => 'annotation',
            'namespace' => 'Eccube2\Entity',
            'path' => array(
                __DIR__.'/../../app/Eccube/Entity',
            ),
            'use_simple_annotation_reader' => false,
        );

        foreach ($pluginConfigs as $code) {
            $config = $code['config'];
            // Doctrine Extend
            if (isset($config['orm.path']) && is_array($config['orm.path'])) {
                $paths = array();
                foreach ($config['orm.path'] as $path) {
                    $paths[] = $this['config']['plugin_realdir'].'/'.$config['code'].$path;
                }
                $ormMappings[] = array(
                    'type' => 'yml',
                    'namespace' => 'Plugin\\'.$config['code'].'\\Entity',
                    'path' => $paths,
                );
                $ormMappings[] = array(
                    'type' => 'annotation',
                    'namespace' => 'Plugin\\'.$config['code'].'\\Entity',
                    'path' => $paths,
                    'use_simple_annotation_reader' => false,
                );
            }
        }

        $options = array(
            'mappings' => $ormMappings
        );

        if (!$this['debug']) {
            $cacheDrivers = array();
            if (array_key_exists('doctrine_cache', $this['config'])) {
                $cacheDrivers = $this['config']['doctrine_cache'];
            }

            if (array_key_exists('metadata_cache', $cacheDrivers)) {
                $options['metadata_cache'] = $cacheDrivers['metadata_cache'];
            }
            if (array_key_exists('query_cache', $cacheDrivers)) {
                $options['query_cache'] = $cacheDrivers['query_cache'];
            }
            if (array_key_exists('result_cache', $cacheDrivers)) {
                $options['result_cache'] = $cacheDrivers['result_cache'];
            }
            if (array_key_exists('hydration_cache', $cacheDrivers)) {
                $options['hydration_cache'] = $cacheDrivers['hydration_cache'];
            }
        }

        if (!$this->offsetExists('orm.ems')) {
            $this->register(new \Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), array(
                'orm.proxies_dir' => __DIR__.'/../../app/cache/doctrine/proxies',
                'orm.em.options' => $options,
                'orm.custom.functions.string' => array(
                    'NORMALIZE' => 'Eccube\Doctrine\ORM\Query\Normalize',
                ),
                'orm.custom.functions.numeric' => array(
                    'EXTRACT' => 'Eccube\Doctrine\ORM\Query\Extract',
                ),
            ));
            $this->extend('orm.em', function (\Doctrine\ORM\EntityManager $em, \Silex\Application $app) {
                // tax_rule
                $taxRuleRepository = $em->getRepository('Eccube\Entity\TaxRule');
                $taxRuleRepository->setApplication($app);
                $taxRuleService = new \Eccube\Service\TaxRuleService($taxRuleRepository);
                $em->getEventManager()->addEventSubscriber(new \Eccube\Doctrine\EventSubscriber\TaxRuleEventSubscriber($taxRuleService));

                // save
                $saveEventSubscriber = new \Eccube\Doctrine\EventSubscriber\SaveEventSubscriber($app);
                $em->getEventManager()->addEventSubscriber($saveEventSubscriber);

                // clear cache
                $clearCacheEventSubscriber = new \Eccube\Doctrine\EventSubscriber\ClearCacheEventSubscriber($app);
                $em->getEventManager()->addEventSubscriber($clearCacheEventSubscriber);

                // filters
                $config = $em->getConfiguration();
                $config->addFilter("soft_delete", '\Eccube\Doctrine\Filter\SoftDeleteFilter');
                $config->addFilter("nostock_hidden", '\Eccube\Doctrine\Filter\NoStockHiddenFilter');
                $config->addFilter("incomplete_order_status_hidden", '\Eccube\Doctrine\Filter\OrderStatusFilter');
                $em->getFilters()->enable('soft_delete');

                return $em;
            });
        }
    }

    public function initSecurity()
    {
        $this->register(new \Silex\Provider\SecurityServiceProvider());
        $this->register(new \Silex\Provider\CsrfServiceProvider());
        $this->register(new \Silex\Provider\RememberMeServiceProvider());

        $this['security.firewalls'] = array(
            'admin' => array(
                'pattern' => "^/{$this['config']['admin_route']}/",
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
                    'name' => $this['config']['cookie_name'].'_rememberme',
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
            array("^/{$this['config']['admin_route']}/", 'ROLE_ADMIN'),
            array('^/mypage/login', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/mypage/withdraw_complete', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/mypage/change', 'IS_AUTHENTICATED_FULLY'),
            array('^/mypage', 'ROLE_USER'),
        );

        $this['eccube.password_encoder'] = function ($app) {
            return new \Eccube\Security\Core\Encoder\PasswordEncoder($app['config']);
        };
        $this['security.encoder_factory'] = function ($app) {
            return new \Symfony\Component\Security\Core\Encoder\EncoderFactory(array(
                'Eccube\Entity\Customer' => $app['eccube.password_encoder'],
                'Eccube\Entity\Member' => $app['eccube.password_encoder'],
            ));
        };
        $this['eccube.event_listner.security'] = function ($app) {
            return new \Eccube\EventListener\SecurityEventListener($app['orm.em']);
        };

        // Voterの設定
        $this['authority_voter'] = function ($app) {
            return new \Eccube\Security\Voter\AuthorityVoter($app);
        };

        $this->extend('security.voters', function ($voters, \Silex\Application $app) {
            $voters[] = $app['authority_voter'];

            return $voters;
        });

        $this['security.access_manager'] = function ($app) {
            return new \Symfony\Component\Security\Core\Authorization\AccessDecisionManager($app['security.voters'], 'unanimous');
        };

    }

    public function initializePlugin()
    {
        if ($this->initializedPlugin) {
            return;
        }
        $this->register(new ServiceProvider\EccubePluginServiceProvider());
        $this->initializedPlugin = true;
    }

    /**
     * PHPUnit を実行中かどうかを設定する.
     *
     * @param boolean $testMode PHPUnit を実行中の場合 true
     */
    public function setTestMode($testMode)
    {
        $this->testMode = $testMode;
    }

    /**
     * PHPUnit を実行中かどうか.
     *
     * @return boolean PHPUnit を実行中の場合 true
     */
    public function isTestMode()
    {
        return $this->testMode;
    }

    /**
     *
     * データベースの接続を確認
     * 成功 : trueを返却
     * 失敗 : \Doctrine\DBAL\DBALExceptionエラーが発生( 接続に失敗した場合 )、エラー画面を表示しdie()
     * 備考 : app['debug']がtrueの際は処理を行わない
     *
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

    /**
     * Config ファイルをパースし、連想配列を返します.
     *
     * $config_name.yml ファイルをパースし、連想配列を返します.
     * $config_name.php が存在する場合は、 PHP ファイルに記述された連想配列を使用します。
     *
     * @param string $config_name Config 名称
     * @param array $configAll Config の連想配列
     * @param boolean $wrap_key Config の連想配列に config_name のキーを生成する場合 true, デフォルト false
     * @param string $ymlPath config yaml を格納したディレクトリ
     * @param string $distPath config yaml dist を格納したディレクトリ
     * @return Application
     */
    public function parseConfig($config_name, array &$configAll, $wrap_key = false, $ymlPath = null, $distPath = null)
    {
        $ymlPath = $ymlPath ? $ymlPath : __DIR__.'/../../app/config/eccube';
        $distPath = $distPath ? $distPath : __DIR__.'/../../src/Eccube/Resource/config';
        $config = array();
        $config_php = $ymlPath.'/'.$config_name.'.php';
        if (!file_exists($config_php)) {
            $config_yml = $ymlPath.'/'.$config_name.'.yml';
            if (file_exists($config_yml)) {
                $config = Yaml::parse(file_get_contents($config_yml));
                $config = empty($config) ? array() : $config;
                if (isset($this['output_config_php']) && $this['output_config_php']) {
                    file_put_contents($config_php, sprintf('<?php return %s', var_export($config, true)).';');
                }
            }
        } else {
            $config = require $config_php;
        }

        $config_dist = array();
        $config_php_dist = $distPath.'/'.$config_name.'.dist.php';
        if (!file_exists($config_php_dist)) {
            $config_yml_dist = $distPath.'/'.$config_name.'.yml.dist';
            if (file_exists($config_yml_dist)) {
                $config_dist = Yaml::parse(file_get_contents($config_yml_dist));
                if (isset($this['output_config_php']) && $this['output_config_php']) {
                    file_put_contents($config_php_dist, sprintf('<?php return %s', var_export($config_dist, true)).';');
                }
            }
        } else {
            $config_dist = require $config_php_dist;
        }

        if ($wrap_key) {
            $configAll = array_replace_recursive($configAll, array($config_name => $config_dist), array($config_name => $config));
        } else {
            $configAll = array_replace_recursive($configAll, $config_dist, $config);
        }

        return $this;
    }

    /**
     * セッションが開始されているかどうか.
     *
     * @return boolean セッションが開始済みの場合 true
     * @link http://php.net/manual/ja/function.session-status.php#113468
     */
    protected function isSessionStarted()
    {
        if (php_sapi_name() !== 'cli') {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE ? true : false;
            } else {
                return session_id() === '' ? false : true;
            }
        }

        return false;
    }

    /**
     * Http Cache対応
     */
    protected function initCacheRequest()
    {
        // httpキャッシュが無効の場合はイベント設定を行わない.
        if (!$this['config']['http_cache']['enabled']) {
            return;
        }

        $app = $this;

        // Response Event(http cache対応、event実行は一番遅く設定)
        $this->on(\Symfony\Component\HttpKernel\KernelEvents::RESPONSE, function (\Symfony\Component\HttpKernel\Event\FilterResponseEvent $event) use ($app) {

            if (!$event->isMasterRequest()) {
                return;
            }

            $request = $event->getRequest();
            $response = $event->getResponse();

            $route = $request->attributes->get('_route');

            $etag = md5($response->getContent());

            if (strpos($route, 'admin') === 0) {
                // 管理画面

                // 管理画面ではコンテンツの中身が変更された時点でキャッシュを更新し、キャッシュの適用範囲はprivateに設定
                $response->setCache(array(
                    'etag' => $etag,
                    'private' => true,
                ));

                if ($response->isNotModified($request)) {
                    return $response;
                }

            } else {
                // フロント画面
                $cacheRoute = $app['config']['http_cache']['route'];

                if (in_array($route, $cacheRoute) === true) {
                    // キャッシュ対象となる画面lが含まれていた場合、キャッシュ化
                    // max-ageを設定しているためExpiresは不要
                    // Last-Modifiedだと比較する項目がないためETagで対応
                    // max-ageを設定していた場合、contentの中身が変更されても変更されない

                    $age = $app['config']['http_cache']['age'];

                    $response->setCache(array(
                        'etag' => $etag,
                        'max_age' => $age,
                        's_maxage' => $age,
                        'public' => true,
                    ));

                    if ($response->isNotModified($request)) {
                        return $response;
                    }
                }
            }

        }, -1024);
    }
}
