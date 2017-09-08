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
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Migrations\MigrationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Common\Constant;
use Eccube\Form\Type\Install\Step1Type;
use Eccube\Form\Type\Install\Step3Type;
use Eccube\Form\Type\Install\Step4Type;
use Eccube\Form\Type\Install\Step5Type;
use Eccube\InstallApplication;
use Eccube\Plugin\ConfigManager as PluginConfigManager;
use Eccube\Util\Str;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class InstallController
{
    const MCRYPT = 'mcrypt';
    private $app;
    private $PDO;
    private $config_path;
    private $dist_path;
    private $cache_path;
    private $session_data;
    private $required_modules = array('pdo', 'phar', 'mbstring', 'zlib', 'ctype', 'session', 'JSON', 'xml', 'libxml', 'OpenSSL', 'zip', 'cURL', 'fileinfo');
    private $recommended_module = array('hash', self::MCRYPT);
    const SESSION_KEY = 'eccube.session.install';
    public function __construct()
    {
        $this->config_path = __DIR__ . '/../../../../app/config/eccube';
        $this->dist_path = __DIR__ . '/../../Resource/config';
        $this->cache_path = __DIR__ . '/../../../../app/cache';
    }
    private function isValid(Request $request, Form $form)
    {
        $session = $request->getSession();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $sessionData = $session->get(self::SESSION_KEY) ?: array();
                $formData = array_replace_recursive($sessionData, $form->getData());
                $session->set(self::SESSION_KEY, $formData);
                return true;
            }
        }
        return false;
    }
    private function getSessionData(Request $request)
    {
        return $this->session_data = $request->getSession()->get(self::SESSION_KEY);
    }
    // 最初からやり直す場合、SESSION情報をクリア
    public function index(InstallApplication $app, Request $request)
    {
        $request->getSession()->remove(self::SESSION_KEY);
        return $app->redirect($app->path('install_step1'));
    }
    // ようこそ
    public function step1(InstallApplication $app, Request $request)
    {
        $form = $app['form.factory']
            ->createBuilder(Step1Type::class)
            ->getForm();
        $sessionData = $this->getSessionData($request);
        $form->setData($sessionData);
        if ($this->isValid($request, $form)) {
            return $app->redirect($app->path('install_step2'));
        }
        $this->checkModules($app);
        return $app['twig']->render('step1.twig', array(
            'form' => $form->createView(),
            'publicPath' => '..' . RELATIVE_PUBLIC_DIR_PATH . '/',
        ));
    }
    // 権限チェック
    public function step2(InstallApplication $app, Request $request)
    {
        $this->getSessionData($request);
        $protectedDirs = $this->getProtectedDirs();
        // 権限がある場合, キャッシュディレクトリをクリア
        if (empty($protectedDirs)) {
            $finder = Finder::create()
                ->in($this->cache_path)
                ->notName('.gitkeep')
                ->files();
            $fs = new Filesystem();
            $fs->remove($finder);
        }
        return $app['twig']->render('step2.twig', array(
            'protectedDirs' => $protectedDirs,
            'publicPath' => '..' . RELATIVE_PUBLIC_DIR_PATH . '/',
        ));
    }
    //    サイトの設定
    public function step3(InstallApplication $app, Request $request)
    {
        $form = $app['form.factory']
            ->createBuilder(Step3Type::class)
            ->getForm();
        $sessionData = $this->getSessionData($request);
        if (empty($sessionData['shop_name'])) {
            $config_file = $this->config_path . '/config.php';
            $fs = new Filesystem();
            if ($fs->exists($config_file)) {
                // すでに登録されていた場合、登録データを表示
                $this->setPDO();
                $stmt = $this->PDO->query("SELECT shop_name, email01 FROM dtb_base_info WHERE id = 1;");
                foreach ($stmt as $row) {
                    $sessionData['shop_name'] = $row['shop_name'];
                    $sessionData['email'] = $row['email01'];
                }
                // セキュリティの設定
                $config_file = $this->config_path . '/path.php';
                $config = require $config_file;
                $sessionData['admin_dir'] = $config['admin_route'];
                $config_file = $this->config_path . '/config.php';
                $config = require $config_file;
                $allowHost = $config['admin_allow_host'];
                if (count($allowHost) > 0) {
                    $sessionData['admin_allow_hosts'] = Str::convertLineFeed(implode("\n", $allowHost));
                }
                $sessionData['admin_force_ssl'] = (bool) $config['force_ssl'];
                // ロードバランサー、プロキシサーバ設定
                if (isset($config['trusted_proxies_connection_only'])) {
                    $sessionData['trusted_proxies_connection_only'] = (bool)$config['trusted_proxies_connection_only'];
                }
                if (isset($config['trusted_proxies'])) {
                    $trustedProxies = $config['trusted_proxies'];
                    if (count($trustedProxies) > 0) {
                        $sessionData['trusted_proxies'] = Str::convertLineFeed(implode("\n", $trustedProxies));
                    }
                }
                // メール設定
                $config_file = $this->config_path . '/mail.php';
                $config = require $config_file;
                $mail = $config['mail'];
                $sessionData['mail_backend'] = $mail['transport'];
                $sessionData['smtp_host'] = $mail['host'];
                $sessionData['smtp_port'] = $mail['port'];
                $sessionData['smtp_username'] = $mail['username'];
                $sessionData['smtp_password'] = $mail['password'];
            } else {
                // 初期値にmailを設定
                $sessionData['mail_backend'] = 'mail';
            }
        }
        $form->setData($sessionData);
        if ($this->isValid($request, $form)) {
            $data = $form->getData();
            return $app->redirect($app->path('install_step4'));
        }
        return $app['twig']->render('step3.twig', array(
            'form' => $form->createView(),
            'publicPath' => '..' . RELATIVE_PUBLIC_DIR_PATH . '/',
        ));
    }
    //    データベースの設定
    public function step4(InstallApplication $app, Request $request)
    {
        $form = $app['form.factory']
            ->createBuilder(Step4Type::class)
            ->getForm();
        $sessionData = $this->getSessionData($request);
        if (empty($sessionData['database'])) {
            $config_file = $this->config_path . '/database.php';
            $fs = new Filesystem();
            if ($fs->exists($config_file)) {
                // すでに登録されていた場合、登録データを表示
                // データベース設定
                $config = require $config_file;
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
        $form->setData($sessionData);
        if ($this->isValid($request, $form)) {
            return $app->redirect($app->path('install_step5'));
        }
        return $app['twig']->render('step4.twig', array(
            'form' => $form->createView(),
            'publicPath' => '..' . RELATIVE_PUBLIC_DIR_PATH . '/',
        ));
    }
    //    データベースの初期化
    public function step5(InstallApplication $app, Request $request)
    {
        set_time_limit(0);
        $this->app = $app;
        $form = $app['form.factory']
            ->createBuilder(Step5Type::class)
            ->getForm();
        $sessionData = $this->getSessionData($request);
        $form->setData($sessionData);
        if ($this->isValid($request, $form)) {
            $this
                ->createDatabaseYamlFile($sessionData)
                ->createMailYamlFile($sessionData)
                ->createPathYamlFile($sessionData, $request);
            if (!$form['no_update']->getData()) {
                set_time_limit(0);
                $this->createConfigYamlFile($sessionData);
                $this
                    ->setPDO()
                    ->dropTables()
                    ->createTables()
                    ->importCsv()
                    ->doMigrate()
                    ->insert();
            } else {
                // データベースを初期化しない場合、auth_magicは初期化しない
                $this->createConfigYamlFile($sessionData, false);
                $this
                    ->setPDO()
                    ->update();
            }
            if (isset($sessionData['agree']) && $sessionData['agree'] == '1') {
                $host = $request->getSchemeAndHttpHost();
                $basePath = $request->getBasePath();
                $params = array(
                    'http_url' => $host . $basePath,
                    'shop_name' => $sessionData['shop_name'],
                );
                $this->sendAppData($params);
            }
            $this->addInstallStatus();
            $request->getSession()->remove(self::SESSION_KEY);
            return $app->redirect($app->path('install_complete'));
        }
        return $app['twig']->render('step5.twig', array(
            'form' => $form->createView(),
            'publicPath' => '..' . RELATIVE_PUBLIC_DIR_PATH . '/',
        ));
    }
    //    インストール完了
    public function complete(InstallApplication $app, Request $request)
    {
        $config_yml = $this->config_path . '/config.php';
        $config = require $config_yml;
        $config_path = $this->config_path . '/path.php';
        $path_yml = require $config_path;
        $config = array_replace_recursive($path_yml, $config);
        if (isset($config['trusted_proxies_connection_only']) && !empty($config['trusted_proxies_connection_only'])) {
            Request::setTrustedProxies(array_merge(array($request->server->get('REMOTE_ADDR')), $config['trusted_proxies']));
        } elseif (isset($config['trusted_proxies']) && !empty($config['trusted_proxies'])) {
            Request::setTrustedProxies($config['trusted_proxies']);
        }
        $host = $request->getSchemeAndHttpHost();
        $basePath = $request->getBasePath();
        $adminUrl = $host . $basePath . '/' . $config['admin_dir'];
        return $app['twig']->render('complete.twig', array(
            'admin_url' => $adminUrl,
            'publicPath' => '..' . RELATIVE_PUBLIC_DIR_PATH . '/',
        ));
    }
    private function resetNatTimer()
    {
        // NATの無通信タイマ対策（仮）
        echo str_repeat(' ', 4 * 1024);
        ob_flush();
        flush();
    }
    private function checkModules($app)
    {
        foreach ($this->required_modules as $module) {
            if (!extension_loaded($module)) {
                $app->addDanger('[必須] ' . $module . ' 拡張モジュールが有効になっていません。', 'install');
            }
        }
        if (!extension_loaded('pdo_mysql') && !extension_loaded('pdo_pgsql')) {
            $app->addDanger('[必須] ' . 'pdo_pgsql又はpdo_mysql 拡張モジュールを有効にしてください。', 'install');
        }
        foreach ($this->recommended_module as $module) {
            if (!extension_loaded($module)) {
                if ($module == self::MCRYPT && PHP_VERSION_ID >= 70100) {
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
                $app->addDanger('[必須] ' . 'mod_rewriteを有効にしてください。', 'install');
            }
        } elseif (isset($_SERVER['SERVER_SOFTWARE']) && strpos('Microsoft-IIS', $_SERVER['SERVER_SOFTWARE']) !== false) {
            // iis
        } elseif (isset($_SERVER['SERVER_SOFTWARE']) && strpos('nginx', $_SERVER['SERVER_SOFTWARE']) !== false) {
            // nginx
        }
    }
    private function setPDO()
    {
        $config_file = $this->config_path . '/database.php';
        $config = require $config_file;
        try {
            $this->PDO = \Doctrine\DBAL\DriverManager::getConnection($config['database'], new \Doctrine\DBAL\Configuration());
            $this->PDO->connect();
        } catch (\Exception $e) {
            $this->PDO->close();
            throw $e;
        }
        return $this;
    }
    private function dropTables()
    {
        $this->resetNatTimer();
        $em = $this->getEntityManager();
        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema($metadatas);
        $em->getConnection()->executeQuery('DROP TABLE IF EXISTS doctrine_migration_versions');
        return $this;
    }
    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        if (!isset($this->app['orm.em'])) {
            $config_file = $this->config_path . '/database.php';
            $database = require $config_file;
            $this->app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
                'db.options' => $database['database']
            ));
            $ormMappings = array(
                array(
                    'type' => 'annotation',
                    'namespace' => 'Eccube\Entity',
                    'path' => array(
                        __DIR__ . '/../../Entity',
                    ),
                    'use_simple_annotation_reader' => false,
                ),
                array(  // TODO 暫定
                    'type' => 'annotation',
                    'namespace' => 'Acme\Entity',
                    'path' => array(
                        __DIR__.'/../../../../app/Acme/Entity',
                    ),
                    'use_simple_annotation_reader' => false,
                )
            );
            // XXX 同梱したプラグインがエラーになるため暫定
            $pluginConfigs = PluginConfigManager::getPluginConfigAll();
            foreach ($pluginConfigs as $code) {
                $config = $code['config'];
                // Doctrine Extend
                if (isset($config['orm.path']) && is_array($config['orm.path'])) {
                    $paths = array();
                    foreach ($config['orm.path'] as $path) {
                        $paths[] = __DIR__.'/../../../../app/Plugin/'.$config['code'].$path;
                    }
                    $ormMappings[] = array(
                        'type' => 'annotation',
                        'namespace' => 'Plugin\\'.$config['code'].'\\Entity',
                        'path' => $paths,
                        'use_simple_annotation_reader' => false,
                    );
                }
            }
            $this->app->register(new \Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), array(
                'orm.proxies_dir' => __DIR__ . '/../../app/cache/doctrine',
                'orm.em.options' => array(
                    'mappings' => $ormMappings
                )
            ));
        }
        return $em = $this->app['orm.em'];
    }
    private function createTables()
    {
        $this->resetNatTimer();
        $em = $this->getEntityManager();
        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema($metadatas);
        return $this;
    }
    private function importCsv() {
        $em = $this->getEntityManager();
        $loader = new \Eccube\Doctrine\Common\CsvDataFixtures\Loader();
        $loader->loadFromDirectory(__DIR__.'/../../Resource/doctrine/import_csv');
        $Executor = new \Eccube\Doctrine\Common\CsvDataFixtures\Executor\DbalExecutor($em);
        $fixtures = $loader->getFixtures();
        $Executor->execute($fixtures);
        return $this;
    }
    private function insert()
    {
        $this->resetNatTimer();
        $config_file = $this->config_path . '/database.php';
        $database = require $config_file;
        $config['database'] = $database['database'];
        $config_file = $this->config_path . '/config.php';
        $baseConfig = require $config_file;
        $config['config'] = $baseConfig;
        $this->PDO->beginTransaction();
        try {
            $config = array(
                'auth_type' => '',
                'auth_magic' => $config['config']['auth_magic'],
                'password_hash_algos' => 'sha256',
            );
            $passwordEncoder = new \Eccube\Security\Core\Encoder\PasswordEncoder($config);
            $salt = \Eccube\Util\Str::random(32);
            $encodedPassword = $passwordEncoder->encodePassword($this->session_data['login_pass'], $salt);
            $sth = $this->PDO->prepare("INSERT INTO dtb_base_info (
                id,
                shop_name,
                email01,
                email02,
                email03,
                email04,
                update_date,
                option_product_tax_rule,
                discriminator_type
            ) VALUES (
                1,
                :shop_name,
                :admin_mail,
                :admin_mail,
                :admin_mail,
                :admin_mail,
                current_timestamp,
                0,
                'baseinfo');");
            $sth->execute(array(
                ':shop_name' => $this->session_data['shop_name'],
                ':admin_mail' => $this->session_data['email']
            ));
            $sth = $this->PDO->prepare("INSERT INTO dtb_member (member_id, login_id, password, salt, work, del_flg, authority, creator_id, rank, update_date, create_date,name,department, discriminator_type) VALUES (2, :login_id, :admin_pass , :salt , '1', '0', '0', '1', '1', current_timestamp, current_timestamp,'管理者','EC-CUBE SHOP', 'member');");
            $sth->execute(array(':login_id' => $this->session_data['login_id'], ':admin_pass' => $encodedPassword, ':salt' => $salt));
            $this->PDO->commit();
        } catch (\Exception $e) {
            $this->PDO->rollback();
            throw $e;
        }
        return $this;
    }
    private function update()
    {
        $this->resetNatTimer();
        $config_file = $this->config_path . '/database.php';
        $database = require $config_file;
        $config['database'] = $database['database'];
        $config_file = $this->config_path . '/config.php';
        $baseConfig = require $config_file;
        $config['config'] = $baseConfig;
        $this->PDO->beginTransaction();
        try {
            $config = array(
                'auth_type' => '',
                'auth_magic' => $config['config']['auth_magic'],
                'password_hash_algos' => 'sha256',
            );
            $passwordEncoder = new \Eccube\Security\Core\Encoder\PasswordEncoder($config);
            $salt = \Eccube\Util\Str::random(32);
            $stmt = $this->PDO->prepare("SELECT member_id FROM dtb_member WHERE login_id = :login_id;");
            $stmt->execute(array(':login_id' => $this->session_data['login_id']));
            $rs = $stmt->fetch();
            $encodedPassword = $passwordEncoder->encodePassword($this->session_data['login_pass'], $salt);
            if ($rs) {
                // 同一の管理者IDであればパスワードのみ更新
                $sth = $this->PDO->prepare("UPDATE dtb_member set password = :admin_pass, salt = :salt, update_date = current_timestamp WHERE login_id = :login_id;");
                $sth->execute(array(':admin_pass' => $encodedPassword, ':salt' => $salt, ':login_id' => $this->session_data['login_id']));
            } else {
                // 新しい管理者IDが入力されたらinsert
                $sth = $this->PDO->prepare("INSERT INTO dtb_member (login_id, password, salt, work, del_flg, authority, creator_id, rank, update_date, create_date,name,department,discriminator_type) VALUES (:login_id, :admin_pass , :salt , '1', '0', '0', '1', '1', current_timestamp, current_timestamp,'管理者','EC-CUBE SHOP', 'member');");
                $sth->execute(array(':login_id' => $this->session_data['login_id'], ':admin_pass' => $encodedPassword, ':salt' => $salt));
            }
            $sth = $this->PDO->prepare('UPDATE dtb_base_info set
                shop_name = :shop_name,
                email01 = :admin_mail,
                email02 = :admin_mail,
                email03 = :admin_mail,
                email04 = :admin_mail,
                update_date = current_timestamp
            WHERE id = 1;');
            $sth->execute(array(
                ':shop_name' => $this->session_data['shop_name'],
                ':admin_mail' => $this->session_data['email']
            ));
            $this->PDO->commit();
        } catch (\Exception $e) {
            $this->PDO->rollback();
            throw $e;
        }
        return $this;
    }
    private function getMigration()
    {
        $app = \Eccube\Application::getInstance();
        $app->initialize();
        $app->boot();
        $config = new Configuration($app['db']);
        $config->setMigrationsNamespace('DoctrineMigrations');
        $migrationDir = __DIR__ . '/../../Resource/doctrine/migration';
        $config->setMigrationsDirectory($migrationDir);
        $config->registerMigrationsFromDirectory($migrationDir);
        $migration = new Migration($config);
        $migration->setNoMigrationException(true);
        return $migration;
    }
    private function doMigrate()
    {
        try {
            $migration = $this->getMigration();
            // DBとのコネクションを維持するためpingさせる
            if (is_null($this->PDO)) {
                $this->setPDO();
            }
            $this->PDO->ping();
            // nullを渡すと最新バージョンまでマイグレートする
            $migration->migrate(null, false);
        } catch (MigrationException $e) {

        }
        return $this;
    }
    private function getProtectedDirs()
    {
        $protectedDirs = array();
        $base = __DIR__ . '/../../../..';
        $dirs = array(
            '/html',
            '/app',
            '/app/template',
            '/app/cache',
            '/app/config',
            '/app/config/eccube',
            '/app/log',
            '/app/Plugin',
        );
        foreach ($dirs as $dir) {
            if (!is_writable($base . $dir)) {
                $protectedDirs[] = $dir;
            }
        }
        return $protectedDirs;
    }
    private function createConfigYamlFile($data, $auth = true)
    {
        $fs = new Filesystem();
        $config_file = $this->config_path . '/config.php';
        if ($fs->exists($config_file)) {
            $config = require $config_file;
            $fs->remove($config_file);
        }
        if ($auth) {
            $auth_magic = Str::random(32);
        } else {
            if (isset($config['auth_magic'])) {
                $auth_magic = $config['auth_magic'];
            } else {
                $auth_magic = Str::random(32);
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

        $config = require $this->dist_path . '/config.php';
        $config['eccube_install'] = 0;
        $config['auth_magic'] = $auth_magic;
        $config['shop_name'] = $data['shop_name'];
        $config['force_ssl'] = $data['admin_force_ssl'];
        $config['admin_allow_host'] = $adminAllowHosts;
        $config['trusted_proxies_connection_only'] = $data['trusted_proxies_connection_only'];
        $config['trusted_proxies'] = $adminTrustedProxies;

        $this->createPhp($config_file, $config);

        return $this;
    }

    private function createPhp($path, $config)
    {
        $content = var_export($config, true);
        $content = '<?php return '.$content.';'.PHP_EOL;
        $fs = new Filesystem();
        $fs->dumpFile($path, $content);
    }

    private function addInstallStatus()
    {
        $config_file = $this->config_path . '/config.php';
        $config = require $config_file;
        $config['eccube_install'] = 1;

        $this->createPhp($config_file, $config);

        return $this;
    }
    private function createDatabaseYamlFile($data)
    {
        $fs = new Filesystem();
        $config_file = $this->config_path . '/database.php';
        if ($fs->exists($config_file)) {
            $fs->remove($config_file);
        }
        if ($data['database'] != 'pdo_sqlite') {
            switch ($data['database'])
            {
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

            $config = $this->dist_path . '/database.php';
            $config['database']['driver'] = $data['db_driver'];
            $config['database']['host'] = $data['database_host'];
            $config['database']['dbname'] = $data['database_name'];
            $config['database']['port'] = $data['database_port'];
            $config['database']['user'] = $data['database_user'];
            $config['database']['password'] = $data['database_password'];

        } else {
            $config = $this->dist_path . '/database_sqlite3.php';
            $config['database']['driver'] = 'pdo_sqlite';
            $config['database']['path'] = realpath($this->config_path . '/eccube.db');
        }

        $this->createPhp($config_file, $config);

        return $this;
    }
    private function createMailYamlFile($data)
    {
        $fs = new Filesystem();
        $config_file = $this->config_path . '/mail.php';
        if ($fs->exists($config_file)) {
            $fs->remove($config_file);
        }

        $config = $this->dist_path.'/mail.php';
        $config['mail']['transport'] = $data['mail_backend'];
        $config['mail']['host'] = $data['smtp_host'];
        $config['mail']['port'] = $data['smtp_port'];
        $config['mail']['username'] = $data['smtp_username'];
        $config['mail']['password'] = $data['smtp_password'];

        $this->createPhp($config_file, $config);

        return $this;
    }
    private function createPathYamlFile($data, Request $request)
    {
        $fs = new Filesystem();
        $config_file = $this->config_path . '/path.php';
        if ($fs->exists($config_file)) {
            $fs->remove($config_file);
        }
        $ADMIN_ROUTE = $data['admin_dir'];
        $TEMPLATE_CODE = 'default';
        $USER_DATA_ROUTE = 'user_data';
        $ROOT_DIR = '%ROOT_DIR%';
        $ROOT_URLPATH = $request->getBasePath();
        $ROOT_PUBLIC_URLPATH = $ROOT_URLPATH . RELATIVE_PUBLIC_DIR_PATH;
        $target = array('${ADMIN_ROUTE}', '${TEMPLATE_CODE}', '${USER_DATA_ROUTE}', '${ROOT_DIR}', '${ROOT_URLPATH}', '${ROOT_PUBLIC_URLPATH}');
        $replace = array($ADMIN_ROUTE, $TEMPLATE_CODE, $USER_DATA_ROUTE, $ROOT_DIR, $ROOT_URLPATH, $ROOT_PUBLIC_URLPATH);

        $config = require $this->dist_path . '/path.php';

        $this->createPhp($config_file, $config);

        return $this;
    }
    private function sendAppData($params)
    {
        $config_file = $this->config_path . '/database.php';
        $db_config = require $config_file;
        $this->setPDO();
        $stmt = $this->PDO->query('select version() as v');
        $version = '';
        foreach ($stmt as $row) {
            $version = $row['v'];
        }
        if ($db_config['database']['driver'] === 'pdo_mysql') {
            $db_ver = 'MySQL:' . $version;
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
            'Content-Length: ' . strlen($data),
        );
        $context = stream_context_create(
            array(
                'http' => array(
                    'method' => 'POST',
                    'header' => $header,
                    'content' => $data,
                )
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
            'publicPath' => '..' . RELATIVE_PUBLIC_DIR_PATH . '/',
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
                'publicPath' => '..' . RELATIVE_PUBLIC_DIR_PATH . '/',
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
            'publicPath' => '..' . RELATIVE_PUBLIC_DIR_PATH . '/',
        ));
    }
}