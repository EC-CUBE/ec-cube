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
        $this->register(new \Silex\Provider\ValidatorServiceProvider());

        $app = $this;
        $this->error(function (\Exception $e, $code) use ($app) {
            if ($app['debug']) {
                return;
            }

            switch ($code) {
                case 404:
                    break;
                default:
                    break;
            }

            return $app['view']->render('error.twig', array(
                'error' => 'エラーが発生しました.',
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
            $config = array();
            $config_yml = __DIR__ . '/../../app/config/eccube/config.yml';
            if (file_exists($config_yml)) {
                $config = Yaml::parse($config_yml);
            }

            $config_path = array();
            $path_yml = __DIR__ . '/../../app/config/eccube/path.yml';
            if (file_exists($path_yml)) {
                $config_path = Yaml::parse($path_yml);
            }

            $config_constant = array();
            $constant_yml = __DIR__ . '/../../app/config/eccube/constant.yml';
            if (file_exists($constant_yml)) {
                $config_constant = Yaml::parse($constant_yml);
                $config_constant = empty($config_constant) ? array() : $config_constant;
            }


            $config_constant_dist = array();
            $constant_yml_dist = __DIR__ . '/../../src/Eccube/Resource/config/constant.yml.dist';
            if (file_exists($constant_yml_dist)) {
                $config_constant_dist = Yaml::parse($constant_yml_dist);
            }

            $configAll = array_replace_recursive($config_constant_dist, $config_constant, $config_path, $config);

            $database = array();
            $yml = __DIR__ . '/../../app/config/eccube/database.yml';
            if (file_exists($yml)) {
                $database = Yaml::parse($yml);
            }

            $mail = array();
            $yml = __DIR__ . '/../../app/config/eccube/mail.yml';
            if (file_exists($yml)) {
                $mail = Yaml::parse($yml);
            }

            $configAll = array_replace_recursive($configAll, $database, $mail);
            return $configAll;
        });
    }

    public function initLogger()
    {
        $this->register(new \Silex\Provider\MonologServiceProvider(), array(
            'monolog.logfile' => __DIR__ . '/../../app/log/site.log',
        ));
    }

    public function initSession()
    {
        $this->register(new \Silex\Provider\SessionServiceProvider());
    }

    public function initLocale()
    {
        $this->register(new \Silex\Provider\TranslationServiceProvider(), array(
            'locale' => 'ja',
        ));
        $this['translator'] = $this->share($this->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
            $translator->addResource('yaml', __DIR__ . '/Resource/locale/ja.yml', 'ja');

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
            if (isset($config['orm.path']) and is_array( $config['orm.path'])) {
                $paths = array();
                foreach ($config['orm.path'] as $path) {
                    $paths[] = $ormMappings . '/' . $config['name'] . $path;
                }
                $ormMppings[] = array(
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
                $app[$config['name']] = array(
                    'const' => $config['const'],
                );
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
     * @param mixed $data The initial data for the form
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
     * @param array $context The log context
     * @param int $level The logging level
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
     * @param UserInterface $user A UserInterface instance
     * @param string $password The password to encode
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
     * @param \Swift_Message $message A \Swift_Message instance
     * @param array $failedRecipients An array of failures by-reference
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
     * @param string $id The message id
     * @param array $parameters An array of parameters for the message
     * @param string $domain The domain for the message
     * @param string $locale The locale
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
     * @param string $id The message id
     * @param int $number The number to use to find the indice of the message
     * @param array $parameters An array of parameters for the message
     * @param string $domain The domain for the message
     * @param string $locale The locale
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
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
     * @param Response $response A Response instance
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
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
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
     * @param string $route The name of the route
     * @param mixed $parameters An array of parameters
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
     * @param string $route The name of the route
     * @param mixed $parameters An array of parameters
     *
     * @return string The generated URL
     */
    public function url($route, $parameters = array())
    {
        return $this['url_generator']->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
