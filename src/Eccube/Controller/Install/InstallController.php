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
use Eccube\Form\Type\Install\Step1Type;
use Eccube\Form\Type\Install\Step3Type;
use Eccube\Form\Type\Install\Step4Type;
use Eccube\Form\Type\Install\Step5Type;
use Eccube\InstallApplication;
use Eccube\Security\Core\Encoder\PasswordEncoder;
use Eccube\Util\Str;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class InstallController
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

    public function __construct()
    {
        $this->rootDir = realpath(__DIR__.'/../../../..');
        $this->configDir = realpath($this->rootDir.'/app/config/eccube');
        $this->configDistDir = realpath($this->rootDir.'/src/Eccube/Resource/config');
        $this->cacheDir = realpath($this->rootDir.'/app/cache');
    }

    /**
     * 最初からやり直す場合、SESSION情報をクリア.
     *
     * @param InstallApplication $app
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function index(InstallApplication $app, Request $request, Session $session)
    {
        $this->removeSessionData($session);

        return $app->redirect($app->path('install_step1'));
    }

    /**
     * ようこそ.
     *
     * @param InstallApplication $app
     * @param Request $request
     * @return Response
     */
    public function step1(InstallApplication $app, Request $request, Session $session)
    {
        $form = $app['form.factory']
            ->createBuilder(Step1Type::class)
            ->getForm();

        $form->setData($this->getSessionData($session));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setSessionData($session, $form->getData());

            return $app->redirect($app->path('install_step2'));
        }

        $this->checkModules($app);

        return $app['twig']->render('step1.twig', [
            'form' => $form->createView(),
            'publicPath' => '..'.RELATIVE_PUBLIC_DIR_PATH.'/',
        ]);
    }

    /**
     * ディレクトリの書き込み権限をチェック.
     *
     * @param InstallApplication $app
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function step2(InstallApplication $app, Request $request, Session $session)
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

        return $app['twig']->render('step2.twig', [
            'protectedDirs' => $protectedDirs,
            'publicPath' => '..'.RELATIVE_PUBLIC_DIR_PATH.'/',
        ]);
    }

    /**
     * サイトの設定.
     *
     * @param InstallApplication $app
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function step3(InstallApplication $app, Request $request, Session $session)
    {
        $sessionData = $this->getSessionData($session);

        if (empty($sessionData['shop_name'])) {
            // 再インストールの場合は設定ファイルから復旧
            if (file_exists($this->configDir.'/config.php')) {
                // ショップ名/メールアドレス
                $config = require $this->configDir.'/database.php';
                $conn = $this->createConnection($config['database']);
                $stmt = $conn->query("SELECT shop_name, email01 FROM dtb_base_info WHERE id = 1;");
                $row = $stmt->fetch();
                $sessionData['shop_name'] = $row['shop_name'];
                $sessionData['email'] = $row['email01'];

                // 管理画面ルーティング
                $config = require $this->configDir.'/path.php';
                $sessionData['admin_dir'] = $config['admin_route'];

                // 管理画面許可IP
                $config = require $this->configDir.'/config.php';
                if (!empty($config['admin_allow_host'])) {
                    $sessionData['admin_allow_hosts']
                        = Str::convertLineFeed(implode("\n", $config['admin_allow_host']));
                }
                // 強制SSL
                $sessionData['admin_force_ssl'] = $config['force_ssl'];

                // ロードバランサ, プロキシサーバ
                if (!empty($config['trusted_proxies_connection_only'])) {
                    $sessionData['trusted_proxies_connection_only'] = (bool)$config['trusted_proxies_connection_only'];
                }
                if (!empty($config['trusted_proxies'])) {
                    $sessionData['trusted_proxies'] = Str::convertLineFeed(implode("\n",
                        $sessionData['trusted_proxies']));
                }
                // メール
                $file = $this->configDir.'/mail.php';
                $config = require $file;
                $sessionData['mail_backend'] = $config['mail']['transport'];
                $sessionData['smtp_host'] = $config['mail']['host'];
                $sessionData['smtp_port'] = $config['mail']['port'];
                $sessionData['smtp_username'] = $config['mail']['username'];
                $sessionData['smtp_password'] = $config['mail']['password'];
            }
        }

        $form = $app['form.factory']
            ->createBuilder(Step3Type::class)
            ->getForm();

        $form->setData($sessionData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setSessionData($session, $form->getData());

            return $app->redirect($app->path('install_step4'));
        }

        return $app['twig']->render('step3.twig', [
            'form' => $form->createView(),
            'publicPath' => '..'.RELATIVE_PUBLIC_DIR_PATH.'/',
        ]);
    }

    /**
     * データベースの設定.
     *
     * @param InstallApplication $app
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function step4(InstallApplication $app, Request $request, Session $session)
    {
        $sessionData = $this->getSessionData($session);

        if (empty($sessionData['database'])) {
            // 再インストールの場合は設定ファイルから復旧.
            $file = $this->configDir.'/database.php';
            if (file_exists($file)) {
                // データベース設定
                $config = require $file;
                $database = $config['database'];
                $sessionData['database'] = $database['driver'];
                if ($database['driver'] != 'pdo_sqlite') {
                    $sessionData['database_host'] = $database['host'];
                    $sessionData['database_port'] = $database['port'];
                    $sessionData['database_name'] = $database['dbname'];
                    $sessionData['database_user'] = $database['user'];
                    $sessionData['database_password'] = $database['password'];
                }
            }
        }

        $form = $app['form.factory']
            ->createBuilder(Step4Type::class)
            ->getForm();

        $form->setData($sessionData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setSessionData($session, $form->getData());

            return $app->redirect($app->path('install_step5'));
        }

        return $app['twig']->render('step4.twig', [
            'form' => $form->createView(),
            'publicPath' => '..'.RELATIVE_PUBLIC_DIR_PATH.'/',
        ]);
    }

    /**
     * データベースの初期化.
     *
     * @param InstallApplication $app
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function step5(InstallApplication $app, Request $request, Session $session)
    {
        $form = $app['form.factory']
            ->createBuilder(Step5Type::class)
            ->getForm();

        $sessionData = $this->getSessionData($session);
        $form->setData($sessionData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->createDatabaseConfigFile($sessionData);
            $this->createMailConfigFile($sessionData);
            $this->createPathConfigFile($sessionData);

            $params = require $this->configDir.'/database.php';
            $conn = $this->createConnection($params['database']);
            $em = $this->createEntityManager($conn);
            $migration = $this->createMigration($conn);

            if ($form['no_update']->getData()) {
                // データベースを初期化しない場合、auth_magicは初期化しない
                $this->createConfigFile($sessionData, false);
                $config = require $this->configDir.'/config.php';
                $this->update($conn, [
                    'auth_magic' => $config['auth_magic'],
                    'login_id' => $sessionData['login_id'],
                    'login_pass' => $sessionData['login_pass'],
                    'shop_name' => $sessionData['shop_name'],
                    'email' => $sessionData['email'],
                ]);
            } else {
                $this->createConfigFile($sessionData);
                $config = require $this->configDir.'/config.php';
                $this->dropTables($em);
                $this->createTables($em);
                $this->importCsv($em);
                $this->migrate($migration);
                $this->insert($conn, [
                    'auth_magic' => $config['auth_magic'],
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
                $this->sendAppData($params);
            }

            $this->removeSessionData($session);

            return $app->redirect($app->path('install_complete'));
        }

        return $app['twig']->render('step5.twig', [
            'form' => $form->createView(),
            'publicPath' => '..'.RELATIVE_PUBLIC_DIR_PATH.'/',
        ]);
    }

    //    インストール完了
    public function complete(InstallApplication $app, Request $request)
    {
        $config = require $this->configDir.'/config.php';
        if (isset($config['trusted_proxies_connection_only']) && !empty($config['trusted_proxies_connection_only'])) {
            Request::setTrustedProxies(array_merge(array($request->server->get('REMOTE_ADDR')),
                $config['trusted_proxies']));
        } elseif (isset($config['trusted_proxies']) && !empty($config['trusted_proxies'])) {
            Request::setTrustedProxies($config['trusted_proxies']);
        }

        $pathConfig = require $this->configDir.'/path.php';
        $host = $request->getSchemeAndHttpHost();
        $basePath = $request->getBasePath();
        $adminUrl = $host.$basePath.'/'.$pathConfig['admin_route'];

        return $app['twig']->render('complete.twig', [
            'admin_url' => $adminUrl,
            'publicPath' => '..'.RELATIVE_PUBLIC_DIR_PATH.'/',
        ]);
    }

    private function getSessionData(Session $session)
    {
        return $session->get('eccube.session.install', []);
    }

    private function removeSessionData(Session $session)
    {
        $session->remove('eccube.session.install');
    }

    private function setSessionData(Session $session, $data = [])
    {
        $data = array_replace_recursive($this->getSessionData($session), $data);
        $session->set('eccube.session.install', $data);
    }

    private function checkModules($app)
    {
        foreach ($this->requiredModules as $module) {
            if (!extension_loaded($module)) {
                $app->addDanger('[必須] '.$module.' 拡張モジュールが有効になっていません。', 'install');
            }
        }
        if (!extension_loaded('pdo_mysql') && !extension_loaded('pdo_pgsql')) {
            $app->addDanger('[必須] '.'pdo_pgsql又はpdo_mysql 拡張モジュールを有効にしてください。', 'install');
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
                $app->addInfo('[推奨] WinCache 拡張モジュールが有効になっていません。', 'install');
            }
        } else {
            if (!extension_loaded('apc')) {
                $app->addInfo('[推奨] APC 拡張モジュールが有効になっていません。', 'install');
            }
        }
        if (isset($_SERVER['SERVER_SOFTWARE']) && strpos('Apache', $_SERVER['SERVER_SOFTWARE']) !== false) {
            if (!function_exists('apache_get_modules')) {
                $app->addWarning('mod_rewrite が有効になっているか不明です。', 'install');
            } elseif (!in_array('mod_rewrite', apache_get_modules())) {
                $app->addDanger('[必須] '.'mod_rewriteを有効にしてください。', 'install');
            }
        } elseif (isset($_SERVER['SERVER_SOFTWARE']) && strpos('Microsoft-IIS',
                $_SERVER['SERVER_SOFTWARE']) !== false
        ) {
            // iis
        } elseif (isset($_SERVER['SERVER_SOFTWARE']) && strpos('nginx', $_SERVER['SERVER_SOFTWARE']) !== false) {
            // nginx
        }
    }

    private function createConnection(array $params)
    {
        $conn = DriverManager::getConnection($params);
        $conn->ping();

        return $conn;
    }

    private function createEntityManager(Connection $conn)
    {
        $paths = [
            $this->rootDir.'/src/Eccube/Entity',
            $this->rootDir.'/app/Acme/Entity',
        ];
        $config = Setup::createAnnotationMetadataConfiguration($paths, true, null, null, false);
        $em = EntityManager::create($conn, $config);

        return $em;
    }

    private function createMigration(Connection $conn)
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

    private function dropTables(EntityManager $em)
    {
        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema($metadatas);
        $em->getConnection()->executeQuery('DROP TABLE IF EXISTS doctrine_migration_versions');
    }

    private function createTables(EntityManager $em)
    {
        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema($metadatas);
    }

    private function importCsv(EntityManager $em)
    {
        $loader = new \Eccube\Doctrine\Common\CsvDataFixtures\Loader();
        $loader->loadFromDirectory($this->rootDir.'/src/Eccube/Resource/doctrine/import_csv');
        $executer = new \Eccube\Doctrine\Common\CsvDataFixtures\Executor\DbalExecutor($em);
        $fixtures = $loader->getFixtures();
        $executer->execute($fixtures);
    }

    private function insert(Connection $conn, array $data)
    {
        $conn->beginTransaction();
        try {
            $config = array(
                'auth_type' => '',
                'auth_magic' => $data['auth_magic'],
                'password_hash_algos' => 'sha256',
            );
            $encoder = new PasswordEncoder($config);
            $salt = Str::random(32);
            $password = $encoder->encodePassword($data['login_pass'], $salt);

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
                ? $conn->fetchColumn("select nextval('dtb_member_member_id_seq')")
                : null;

            $conn->insert('dtb_member', [
                'member_id' => $member_id,
                'login_id' => $data['login_id'],
                'password' => $password,
                'salt' => $salt,
                'work' => 1,
                'authority' => 0,
                'creator_id' => 1,
                'rank' => 1,
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

    private function update(Connection $conn, array $data)
    {
        $conn->beginTransaction();
        try {
            $config = array(
                'auth_type' => '',
                'auth_magic' => $data['auth_magic'],
                'password_hash_algos' => 'sha256',
            );
            $encoder = new PasswordEncoder($config);
            $salt = Str::random(32);
            $stmt = $conn->prepare("SELECT member_id FROM dtb_member WHERE login_id = :login_id;");
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
                $sth = $conn->prepare("INSERT INTO dtb_member (login_id, password, salt, work, del_flg, authority, creator_id, rank, update_date, create_date,name,department,discriminator_type) VALUES (:login_id, :password , :salt , '1', '0', '0', '1', '1', current_timestamp, current_timestamp,'管理者','EC-CUBE SHOP', 'member');");
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

    private function migrate(Migration $migration)
    {
        try {
            // nullを渡すと最新バージョンまでマイグレートする
            $migration->migrate(null, false);
        } catch (MigrationException $e) {

        }
    }

    private function createPhp($path, $config)
    {
        $content = var_export($config, true);
        $content = '<?php return '.$content.';'.PHP_EOL;
        file_put_contents($path, $content);
    }

    private function createConfigFile(array $data, $updateAuthMagic = true)
    {
        $file = $this->configDir.'/config.php';
        $config = [];
        if (file_exists($file)) {
            $config = require $file;
            unlink($file);
        }
        if ($updateAuthMagic) {
            $authMagic = Str::random(32);
        } else {
            if (empty($config['auth_magic'])) {
                $authMagic = Str::random(32);
            } else {
                $authMagic = $config['auth_magic'];
            }
        }
        $allowHost = Str::convertLineFeed($data['admin_allow_hosts']);
        if (empty($allowHost)) {
            $adminAllowHosts = array();
        } else {
            $adminAllowHosts = explode("\n", $allowHost);
        }
        $trustedProxies = Str::convertLineFeed($data['trusted_proxies']);
        if (empty($trustedProxies)) {
            $adminTrustedProxies = array();
        } else {
            $adminTrustedProxies = explode("\n", $trustedProxies);
            // ループバックアドレスを含める
            $adminTrustedProxies = array_merge($adminTrustedProxies, array('127.0.0.1/8', '::1'));
        }
        if ($data['trusted_proxies_connection_only']) {
            // ループバックアドレスを含める
            $adminTrustedProxies = array('127.0.0.1/8', '::1');
        }

        $config = require $this->configDistDir.'/config.php';
        $config['eccube_install'] = 1;
        $config['auth_magic'] = $authMagic;
        $config['force_ssl'] = $data['admin_force_ssl'];
        $config['admin_allow_host'] = $adminAllowHosts;
        $config['trusted_proxies_connection_only'] = $data['trusted_proxies_connection_only'];
        $config['trusted_proxies'] = $adminTrustedProxies;

        $this->createPhp($file, $config);
    }

    private function createDatabaseConfigFile(array $data)
    {
        $file = $this->configDir.'/database.php';

        if (file_exists($file)) {
            unlink($file);
        }

        if ($data['database'] === 'pdo_sqlite') {
            $config = require $this->configDistDir.'/database_sqlite3.php';
            $config['database']['path'] = realpath($this->configDir.'/eccube.db');
        } else {
            switch ($data['database']) {
                case 'pdo_pgsql':
                    if (empty($data['db_port'])) {
                        $data['db_port'] = '5432';
                    }
                    $data['db_driver'] = 'pdo_pgsql';
                    break;
                case 'pdo_mysql':
                    if (empty($data['db_port'])) {
                        $data['db_port'] = '3306';
                    }
                    $data['db_driver'] = 'pdo_mysql';
                    break;
            }
            $config = require $this->configDistDir.'/database.php';
            $config['database']['driver'] = $data['db_driver'];
            $config['database']['host'] = $data['database_host'];
            $config['database']['dbname'] = $data['database_name'];
            $config['database']['port'] = $data['database_port'];
            $config['database']['user'] = $data['database_user'];
            $config['database']['password'] = $data['database_password'];
        }

        $this->createPhp($file, $config);
    }

    private function createMailConfigFile(array $data)
    {
        $file = $this->configDir.'/mail.php';

        if (file_exists($file)) {
            unlink($file);
        }

        $config = require $this->configDistDir.'/mail.php';
        $config['mail']['transport'] = $data['mail_backend'];
        $config['mail']['host'] = $data['smtp_host'];
        $config['mail']['port'] = $data['smtp_port'];
        $config['mail']['username'] = $data['smtp_username'];
        $config['mail']['password'] = $data['smtp_password'];

        $this->createPhp($file, $config);
    }

    private function createPathConfigFile($data)
    {
        $file = $this->configDir.'/path.php';

        if (file_exists($file)) {
            unlink($file);
        }

        $config = require $this->configDistDir.'/path.php';
        $config['admin_route'] = $data['admin_dir'];

        $this->createPhp($file, $config);
    }

    private function sendAppData($params)
    {
        $config = require $this->configDir.'/database.php';
        $conn = $this->createConnection($config['database']);
        $stmt = $conn->query('select version() as v');
        $version = '';

        foreach ($stmt as $row) {
            $version = $row['v'];
        }

        if ($config['database']['driver'] === 'pdo_mysql') {
            $db_ver = 'MySQL:'.$version;
        } else {
            $db_ver = $version;
        }

        $data = http_build_query(
            array(
                'site_url' => $params['http_url'],
                'shop_name' => $params['shop_name'],
                'cube_ver' => Constant::VERSION,
                'php_ver' => phpversion(),
                'db_ver' => $db_ver,
                'os_type' => php_uname(),
            )
        );
        $header = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: '.strlen($data),
        );
        $context = stream_context_create(
            array(
                'http' => array(
                    'method' => 'POST',
                    'header' => $header,
                    'content' => $data,
                ),
            )
        );
        file_get_contents('http://www.ec-cube.net/mall/use_site.php', false, $context);

        return $this;
    }

    /**
     * マイグレーション画面を表示する.
     *
     * @param InstallApplication $app
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function migration(InstallApplication $app, Request $request)
    {
        return $app['twig']->render('migration.twig', array(
            'publicPath' => '..'.RELATIVE_PUBLIC_DIR_PATH.'/',
        ));
    }

    /**
     * インストール済プラグインの一覧を表示する.
     * プラグインがインストールされていない場合は, マイグレーション実行画面へリダイレクトする.
     *
     * @param InstallApplication $app
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function migration_plugin(InstallApplication $app, Request $request)
    {
        $eccube = \Eccube\Application::getInstance();
        $eccube->initialize();
        $eccube->boot();
        $pluginRepository = $eccube['orm.em']->getRepository('Eccube\Entity\Plugin');
        $Plugins = $pluginRepository->findBy(array('del_flg' => Constant::DISABLED));
        if (empty($Plugins)) {
            // インストール済プラグインがない場合はマイグレーション実行画面へリダイレクト.
            return $app->redirect($app->path('migration_end'));
        } else {
            return $app['twig']->render('migration_plugin.twig', array(
                'Plugins' => $Plugins,
                'version' => Constant::VERSION,
                'publicPath' => '..'.RELATIVE_PUBLIC_DIR_PATH.'/',
            ));
        }
    }

    /**
     * マイグレーションを実行し, 完了画面を表示させる
     *
     * @param InstallApplication $app
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function migration_end(InstallApplication $app, Request $request)
    {
        $this->doMigrate();
        $config_app = new \Eccube\Application(); // install用のappだとconfigが取れないので
        $config_app->initialize();
        $config_app->boot();
        \Eccube\Util\Cache::clear($config_app, true);

        return $app['twig']->render('migration_end.twig', array(
            'publicPath' => '..'.RELATIVE_PUBLIC_DIR_PATH.'/',
        ));
    }
}