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

namespace Eccube\Controller\Install;

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
use Eccube\Form\Type\Install\Step1Type;
use Eccube\Form\Type\Install\Step3Type;
use Eccube\Form\Type\Install\Step4Type;
use Eccube\Form\Type\Install\Step5Type;
use Eccube\Security\Core\Encoder\PasswordEncoder;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route(service=InstallController::class)
 */
class InstallController extends AbstractController
{
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
        'intl'
    ];

    protected $recommendedModules = [
        'hash',
        'mcrypt'
    ];

    protected $writableDirs = [
        '/html',
        '/app',
        '/app/template',
        '/app/cache',
        '/app/config',
        '/app/config/eccube',
        '/app/log',
        '/app/Plugin',
        '/app/proxy',
        '/app/proxy/entity',
    ];

    protected $rootDir;
    protected $configDir;
    protected $configDistDir;
    protected $cacheDir;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var PasswordEncoder
     */
    protected $encoder;

    public function __construct(
        SessionInterface $session,
        FormFactoryInterface $formFactory,
        PasswordEncoder $encoder,
        $environment,
        $eccubeConfig
    ) {
        $this->rootDir = realpath(__DIR__.'/../../../..');
        $this->configDir = realpath($this->rootDir.'/app/config/eccube');
        $this->configDistDir = realpath($this->rootDir.'/src/Eccube/Resource/config');
        $this->cacheDir = realpath($this->rootDir.'/app/cache');
        $this->session = $session;
        $this->formFactory = $formFactory;
        $this->encoder = $encoder;
        $this->environment = $environment;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * 最初からやり直す場合、SESSION情報をクリア.
     *
     * @Route("/", name="homepage")
     * @Template("index.twig")
     *
     * @param InstallApplication $app
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // $this->removeSessionData($session);

        return $this->redirectToRoute('install_step1');
    }

    /**
     * ようこそ.
     *
     * @Route("/install/step1", name="install_step1")
     * @Template("step1.twig")
     *
     * @param Request $request
     * @return Response
     */
    public function step1(Request $request)
    {
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
        $authmagic = env('ECCUBE_AUTH_MAGIC', 'secret'); // 'secret' is defaut value of .env.dist file.
        if ($authmagic == 'secret') {
            $authmagic =  StringUtil::random(32);
        }
        $this->setSessionData($this->session, ['authmagic' => $authmagic]);

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * ディレクトリの書き込み権限をチェック.
     *
     * @Route("/install/step2", name="install_step2")
     * @Template("step2.twig")
     *
     * @param InstallApplication $app
     * @param Request $request
     * @return Response
     */
    public function step2(Request $request)
    {
        $protectedDirs = [];
        foreach ($this->writableDirs as $dir) {
            if (!is_writable($this->rootDir.$dir)) {
                $protectedDirs[] = $dir;
            }
        }

        // 権限がある場合, キャッシュディレクトリをクリア
        if (empty($protectedDirs)) {
            $finder = Finder::create()
                ->in($this->cacheDir)
                ->notName('.gitkeep')
                ->files();
            $fs = new Filesystem();
            $fs->remove($finder);
        }

        return [
            'protectedDirs' => $protectedDirs,
        ];
    }

    /**
     * サイトの設定.
     *
     * @Route("/install/step3", name="install_step3")
     * @Template("step3.twig")
     *
     * @param InstallApplication $app
     * @param Request $request
     * @return Response
     */
    public function step3(Request $request)
    {
        $sessionData = $this->getSessionData($this->session);

        // 再インストールの場合は環境変数から復旧
        if (env('DATABASE_URL')) {
            // ショップ名/メールアドレス
            $conn = $this->container->get('database_connection');
            $stmt = $conn->query("SELECT shop_name, email01 FROM dtb_base_info WHERE id = 1;");
            $row = $stmt->fetch();
            $sessionData['shop_name'] = $row['shop_name'];
            $sessionData['email'] = $row['email01'];

            $sessionData = array_merge($sessionData, $this->extractDatabaseUrl(env('DATABASE_URL')));

            // 管理画面ルーティング
            $sessionData['admin_dir'] = env('ECCUBE_ADMIN_ROUTE');

            // 管理画面許可IP
            $sessionData['admin_allow_hosts'] = implode(PHP_EOL, env('ECCUBE_ADMIN_ALLOW_HOSTS', ["127.0.0.1"]));

            // 強制SSL
            $sessionData['admin_force_ssl'] = env('ECCUBE_FORCE_SSL', false);

            // メール
            $sessionData = array_merge($sessionData, $this->extractMailerUrl(env('MAILER_URL')));
        } else {
            // 初期値設定
            if (!isset($sessionData['admin_allow_hosts'])) {
                $sessionData['admin_allow_hosts'] = implode(PHP_EOL, env('ECCUBE_ADMIN_ALLOW_HOSTS', ["127.0.0.1"]));
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
            'request' => $request
        ];
    }

    /**
     * データベースの設定.
     *
     * @Route("/install/step4", name="install_step4")
     * @Template("step4.twig")
     *
     * @param InstallApplication $app
     * @param Request $request
     * @return Response
     */
    public function step4(Request $request)
    {
        $sessionData = $this->getSessionData($this->session);

        if (empty($sessionData['database'])) {
            // 再インストールの場合は環境変数から復旧.
            if (env('DATABASE_URL')) {
                $sessionData = array_merge($sessionData, $this->extractDatabaseUrl(env('DATABASE_URL')));
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
                $data['database_name'] = '/'.$this->configDir.'/eccube.db';
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
     * @Route("/install/step5", name="install_step5")
     * @Template("step5.twig")
     *
     * @param InstallApplication $app
     * @param Request $request
     * @return Response
     */
    public function step5(Request $request)
    {
        $form = $this->formFactory
            ->createBuilder(Step5Type::class)
            ->getForm();

        $sessionData = $this->getSessionData($this->session);
        $form->setData($sessionData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $noUpdate = $form['no_update']->getData();

            $url = $this->createDatabaseUrl($sessionData);

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

            if (isset($sessionData['agree']) && $sessionData['agree']) {
                $host = $request->getSchemeAndHttpHost();
                $basePath = $request->getBasePath();
                $params = array(
                    'http_url' => $host.$basePath,
                    'shop_name' => $sessionData['shop_name'],
                );
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
     * @Route("/install/complete", name="install_complete")
     * @Template("complete.twig")
     */
    public function complete(Request $request)
    {
        $sessionData = $this->getSessionData($this->session);
        $databaseUrl = $this->createDatabaseUrl($sessionData);
        $mailerUrl = $this->createMailerUrl($sessionData);
        $forceSSL = isset($sessionData['admin_force_ssl']) ? (boolean)$sessionData['admin_force_ssl'] : false;
        if ($forceSSL === false) {
            $forceSSL = 'false';
        } elseif ($forceSSL === true) {
            $forceSSL = 'true';
        }
        $env = file_get_contents(__DIR__.'/../../../../.env.dist');
        $replacement = [
            'APP_ENV' => 'dev', // TODO 本番環境では prod にするが cache:warmup しないと Not found になってしまう
            'APP_DEBUG' => '1',
            'APP_SECRET' => StringUtil::random(32),
            'DATABASE_URL' => $databaseUrl,
            'MAILER_URL' => $mailerUrl,
            'ECCUBE_AUTH_MAGIC' => $sessionData['authmagic'],
            'DATABASE_SERVER_VERSION' => isset($sessionData['database_version']) ? $sessionData['database_version'] : '3',
            'ECCUBE_ADMIN_ALLOW_HOSTS' => $this->convertAdminAllowHosts($sessionData['admin_allow_hosts']),
            'ECCUBE_FORCE_SSL' => $forceSSL,
            'ECCUBE_ADMIN_ROUTE' => isset($sessionData['admin_dir']) ? $sessionData['admin_dir'] : 'admin'
        ];

        $env = $this->replaceEnv($env, $replacement);

        if ($this->environment === 'install') {
            file_put_contents(__DIR__.'/../../../../.env', $env);
        }
        $host = $request->getSchemeAndHttpHost();
        $basePath = $request->getBasePath();
        $adminUrl = $host.$basePath.'/'.$replacement['ECCUBE_ADMIN_ROUTE'];

        $this->removeSessionData($this->session);
        return [
            'admin_url' => $adminUrl,
        ];
    }

    public function getSessionData(SessionInterface $session)
    {
        return $session->get('eccube.session.install', []);
    }

    public function removeSessionData(SessionInterface $session)
    {
        $session->remove('eccube.session.install');
    }

    public function setSessionData(SessionInterface $session, $data = [])
    {
        $data = array_replace_recursive($this->getSessionData($session), $data);
        $session->set('eccube.session.install', $data);
    }

    public function checkModules()
    {
        foreach ($this->requiredModules as $module) {
            if (!extension_loaded($module)) {
                $this->addDanger('[必須] '.$module.' 拡張モジュールが有効になっていません。', 'install');
            }
        }
        if (!extension_loaded('pdo_mysql') && !extension_loaded('pdo_pgsql')) {
            $this->addDanger('[必須] '.'pdo_pgsql又はpdo_mysql 拡張モジュールを有効にしてください。', 'install');
        }
        foreach ($this->recommendedModules as $module) {
            if (!extension_loaded($module)) {
                if ($module == 'mcrypt' && PHP_VERSION_ID >= 70100) {
                    //The mcrypt extension has been deprecated in PHP 7.1.x
                    //http://php.net/manual/en/migration71.deprecated.php
                    continue;
                }
                $app->addInfo('[推奨] '.$module.' 拡張モジュールが有効になっていません。', 'install');
            }
        }
        if ('\\' === DIRECTORY_SEPARATOR) { // for Windows
            if (!extension_loaded('wincache')) {
                $this->addInfo('[推奨] WinCache 拡張モジュールが有効になっていません。', 'install');
            }
        } else {
            if (!extension_loaded('apc')) {
                $this->addInfo('[推奨] APC 拡張モジュールが有効になっていません。', 'install');
            }
        }
        if (isset($_SERVER['SERVER_SOFTWARE']) && strpos('Apache', $_SERVER['SERVER_SOFTWARE']) !== false) {
            if (!function_exists('apache_get_modules')) {
                $this->addWarning('mod_rewrite が有効になっているか不明です。', 'install');
            } elseif (!in_array('mod_rewrite', apache_get_modules())) {
                $this->addDanger('[必須] '.'mod_rewriteを有効にしてください。', 'install');
            }
        } elseif (isset($_SERVER['SERVER_SOFTWARE']) && strpos('Microsoft-IIS',
                $_SERVER['SERVER_SOFTWARE']) !== false
        ) {
            // iis
        } elseif (isset($_SERVER['SERVER_SOFTWARE']) && strpos('nginx', $_SERVER['SERVER_SOFTWARE']) !== false) {
            // nginx
        }
    }

    public function createConnection(array $params)
    {
        $conn = DriverManager::getConnection($params);
        $conn->ping();

        return $conn;
    }

    public function createEntityManager(Connection $conn)
    {
        $paths = [
            $this->rootDir.'/src/Eccube/Entity',
            $this->rootDir.'/app/Acme/Entity',
        ];
        $config = Setup::createAnnotationMetadataConfiguration($paths, true, null, null, false);
        $em = EntityManager::create($conn, $config);

        return $em;
    }

    /**
     * @param array $params
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
                        $url .= ':'.$params['database_password'];
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
     * @return array
     */
    public function extractDatabaseUrl($url)
    {
        if (preg_match('|^sqlite://(.*)$|', $url, $matches)) {
            return [
                'database' => 'pdo_sqlite',
                'database_name' => $matches[1]
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
            'database_password' => isset($parsed['pass']) ? $parsed['pass'] : null
        ];
    }

    /**
     * @param array $options
     * @return string
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
            'auth_mode' => null
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
                    if (isset($query[$key]) && $query[$key] != "") {
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

    public function createMigration(Connection $conn)
    {
        $config = new Configuration($conn);
        $config->setMigrationsNamespace('DoctrineMigrations');
        $migrationDir = $this->rootDir.'/src/Eccube/Resource/doctrine/migration';
        $config->setMigrationsDirectory($migrationDir);
        $config->registerMigrationsFromDirectory($migrationDir);

        $migration = new Migration($config);
        $migration->setNoMigrationException(true);

        return $migration;
    }

    public function dropTables(EntityManager $em)
    {
        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema($metadatas);
        $em->getConnection()->executeQuery('DROP TABLE IF EXISTS doctrine_migration_versions');
    }

    public function createTables(EntityManager $em)
    {
        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema($metadatas);
    }

    public function importCsv(EntityManager $em)
    {
        $loader = new \Eccube\Doctrine\Common\CsvDataFixtures\Loader();
        $loader->loadFromDirectory($this->rootDir.'/src/Eccube/Resource/doctrine/import_csv');
        $executer = new \Eccube\Doctrine\Common\CsvDataFixtures\Executor\DbalExecutor($em);
        $fixtures = $loader->getFixtures();
        $executer->execute($fixtures);
    }

    public function insert(Connection $conn, array $data)
    {
        $conn->beginTransaction();
        try {
            $salt = StringUtil::random(32);
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
                'discriminator_type' => 'baseinfo'
            ], [
                'update_date' => \Doctrine\DBAL\Types\Type::DATETIME
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
                'name' => '管理者',
                'department' => 'EC-CUBE SHOP',
                'discriminator_type' => 'member'
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

    public function update(Connection $conn, array $data)
    {
        $conn->beginTransaction();
        try {
            $config = array(
                'auth_type' => '',
                'auth_magic' => $data['auth_magic'],
                'password_hash_algos' => 'sha256',
            );
            $encoder = new PasswordEncoder($config);
            $salt = StringUtil::random(32);
            $stmt = $conn->prepare("SELECT id FROM dtb_member WHERE login_id = :login_id;");
            $stmt->execute([':login_id' => $data['login_id']]);
            $row = $stmt->fetch();
            $password = $encoder->encodePassword($data['login_pass'], $salt);
            if ($row) {
                // 同一の管理者IDであればパスワードのみ更新
                $sth = $conn->prepare("UPDATE dtb_member set password = :password, salt = :salt, update_date = current_timestamp WHERE login_id = :login_id;");
                $sth->execute([
                    ':password' => $password,
                    ':salt' => $salt,
                    ':login_id' => $data['login_id'],
                ]);
            } else {
                // 新しい管理者IDが入力されたらinsert
                $sth = $conn->prepare("INSERT INTO dtb_member (login_id, password, salt, work, del_flg, authority, creator_id, sort_no, update_date, create_date,name,department,discriminator_type) VALUES (:login_id, :password , :salt , '1', '0', '0', '1', '1', current_timestamp, current_timestamp,'管理者','EC-CUBE SHOP', 'member');");
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
            $stmt->execute(array(
                ':shop_name' => $data['shop_name'],
                ':admin_mail' => $data['email'],
            ));
            $stmt->commit();
        } catch (\Exception $e) {
            $stmt->rollback();
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
     * @param EntityManager $em
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
     * @param EntityManager $em
     */
    public function sendAppData($params, EntityManager $em)
    {
        $query = http_build_query($this->createAppData($params, $em));
        $header = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: '.strlen($query),
        );
        $context = stream_context_create(
            array(
                'http' => array(
                    'method' => 'POST',
                    'header' => $header,
                    'content' => $query,
                ),
            )
        );
        file_get_contents('http://www.ec-cube.net/mall/use_site.php', false, $context);

        return $this;
    }

    /**
     * @param string $env
     * @param array $replacement
     * @return string
     */
    public function replaceEnv($env, array $replacement)
    {
        foreach ($replacement as $key => $value) {
            $env = preg_replace('/('.$key.')=(.*)/', '$1='.$value, $env);
        }
        return $env;
    }

    /**
     * @param EntityManager $em
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

            case 'pgsql':
            default:
                $sql = 'SHOW server_version';
        }

        $version = $em->createNativeQuery($sql, $rsm)
            ->getSingleScalarResult();
        return $version;
    }

    /**
     * @param string
     * @return string
     */
    public function convertAdminAllowHosts($adminAllowHosts)
    {
        if ($adminAllowHosts) {
            $adminAllowHosts = explode("\n", $adminAllowHosts);
        } else {
            $adminAllowHosts = ['127.0.0.1'];
        }
        foreach ($adminAllowHosts as &$allowHost) {
            $allowHost = '"'.trim($allowHost).'"';
        }
        return "'[".implode(',', $adminAllowHosts)."]'";
    }
}
