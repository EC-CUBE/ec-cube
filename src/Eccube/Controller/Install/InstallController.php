<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Install;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Migrations\MigrationException;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Doctrine\DBAL\Types\UTCDateTimeType;
use Eccube\Doctrine\DBAL\Types\UTCDateTimeTzType;
use Eccube\Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Eccube\Form\Type\Install\Step1Type;
use Eccube\Form\Type\Install\Step3Type;
use Eccube\Form\Type\Install\Step4Type;
use Eccube\Form\Type\Install\Step5Type;
use Eccube\Security\Core\Encoder\PasswordEncoder;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class InstallController extends AbstractController
{
    /**
     * default value of auth magic
     */
    const DEFAULT_AUTH_MAGIC = '<change.me>';

    /** @var string */
    const TRANSACTION_CHECK_FILE = '/var/.httransaction';

    protected $requiredModules = [
        'pdo',
        'phar',
        'mbstring',
        'zlib',
        'ctype',
        'session',
        'JSON',
        'xml',
        'libxml',
        'OpenSSL',
        'zip',
        'cURL',
        'fileinfo',
        'intl',
    ];

    protected $recommendedModules = [
        'hash',
        'mcrypt',
    ];

    protected $eccubeDirs = [
        'app/Plugin',
        'app/PluginData',
        'app/proxy',
        'app/template',
        'html',
        'var',
        'vendor',
    ];

    protected $eccubeFiles = [
        'composer.json',
        'composer.lock',
    ];

    /**
     * @var PasswordEncoder
     */
    protected $encoder;

    /**
     * @var CacheUtil
     */
    protected $cacheUtil;

    public function __construct(PasswordEncoder $encoder, CacheUtil $cacheUtil)
    {
        $this->encoder = $encoder;
        $this->cacheUtil = $cacheUtil;
    }

    /**
     * 最初からやり直す場合、SESSION情報をクリア.
     *
     * @Route("/", name="homepage", methods={"GET"})
     * @Route("/install", name="install", methods={"GET"})
     *
     * @Template("index.twig")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index()
    {
        if (!$this->isInstallEnv()) {
            throw new NotFoundHttpException();
        }

        $this->removeSessionData($this->session);

        return $this->redirectToRoute('install_step1');
    }

    /**
     * ようこそ.
     *
     * @Route("/install/step1", name="install_step1", methods={"GET", "POST"})
     * @Template("step1.twig")
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function step1(Request $request)
    {
        if (!$this->isInstallEnv()) {
            throw new NotFoundHttpException();
        }

        $form = $this->formFactory
            ->createBuilder(Step1Type::class)
            ->getForm();

        $form->setData($this->getSessionData($this->session));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setSessionData($this->session, $form->getData());

            return $this->redirectToRoute('install_step2');
        }

        $this->checkModules();

        $authmagic = $this->getParameter('eccube_auth_magic');
        if ($authmagic === self::DEFAULT_AUTH_MAGIC) {
            $authmagic = StringUtil::random(32);
        }
        $this->setSessionData($this->session, ['authmagic' => $authmagic]);

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * ディレクトリとファイルの書き込み権限をチェック.
     *
     * @Route("/install/step2", name="install_step2", methods={"GET"})
     * @Template("step2.twig")
     *
     * @return array
     */
    public function step2()
    {
        if (!$this->isInstallEnv()) {
            throw new NotFoundHttpException();
        }

        $noWritePermissions = [];

        $projectDir = $this->getParameter('kernel.project_dir');

        $eccubeDirs = array_map(function ($dir) use ($projectDir) {
            return $projectDir.'/'.$dir;
        }, $this->eccubeDirs);

        $eccubeFiles = array_map(function ($file) use ($projectDir) {
            return $projectDir.'/'.$file;
        }, $this->eccubeFiles);

        // ルートディレクトリの書き込み権限をチェック
        if (!is_writable($projectDir)) {
            $noWritePermissions[] = $projectDir;
        }

        // 対象ディレクトリの書き込み権限をチェック
        foreach ($eccubeDirs as $dir) {
            if (!is_writable($dir)) {
                $noWritePermissions[] = $dir;
            }
        }

        // 対象ディレクトリ配下のディレクトリ・ファイルの書き込み権限をチェック
        $finder = Finder::create()->in($eccubeDirs);
        foreach ($finder as $file) {
            if (!is_writable($file->getRealPath())) {
                $noWritePermissions[] = $file;
            }
        }

        // composer.json, composer.lockの書き込み権限をチェック
        foreach ($eccubeFiles as $file) {
            if (!is_writable($file)) {
                $noWritePermissions[] = $file;
            }
        }

        $faviconPath = '/assets/img/common/favicon.ico';
        if (!file_exists($this->getParameter('eccube_html_dir').'/user_data'.$faviconPath)) {
            $file = new Filesystem();
            $file->copy(
                $this->getParameter('eccube_html_front_dir').$faviconPath,
                $this->getParameter('eccube_html_dir').'/user_data'.$faviconPath
            );
        }

        $logoPath = '/assets/pdf/logo.png';
        if (!file_exists($this->getParameter('eccube_html_dir').'/user_data'.$logoPath)) {
            $file = new Filesystem();
            $file->copy(
                $this->getParameter('eccube_html_admin_dir').$logoPath,
                $this->getParameter('eccube_html_dir').'/user_data'.$logoPath
            );
        }

        return [
            'noWritePermissions' => $noWritePermissions,
        ];
    }

    /**
     * サイトの設定.
     *
     * @Route("/install/step3", name="install_step3", methods={"GET", "POST"})
     * @Template("step3.twig")
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function step3(Request $request)
    {
        if (!$this->isInstallEnv()) {
            throw new NotFoundHttpException();
        }

        $sessionData = $this->getSessionData($this->session);

        // 再インストールの場合は環境変数から復旧
        if ($this->isInstalled()) {
            // ショップ名/メールアドレス
            $conn = $this->entityManager->getConnection();
            $stmt = $conn->query('SELECT shop_name, email01 FROM dtb_base_info WHERE id = 1;');
            $row = $stmt->fetch();
            $sessionData['shop_name'] = $row['shop_name'];
            $sessionData['email'] = $row['email01'];

            $databaseUrl = $this->getParameter('eccube_database_url');
            $sessionData = array_merge($sessionData, $this->extractDatabaseUrl($databaseUrl));

            // 管理画面ルーティング
            $sessionData['admin_dir'] = $this->getParameter('eccube_admin_route');

            // 管理画面許可IP
            $sessionData['admin_allow_hosts'] = implode($this->getParameter('eccube_admin_allow_hosts'));

            // 強制SSL
            $sessionData['admin_force_ssl'] = $this->getParameter('eccube_force_ssl');

            // メール
            $mailerUrl = $this->getParameter('eccube_mailer_url');
            $sessionData = array_merge($sessionData, $this->extractMailerUrl($mailerUrl));
        } else {
            // 初期値設定
            if (!isset($sessionData['admin_allow_hosts'])) {
                $sessionData['admin_allow_hosts'] = '';
            }
            if (!isset($sessionData['smtp_host'])) {
                $sessionData = array_merge($sessionData, $this->extractMailerUrl('smtp://localhost:25'));
            }
        }

        $form = $this->formFactory
            ->createBuilder(Step3Type::class)
            ->getForm();

        $form->setData($sessionData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setSessionData($this->session, $form->getData());

            return $this->redirectToRoute('install_step4');
        }

        return [
            'form' => $form->createView(),
            'request' => $request,
        ];
    }

    /**
     * データベースの設定.
     *
     * @Route("/install/step4", name="install_step4", methods={"GET", "POST"})
     * @Template("step4.twig")
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Exception
     */
    public function step4(Request $request)
    {
        if (!$this->isInstallEnv()) {
            throw new NotFoundHttpException();
        }

        $sessionData = $this->getSessionData($this->session);

        if (empty($sessionData['database'])) {
            // 再インストールの場合は環境変数から復旧.
            if ($this->isInstalled()) {
                $databaseUrl = $this->getParameter('eccube_database_url');
                $sessionData = array_merge($sessionData, $this->extractDatabaseUrl($databaseUrl));
            }
        }

        $form = $this->formFactory
            ->createBuilder(Step4Type::class)
            ->getForm();

        $form->setData($sessionData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data['database'] === 'pdo_sqlite') {
                $data['database_name'] = '/var/eccube.db';
            }

            $this->setSessionData($this->session, $data);

            return $this->redirectToRoute('install_step5');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * データベースの初期化.
     *
     * @Route("/install/step5", name="install_step5", methods={"GET", "POST"})
     * @Template("step5.twig")
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Exception
     */
    public function step5(Request $request)
    {
        if (!$this->isInstallEnv()) {
            throw new NotFoundHttpException();
        }

        $form = $this->formFactory
            ->createBuilder(Step5Type::class)
            ->getForm();

        $sessionData = $this->getSessionData($this->session);
        $form->setData($sessionData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $noUpdate = $form['no_update']->getData();

            $url = $this->createDatabaseUrl($sessionData);

            try {
                $conn = $this->createConnection(['url' => $url]);
                $em = $this->createEntityManager($conn);
                $migration = $this->createMigration($conn);

                if ($noUpdate) {
                    $this->update($conn, [
                        'auth_magic' => $sessionData['authmagic'],
                        'login_id' => $sessionData['login_id'],
                        'login_pass' => $sessionData['login_pass'],
                        'shop_name' => $sessionData['shop_name'],
                        'email' => $sessionData['email'],
                    ]);
                } else {
                    $this->dropTables($em);
                    $this->createTables($em);
                    $this->importCsv($em);
                    $this->migrate($migration);
                    $this->insert($conn, [
                        'auth_magic' => $sessionData['authmagic'],
                        'login_id' => $sessionData['login_id'],
                        'login_pass' => $sessionData['login_pass'],
                        'shop_name' => $sessionData['shop_name'],
                        'email' => $sessionData['email'],
                    ]);
                }
            } catch (\Exception $e) {
                log_error($e->getMessage());
                $this->addError($e->getMessage());

                return [
                    'form' => $form->createView(),
                ];
            }

            if (isset($sessionData['agree']) && $sessionData['agree']) {
                $host = $request->getSchemeAndHttpHost();
                $basePath = $request->getBasePath();
                $params = [
                    'http_url' => $host.$basePath,
                    'shop_name' => $sessionData['shop_name'],
                ];
                $this->sendAppData($params, $em);
            }
            $version = $this->getDatabaseVersion($em);
            $this->setSessionData($this->session, ['database_version' => $version]);

            return $this->redirectToRoute('install_complete');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * インストール完了
     *
     * @Route("/install/complete", name="install_complete", methods={"GET"})
     * @Template("complete.twig")
     */
    public function complete(Request $request)
    {
        if (!$this->isInstallEnv()) {
            throw new NotFoundHttpException();
        }

        $sessionData = $this->getSessionData($this->session);
        $databaseUrl = $this->createDatabaseUrl($sessionData);
        $mailerUrl = $this->createMailerUrl($sessionData);
        $forceSSL = isset($sessionData['admin_force_ssl']) ? (bool) $sessionData['admin_force_ssl'] : false;
        if ($forceSSL === false) {
            $forceSSL = 'false';
        } elseif ($forceSSL === true) {
            $forceSSL = 'true';
        }
        $env = file_get_contents(__DIR__.'/../../../../.env.dist');
        $replacement = [
            'APP_ENV' => 'prod',
            'APP_DEBUG' => '0',
            'DATABASE_URL' => $databaseUrl,
            'MAILER_URL' => $mailerUrl,
            'ECCUBE_AUTH_MAGIC' => $sessionData['authmagic'],
            'DATABASE_SERVER_VERSION' => isset($sessionData['database_version']) ? $sessionData['database_version'] : '3',
            'ECCUBE_ADMIN_ALLOW_HOSTS' => $this->convertAdminAllowHosts($sessionData['admin_allow_hosts']),
            'ECCUBE_FORCE_SSL' => $forceSSL,
            'ECCUBE_ADMIN_ROUTE' => isset($sessionData['admin_dir']) ? $sessionData['admin_dir'] : 'admin',
            'ECCUBE_COOKIE_PATH' => $request->getBasePath() ? $request->getBasePath() : '/',
            'ECCUBE_TEMPLATE_CODE' => 'default',
            'ECCUBE_LOCALE' => 'ja',
            'TRUSTED_HOSTS' => '^'.str_replace('.', '\\.', $request->getHost()).'$',
        ];

        $env = StringUtil::replaceOrAddEnv($env, $replacement);

        if ($this->getParameter('kernel.environment') === 'install') {
            file_put_contents(__DIR__.'/../../../../.env', $env);
        }
        $host = $request->getSchemeAndHttpHost();
        $basePath = $request->getBasePath();
        $adminUrl = $host.$basePath.'/'.$replacement['ECCUBE_ADMIN_ROUTE'];

        $this->removeSessionData($this->session);

        // 有効化URLのトランザクションチェックファイルを生成する
        $token = StringUtil::random(32);
        file_put_contents($this->getParameter('kernel.project_dir').self::TRANSACTION_CHECK_FILE, time() + (60 * 10).':'.$token);

        $this->cacheUtil->clearCache('prod');

        return [
            'admin_url' => $adminUrl,
            'is_sqlite' => strpos($databaseUrl, 'sqlite') !== false,
            'token' => $token,
        ];
    }

    protected function getSessionData(SessionInterface $session)
    {
        return $session->get('eccube.session.install', []);
    }

    protected function removeSessionData(SessionInterface $session)
    {
        $session->clear();
    }

    protected function setSessionData(SessionInterface $session, $data = [])
    {
        $data = array_replace_recursive($this->getSessionData($session), $data);
        $session->set('eccube.session.install', $data);
    }

    protected function checkModules()
    {
        foreach ($this->requiredModules as $module) {
            if (!extension_loaded($module)) {
                $this->addDanger(trans('install.required_extension_disabled', ['%module%' => $module]), 'install');
            }
        }
        if (!extension_loaded('pdo_mysql') && !extension_loaded('pdo_pgsql')) {
            $this->addDanger(trans('install.required_database_extension_disabled'), 'install');
        }
        foreach ($this->recommendedModules as $module) {
            if (!extension_loaded($module)) {
                if ($module == 'mcrypt' && PHP_VERSION_ID >= 70100) {
                    //The mcrypt extension has been deprecated in PHP 7.1.x
                    //http://php.net/manual/en/migration71.deprecated.php
                    continue;
                }
                $this->addInfo(trans('install.recommend_extension_disabled', ['%module%' => $module]), 'install');
            }
        }
        if ('\\' === DIRECTORY_SEPARATOR) { // for Windows
            if (!extension_loaded('wincache')) {
                $this->addInfo(trans('install.recommend_extension_disabled', ['%module%' => 'wincache']), 'install');
            }
        } else {
            if (!extension_loaded('apc')) {
                $this->addInfo(trans('install.recommend_extension_disabled', ['%module%' => 'apc']), 'install');
            }
        }
        if (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) {
            if (!function_exists('apache_get_modules')) {
                $this->addWarning(trans('install.mod_rewrite_unknown'), 'install');
            } elseif (!in_array('mod_rewrite', apache_get_modules())) {
                $this->addDanger(trans('install.mod_rewrite_disabled'), 'install');
            }
        } elseif (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false) {
            // iis
        } elseif (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false) {
            // nginx
        }
    }

    protected function createConnection(array $params)
    {
        if (strpos($params['url'], 'mysql') !== false) {
            $params['charset'] = 'utf8';
            $params['defaultTableOptions'] = [
                'collate' => 'utf8_general_ci',
            ];
        }

        Type::overrideType('datetime', UTCDateTimeType::class);
        Type::overrideType('datetimetz', UTCDateTimeTzType::class);

        $conn = DriverManager::getConnection($params);
        $conn->ping();

        $platform = $conn->getDatabasePlatform();
        $platform->markDoctrineTypeCommented('datetime');
        $platform->markDoctrineTypeCommented('datetimetz');

        return $conn;
    }

    protected function createEntityManager(Connection $conn)
    {
        $paths = [
            $this->getParameter('kernel.project_dir').'/src/Eccube/Entity',
            $this->getParameter('kernel.project_dir').'/app/Customize/Entity',
        ];
        $config = Setup::createConfiguration(true);
        $driver = new AnnotationDriver(new CachedReader(new AnnotationReader(), new ArrayCache()), $paths);
        $driver->setTraitProxiesDirectory($this->getParameter('kernel.project_dir').'/app/proxy/entity');
        $config->setMetadataDriverImpl($driver);

        $em = EntityManager::create($conn, $config);

        return $em;
    }

    /**
     * @return string
     */
    public function createDatabaseUrl(array $params)
    {
        if (!isset($params['database'])) {
            return null;
        }

        $url = '';
        switch ($params['database']) {
            case 'pdo_sqlite':
                $url = 'sqlite://'.$params['database_name'];
                break;

            case 'pdo_mysql':
            case 'pdo_pgsql':
                $url = str_replace('pdo_', '', $params['database']);
                $url .= '://';
                if (isset($params['database_user'])) {
                    $url .= $params['database_user'];
                    if (isset($params['database_password'])) {
                        $url .= ':'.\rawurlencode($params['database_password']);
                    }
                    $url .= '@';
                }
                if (isset($params['database_host'])) {
                    $url .= $params['database_host'];
                    if (isset($params['database_port'])) {
                        $url .= ':'.$params['database_port'];
                    }
                    $url .= '/';
                }
                $url .= $params['database_name'];
                break;
        }

        return $url;
    }

    /**
     * @param string $url
     *
     * @return array
     */
    public function extractDatabaseUrl($url)
    {
        if (preg_match('|^sqlite://(.*)$|', $url, $matches)) {
            return [
                'database' => 'pdo_sqlite',
                'database_name' => $matches[1],
            ];
        }

        $parsed = parse_url($url);

        if ($parsed === false) {
            throw new \Exception('Malformed parameter "url".');
        }

        return [
            'database' => 'pdo_'.$parsed['scheme'],
            'database_name' => ltrim($parsed['path'], '/'),
            'database_host' => $parsed['host'],
            'database_port' => isset($parsed['port']) ? $parsed['port'] : null,
            'database_user' => isset($parsed['user']) ? $parsed['user'] : null,
            'database_password' => isset($parsed['pass']) ? $parsed['pass'] : null,
        ];
    }

    /**
     * @return string
     *
     * @see https://github.com/symfony/swiftmailer-bundle/blob/9728097df87e76e2db71fc41fd7d211c06daea3e/DependencyInjection/SwiftmailerTransportFactory.php#L80-L142
     */
    public function createMailerUrl(array $params)
    {
        $url = '';
        if (isset($params['transport'])) {
            $url = $params['transport'].'://';
        } else {
            $url = 'smtp://';
        }
        if (isset($params['smtp_username'])) {
            $url .= $params['smtp_username'];
            if (isset($params['smtp_password'])) {
                $url .= ':'.$params['smtp_password'];
            }
            $url .= '@';
        }

        $queryStrings = [];
        if (isset($params['encryption'])) {
            $queryStrings['encryption'] = $params['encryption'];
            if ($params['encryption'] === 'ssl' && !isset($params['smtp_port'])) {
                $params['smtp_port'] = 465;
            }
        }
        if (isset($params['auth_mode'])) {
            $queryStrings['auth_mode'] = $params['auth_mode'];
        } else {
            if (isset($params['smtp_username'])) {
                $queryStrings['auth_mode'] = 'plain';
            }
        }
        ksort($queryStrings, SORT_STRING);

        if (isset($params['smtp_host'])) {
            $url .= $params['smtp_host'];
            if (isset($params['smtp_port'])) {
                $url .= ':'.$params['smtp_port'];
            }
        }

        if (isset($params['smtp_username']) || array_values($queryStrings)) {
            $url .= '?';
            $i = count($queryStrings);
            foreach ($queryStrings as $key => $value) {
                $url .= $key.'='.$value;
                if ($i > 1) {
                    $url .= '&';
                }
                $i--;
            }
        }

        return $url;
    }

    /**
     * @param string $url
     *
     * @return array
     */
    public function extractMailerUrl($url)
    {
        $options = [
            'transport' => null,
            'smtp_username' => null,
            'smtp_password' => null,
            'smtp_host' => null,
            'smtp_port' => null,
            'encryption' => null,
            'auth_mode' => null,
        ];

        if ($url) {
            $parts = parse_url($url);
            if (isset($parts['scheme'])) {
                $options['transport'] = $parts['scheme'];
            }
            if (isset($parts['user'])) {
                $options['smtp_username'] = $parts['user'];
            }
            if (isset($parts['pass'])) {
                $options['smtp_password'] = $parts['pass'];
            }
            if (isset($parts['host'])) {
                $options['smtp_host'] = $parts['host'];
            }
            if (isset($parts['port'])) {
                $options['smtp_port'] = $parts['port'];
            }
            if (isset($parts['query'])) {
                parse_str($parts['query'], $query);
                foreach (array_keys($options) as $key) {
                    if (isset($query[$key]) && $query[$key] != '') {
                        $options[$key] = $query[$key];
                    }
                }
            }
        }
        if (!isset($options['transport'])) {
            $options['transport'] = 'smtp';
        } elseif ('gmail' === $options['transport']) {
            $options['encryption'] = 'ssl';
            $options['auth_mode'] = 'login';
            $options['smtp_host'] = 'smtp.gmail.com';
            $options['transport'] = 'smtp';
        }
        if (!isset($options['smtp_port'])) {
            $options['smtp_port'] = 'ssl' === $options['encryption'] ? 465 : 25;
        }
        if (isset($options['smtp_username']) && !isset($options['auth_mode'])) {
            $options['auth_mode'] = 'plain';
        }
        ksort($options, SORT_STRING);

        return $options;
    }

    protected function createMigration(Connection $conn)
    {
        $config = new Configuration($conn);
        $config->setMigrationsNamespace('DoctrineMigrations');
        $migrationDir = $this->getParameter('kernel.project_dir').'/src/Eccube/Resource/doctrine/migration';
        $config->setMigrationsDirectory($migrationDir);
        $config->registerMigrationsFromDirectory($migrationDir);

        $migration = new Migration($config);
        $migration->setNoMigrationException(true);

        return $migration;
    }

    protected function dropTables(EntityManager $em)
    {
        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema($metadatas);
        $em->getConnection()->executeQuery('DROP TABLE IF EXISTS doctrine_migration_versions');
    }

    protected function createTables(EntityManager $em)
    {
        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema($metadatas);
    }

    protected function importCsv(EntityManager $em)
    {
        // for full locale code cases
        $locale = env('ECCUBE_LOCALE', 'ja_JP');
        $locale = str_replace('_', '-', $locale);
        $locales = \Locale::parseLocale($locale);
        $localeDir = is_null($locales) ? 'ja' : $locales['language'];

        $loader = new \Eccube\Doctrine\Common\CsvDataFixtures\Loader();
        $loader->loadFromDirectory($this->getParameter('kernel.project_dir').'/src/Eccube/Resource/doctrine/import_csv/'.$localeDir);
        $executer = new \Eccube\Doctrine\Common\CsvDataFixtures\Executor\DbalExecutor($em);
        $fixtures = $loader->getFixtures();
        $executer->execute($fixtures);
    }

    protected function insert(Connection $conn, array $data)
    {
        $conn->beginTransaction();
        try {
            $salt = StringUtil::random(32);
            $this->encoder->setAuthMagic($data['auth_magic']);
            $password = $this->encoder->encodePassword($data['login_pass'], $salt);

            $id = ('postgresql' === $conn->getDatabasePlatform()->getName())
                ? $conn->fetchColumn("select nextval('dtb_base_info_id_seq')")
                : null;

            $conn->insert('dtb_base_info', [
                'id' => $id,
                'shop_name' => $data['shop_name'],
                'email01' => $data['email'],
                'email02' => $data['email'],
                'email03' => $data['email'],
                'email04' => $data['email'],
                'update_date' => new \DateTime(),
                'discriminator_type' => 'baseinfo',
            ], [
                'update_date' => \Doctrine\DBAL\Types\Type::DATETIME,
            ]);

            $member_id = ('postgresql' === $conn->getDatabasePlatform()->getName())
                ? $conn->fetchColumn("select nextval('dtb_member_id_seq')")
                : null;

            $conn->insert('dtb_member', [
                'id' => $member_id,
                'login_id' => $data['login_id'],
                'password' => $password,
                'salt' => $salt,
                'work_id' => 1,
                'authority_id' => 0,
                'creator_id' => 1,
                'sort_no' => 1,
                'update_date' => new \DateTime(),
                'create_date' => new \DateTime(),
                'name' => trans('install.member_name'),
                'department' => $data['shop_name'],
                'discriminator_type' => 'member',
            ], [
                'update_date' => Type::DATETIME,
                'create_date' => Type::DATETIME,
            ]);
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }

    protected function update(Connection $conn, array $data)
    {
        $conn->beginTransaction();
        try {
            $salt = StringUtil::random(32);
            $stmt = $conn->prepare('SELECT id FROM dtb_member WHERE login_id = :login_id;');
            $stmt->execute([':login_id' => $data['login_id']]);
            $row = $stmt->fetch();
            $this->encoder->setAuthMagic($data['auth_magic']);
            $password = $this->encoder->encodePassword($data['login_pass'], $salt);
            if ($row) {
                // 同一の管理者IDであればパスワードのみ更新
                $sth = $conn->prepare('UPDATE dtb_member set password = :password, salt = :salt, update_date = current_timestamp WHERE login_id = :login_id;');
                $sth->execute([
                    ':password' => $password,
                    ':salt' => $salt,
                    ':login_id' => $data['login_id'],
                ]);
            } else {
                // 新しい管理者IDが入力されたらinsert
                $sth = $conn->prepare("INSERT INTO dtb_member (login_id, password, salt, work_id, authority_id, creator_id, sort_no, update_date, create_date,name,department,discriminator_type) VALUES (:login_id, :password , :salt , '1', '0', '1', '1', current_timestamp, current_timestamp,'管理者','EC-CUBE SHOP', 'member');");
                $sth->execute([
                    ':login_id' => $data['login_id'],
                    ':password' => $password,
                    ':salt' => $salt,
                ]);
            }
            $stmt = $conn->prepare('UPDATE dtb_base_info set
                shop_name = :shop_name,
                email01 = :admin_mail,
                email02 = :admin_mail,
                email03 = :admin_mail,
                email04 = :admin_mail,
                update_date = current_timestamp
            WHERE id = 1;');
            $stmt->execute([
                ':shop_name' => $data['shop_name'],
                ':admin_mail' => $data['email'],
            ]);
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }

    public function migrate(Migration $migration)
    {
        try {
            // nullを渡すと最新バージョンまでマイグレートする
            $migration->migrate(null, false);
        } catch (MigrationException $e) {
        }
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function createAppData($params, EntityManager $em)
    {
        $platform = $em->getConnection()->getDatabasePlatform()->getName();
        $version = $this->getDatabaseVersion($em);
        $data = [
            'site_url' => $params['http_url'],
            'shop_name' => $params['shop_name'],
            'cube_ver' => Constant::VERSION,
            'php_ver' => phpversion(),
            'db_ver' => $platform.' '.$version,
            'os_type' => php_uname(),
        ];

        return $data;
    }

    /**
     * @param array $params
     */
    protected function sendAppData($params, EntityManager $em)
    {
        try {
            $query = http_build_query($this->createAppData($params, $em));
            $header = [
                'Content-Type: application/x-www-form-urlencoded',
                'Content-Length: '.strlen($query),
            ];
            $context = stream_context_create(
                [
                    'http' => [
                        'method' => 'POST',
                        'header' => $header,
                        'content' => $query,
                    ],
                ]
            );
            file_get_contents('https://www.ec-cube.net/mall/use_site.php', false, $context);
        } catch (\Exception $e) {
            // 送信に失敗してもインストールは継続できるようにする
            log_error($e->getMessage());
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDatabaseVersion(EntityManager $em)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('server_version', 'server_version');

        $platform = $em->getConnection()->getDatabasePlatform()->getName();
        switch ($platform) {
            case 'sqlite':
                $sql = 'SELECT sqlite_version() AS server_version';
                break;

            case 'mysql':
                $sql = 'SELECT version() AS server_version';
                break;

            case 'postgresql':
            default:
                $sql = 'SHOW server_version';
        }

        $version = $em->createNativeQuery($sql, $rsm)
            ->getSingleScalarResult();

        // postgresqlのバージョンが10.x以降の場合に、getSingleScalarResult()で取得される不要な文字列を除く処理
        if ($platform === 'postgresql') {
            preg_match('/\A([\d+\.]+)/', $version, $matches);
            $version = $matches[1];
        }

        return $version;
    }

    /**
     * @param string
     *
     * @return string
     */
    public function convertAdminAllowHosts($adminAllowHosts)
    {
        if (empty($adminAllowHosts)) {
            return '[]';
        }

        $adminAllowHosts = \json_encode(
            \explode("\n", StringUtil::convertLineFeed($adminAllowHosts))
        );

        return "'$adminAllowHosts'";
    }

    /**
     * @return bool
     */
    protected function isInstalled()
    {
        return self::DEFAULT_AUTH_MAGIC !== $this->getParameter('eccube_auth_magic');
    }

    /**
     * @return bool
     */
    protected function isInstallEnv()
    {
        $env = $this->getParameter('kernel.environment');

        if ($env === 'install' || $env === 'test') {
            return true;
        }

        return false;
    }
}
