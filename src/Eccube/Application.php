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
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class Application extends ApplicationTrait
{
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        // load config
        $this->initConfig();

        // init monolog
        $this->initLogger();
    }

    public function initialize()
    {
        // init locale
        $this->initLocale();

        // init session
        $this->initSession();

        // init twig
        $this->initRendering();

        // init provider
        $this->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        $this->register(new \Silex\Provider\FormServiceProvider());
        $this->register(new \Eccube\ServiceProvider\ValidatorServiceProvider());

        $app = $this;
        $this->error(function (\Exception $e, $code) use ($app) {
            if ($app['debug']) {
                return;
            }

            switch ($code) {
                case 404:
                    $title = 'ページがみつかりません。';
                    $message = 'URLに間違いがないかご確認ください。';
                    break;
                default:
                    $title = 'システムエラーが発生しました。';
                    $message = '大変お手数ですが、サイト管理者までご連絡ください。';
                    break;
            }

            return $app['twig']->render('error.twig', array(
                'error_title' => $title,
                'error_message' => $message,
            ));
        });

        // init mailer
        $this->initMailer();

        // init doctrine orm
        $this->initDoctrine();

        // init security
        $this->initSecurity();

        // init ec-cube service provider
        $this->register(new ServiceProvider\EccubeServiceProvider());

        // mount controllers
        $this->register(new \Silex\Provider\ServiceControllerServiceProvider());
        $this->mount('', new ControllerProvider\FrontControllerProvider());
        $this->mount('/' . trim($this['config']['admin_route'], '/') . '/', new ControllerProvider\AdminControllerProvider());
    }

    public function initializePlugin()
    {
        // setup event dispatcher
        $this->initPluginEventDispatcher();

        // load plugin
        $this->loadPlugin();
    }

    public function initConfig()
    {
        // load config
        $this['config'] = $this->share(function () {
            $ymlPath = __DIR__ . '/../../app/config/eccube';
            $distPath = __DIR__ . '/../../src/Eccube/Resource/config';

            $config = array();
            $config_yml = $ymlPath . '/config.yml';
            if (file_exists($config_yml)) {
                $config = Yaml::parse($config_yml);
            }

            $config_path = array();
            $path_yml = $ymlPath . '/path.yml';
            if (file_exists($path_yml)) {
                $config_path = Yaml::parse($path_yml);
            }

            $config_constant = array();
            $constant_yml = $ymlPath . '/constant.yml';
            if (file_exists($constant_yml)) {
                $config_constant = Yaml::parse($constant_yml);
                $config_constant = empty($config_constant) ? array() : $config_constant;
            }


            $config_constant_dist = array();
            $constant_yml_dist = $distPath . '/constant.yml.dist';
            if (file_exists($constant_yml_dist)) {
                $config_constant_dist = Yaml::parse($constant_yml_dist);
            }

            $configAll = array_replace_recursive($config_constant_dist, $config_constant, $config_path, $config);

            $database = array();
            $yml = $ymlPath . '/database.yml';
            if (file_exists($yml)) {
                $database = Yaml::parse($yml);
            }

            $mail = array();
            $yml = $ymlPath . '/mail.yml';
            if (file_exists($yml)) {
                $mail = Yaml::parse($yml);
            }
            $configAll = array_replace_recursive($configAll, $database, $mail);

            $config_log = array();
            $yml = $ymlPath . '/log.yml';
            if (file_exists($yml)) {
                $config_log = Yaml::parse($yml);
            }
            $config_log_dist = array();
            $log_yml_dist = $distPath . '/log.yml.dist';
            if (file_exists($log_yml_dist)) {
                $config_log_dist = Yaml::parse($log_yml_dist);
            }

            $configAll = array_replace_recursive($configAll, $config_log_dist, $config_log);

            return $configAll;
        });
    }

    public function initLogger()
    {
        $file = __DIR__ . '/../../app/log/site.log';
        $this->register(new \Silex\Provider\MonologServiceProvider(), array(
            'monolog.logfile' => $file,
        ));

        $levels = Logger::getLevels();
        $this['monolog'] = $this->share($this->extend('monolog', function ($monolog, $this) use ($levels, $file) {

            $RotateHandler = new RotatingFileHandler($file, $this['config']['log']['max_files'], $this['config']['log']['log_level']);
            $RotateHandler->setFilenameFormat(
                $this['config']['log']['prefix'] . '{date}' . $this['config']['log']['suffix'],
                $this['config']['log']['format']
            );

            $FingerCrossedHandler = new FingersCrossedHandler(
                $RotateHandler,
                new ErrorLevelActivationStrategy($levels[$this['config']['log']['action_level']])
            );
            $monolog->popHandler();
            $monolog->pushHandler($FingerCrossedHandler);

            return $monolog;
        }));
    }

    public function initSession()
    {
        $this->register(new \Silex\Provider\SessionServiceProvider(), array(
            'session.storage.save_path' => $this['config']['root_dir'] . '/app/cache/eccube/session',
            'session.storage.options' => array(
                'name' => 'eccube',
                'cookie_path' => $this['config']['root_urlpath'],
                'cookie_secure' => $this['config']['force_ssl'],
                'cookie_lifetime' => $this['config']['cookie_lifetime'],
                'cookie_httponly' => true,
                // cookie_domainは指定しない
                // http://blog.tokumaru.org/2011/10/cookiedomain.html
            ),
        ));
    }

    public function initLocale()
    {
        $this->register(new \Silex\Provider\TranslationServiceProvider(), array(
            'locale' => $this['config']['locale'],
        ));
        $this['translator'] = $this->share($this->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());

            $r = new \ReflectionClass('Symfony\Component\Validator\Validator');
            $file = dirname($r->getFilename()) . '/Resources/translations/validators.' . $app['locale'] . '.xlf';
            if (file_exists($file)) {
                $translator->addResource('xliff', $file, $app['locale'], 'validators');
            }

            $file = __DIR__ . '/Resource/locale/validator.' . $app['locale'] . '.yml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale'], 'validators');
            }

            $file = __DIR__ . '/Resource/locale/message.' . $app['locale'] . '.yml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale']);
            }

            return $translator;
        }));
    }

    public function initRendering()
    {
        $this->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.form.templates' => array('Form/form_layout.twig'),
        ));
        $this['twig'] = $this->share($this->extend("twig", function (\Twig_Environment $twig, \Silex\Application $app) {
            $twig->addExtension(new \Eccube\Twig\Extension\EccubeExtension($app));
            $twig->addExtension(new \Twig_Extension_StringLoader());

            return $twig;
        }));

        $this->before(function (Request $request, \Silex\Application $app) {
            // フロント or 管理画面ごとにtwigの探索パスを切り替える.
            $app['twig'] = $app->share($app->extend("twig", function (\Twig_Environment $twig, \Silex\Application $app) {
                $paths = array();
                if (strpos($app['request']->getPathInfo(), '/' . trim($app['config']['admin_route'], '/')) === 0) {
                    if (file_exists(__DIR__ . '/../../app/template/admin')) {
                        $paths[] = __DIR__ . '/../../app/template/admin';
                    }
                    $paths[] = $app['config']['template_admin_realdir'];
                    $paths[] = __DIR__ . '/../../app/Plugin';
                    $cache = __DIR__ . '/../../app/cache/twig/admin';
                } else {
                    if (file_exists($app['config']['template_realdir'])) {
                        $paths[] = $app['config']['template_realdir'];
                    }
                    $paths[] = $app['config']['template_default_realdir'];
                    $paths[] = __DIR__ . '/../../app/Plugin';
                    $cache = __DIR__ . '/../../app/cache/twig/' . $app['config']['template_code'];
                }
                $twig->setCache($cache);
                $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem($paths));

                return $twig;
            }));

            // 管理画面のIP制限チェック.
            if (strpos($app['request']->getPathInfo(), '/' . trim($app['config']['admin_route'], '/')) === 0) {
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
        $this->on(\Symfony\Component\HttpKernel\KernelEvents::CONTROLLER, function (\Symfony\Component\HttpKernel\Event\FilterControllerEvent $event) use ($app) {
            // ショップ基本情報
            $BaseInfo = $app['eccube.repository.base_info']->get();
            $app["twig"]->addGlobal("BaseInfo", $BaseInfo);

            // 管理画面
            if (strpos($app['request']->getPathInfo(), '/' . trim($app['config']['admin_route'], '/')) === 0) {
                // 管理画面メニュー
                $menus = array('', '', '');
                $app['twig']->addGlobal('menus', $menus);
                // フロント画面
            } else {
                $request = $event->getRequest();
                try {
                    $DeviceType = $app['eccube.repository.master.device_type']->find(\Eccube\Entity\Master\DeviceType::DEVICE_TYPE_PC);
                    if ($request->get('preview')) {
                        $PageLayout = $app['eccube.repository.page_layout']->getByUrl($DeviceType, 'preview');
                    } else {
                        $PageLayout = $app['eccube.repository.page_layout']->getByUrl($DeviceType,
                            $request->attributes->get('_route'));
                    }
                } catch (\Doctrine\ORM\NoResultException $e) {
                    $PageLayout = $app['eccube.repository.page_layout']->newPageLayout($DeviceType);
                }

                $app["twig"]->addGlobal("PageLayout", $PageLayout);
                $app["twig"]->addGlobal("title", $PageLayout->getName());
            }
        });
    }

    public function initMailer()
    {
        $this->register(new \Silex\Provider\SwiftmailerServiceProvider());
        $this['swiftmailer.options'] = $this['config']['mail'];

        if (isset($this['config']['mail']['spool']) && is_bool($this['config']['mail']['spool'])) {
            $this['swiftmailer.use_spool'] = $this['config']['mail']['spool'];
        }
        // デフォルトはsmtpを使用
        $transport = $this['config']['mail']['transport'];
        if ($transport == 'sendmail') {
            $this['swiftmailer.transport'] = \Swift_SendmailTransport::newInstance();
        } else if ($transport == 'mail') {
            $this['swiftmailer.transport'] = \Swift_MailTransport::newInstance();
        }
    }

    public function initDoctrine()
    {
        $this->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => $this['config']['database']
        ));
        $this->register(new \Saxulum\DoctrineOrmManagerRegistry\Silex\Provider\DoctrineOrmManagerRegistryProvider());

        // プラグインのmetadata定義を合わせて行う.
        $pluginBasePath = __DIR__ . '/../../app/Plugin';
        $finder = Finder::create()
            ->in($pluginBasePath)
            ->directories()
            ->depth(0);

        $ormMappings = array();
        $ormMappings[] = array(
            'type' => 'yml',
            'namespace' => 'Eccube\Entity',
            'path' => array(
                __DIR__ . '/Resource/doctrine',
                __DIR__ . '/Resource/doctrine/master',
            ),
        );

        foreach ($finder as $dir) {
            $config = Yaml::parse($dir->getRealPath() . '/config.yml');

            // Doctrine Extend
            if (isset($config['orm.path']) and is_array($config['orm.path'])) {
                $paths = array();
                foreach ($config['orm.path'] as $path) {
                    $paths[] = $pluginBasePath . '/' . $config['name'] . $path;
                }
                $ormMappings[] = array(
                    'type' => 'yml',
                    'namespace' => 'Plugin\\' . $config['name'] . '\\Entity',
                    'path' => $paths,
                );
            }
        }

        $this->register(new \Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), array(
            "orm.proxies_dir" => __DIR__ . '/../../app/cache/doctrine',
            'orm.em.options' => array(
                'mappings' => $ormMappings,
            ),
        ));
    }

    public function initPluginEventDispatcher()
    {
        // EventDispatcher
        $this['eccube.event.dispatcher'] = $this->share(function () {
            return new EventDispatcher();
        });

        // hook point
        $this->before(function (Request $request, \Silex\Application $app) {
            $app['eccube.event.dispatcher']->dispatch('eccube.event.app.before');
        }, self::EARLY_EVENT);

        $this->before(function (Request $request, \Silex\Application $app) {
            $event = 'eccube.event.controller.' . $request->attributes->get('_route') . '.before';
            $app['eccube.event.dispatcher']->dispatch($event);
        });

        $this->after(function (Request $request, Response $response, \Silex\Application $app) {
            $event = 'eccube.event.controller.' . $request->attributes->get('_route') . '.after';
            $app['eccube.event.dispatcher']->dispatch($event);
        });

        $this->after(function (Request $request, Response $response, \Silex\Application $app) {
            $app['eccube.event.dispatcher']->dispatch('eccube.event.app.after');
        }, self::LATE_EVENT);

        $this->finish(function (Request $request, Response $response, \Silex\Application $app) {
            $event = 'eccube.event.controller.' . $request->attributes->get('_route') . '.finish';
            $app['eccube.event.dispatcher']->dispatch($event);
        });
    }

    public function loadPlugin()
    {
        // プラグインディレクトリを探索.
        $basePath = __DIR__ . '/../../app/Plugin';
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
            if (!file_exists($dir->getRealPath() . '/config.yml')) {
                continue;
            }
            $config = Yaml::parse($dir->getRealPath() . '/config.yml');
            // Type: Event
            if (isset($config['event'])) {
                $class = '\\Plugin\\' . $config['name'] . '\\' . $config['event'];
                $subscriber = new $class($this);

                if (file_exists($dir->getRealPath() . '/event.yml')) {

                    foreach (Yaml::Parse($dir->getRealPath() . '/event.yml') as $event => $handlers) {
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
            // const
            if (isset($config['const'])) {
                $this['config'] = $this->share($this->extend('config', function ($eccubeConfig) use ($config) {
                    $eccubeConfig[$config['name']] = array(
                        'const' => $config['const'],
                    );

                    return $eccubeConfig;
                }));
            }
            // Type: ServiceProvider
            if (isset($config['service'])) {
                foreach ($config['service'] as $service) {
                    $class = '\\Plugin\\' . $config['name'] . '\\ServiceProvider\\' . $service;
                    $this->register(new $class($this));
                }
            }
        }
    }

    public function initSecurity()
    {
        $this->register(new \Silex\Provider\SecurityServiceProvider(), array(
            'security.firewalls' => array(
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
                    'users' => $this['orm.em']->getRepository('Eccube\Entity\Customer'),
                    'anonymous' => true,
                ),
            ),
        ));
        $this['security.access_rules'] = array(
            array("^/{$this['config']['admin_route']}/login", 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array("^/{$this['config']['admin_route']}", 'ROLE_ADMIN'),
            array('^/mypage/login', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/mypage/withdraw_complete', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/mypage', 'ROLE_USER'),
        );
        $this['eccube.password_encoder'] = $this->share(function ($app) {
            return new \Eccube\Security\Core\Encoder\PasswordEncoder($app['config']);
        });
        $this['security.encoder_factory'] = $this->share(function ($app) {
            return new \Symfony\Component\Security\Core\Encoder\EncoderFactory(array(
                'Eccube\Entity\Customer' => $app['eccube.password_encoder'],
                'Eccube\Entity\Member' => $app['eccube.password_encoder'],
            ));
        });
        $this['eccube.event_listner.security'] = $this->share(function ($app) {
            return new \Eccube\EventListner\SecurityEventListner($app['orm.em']);
        });
        $this['user'] = $this->share(function ($app) {
            $token = $app['security']->getToken();

            return ($token !== null) ? $token->getUser() : null;
        });

        // ログイン時のイベントを設定.
        $this['dispatcher']->addListener(\Symfony\Component\Security\Http\SecurityEvents::INTERACTIVE_LOGIN, array($this['eccube.event_listner.security'], 'onInteractiveLogin'));
    }


    /**
     * Application Shortcut Methods
     *
     *
     */

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

    public function addRequestError($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->set('eccube.' . $namespace . '.request.error', $message);
    }

}
