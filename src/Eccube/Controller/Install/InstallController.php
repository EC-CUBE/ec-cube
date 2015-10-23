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
use Eccube\InstallApplication;
use Eccube\Util\Str;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

class InstallController
{
    private $app;

    private $PDO;

    private $config_path;

    private $dist_path;

    private $cache_path;

    private $session_data;

    private $required_modules = array('pdo', 'phar', 'mbstring', 'zlib', 'ctype', 'session', 'JSON', 'xml', 'libxml', 'OpenSSL', 'zip', 'cURL', 'fileinfo');

    private $recommended_module = array('hash', 'APC', 'mcrypt');

    const SESSION_KEY = 'eccube.session.install';

    public function __construct()
    {
        $this->config_path = __DIR__ . '/../../../../app/config/eccube';
        $this->dist_path = __DIR__ . '/../../Resource/config';
        $this->cache_path = __DIR__ . '/../../../../app/cache';

        // timezone
        $config = Yaml::parse($this->dist_path . '/config.yml.dist');
        if (!empty($config['timezone'])) {
            date_default_timezone_set($config['timezone']);
        }
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

        return $app->redirect($app->url('install_step1'));
    }

    // ようこそ
    public function step1(InstallApplication $app, Request $request)
    {
        $form = $app['form.factory']
            ->createBuilder('install_step1')
            ->getForm();
        $sessionData = $this->getSessionData($request);
        $form->setData($sessionData);

        if ($this->isValid($request, $form)) {
            return $app->redirect($app->url('install_step2'));
        }

        $this->checkModules($app);

        return $app['twig']->render('step1.twig', array(
            'form' => $form->createView(),
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
                ->directories()
                ->depth(0);
            $fs = new Filesystem();
            $fs->remove($finder);
        }

        return $app['twig']->render('step2.twig', array(
            'protectedDirs' => $protectedDirs,
        ));
    }

    //    サイトの設定
    public function step3(InstallApplication $app, Request $request)
    {
        $form = $app['form.factory']
            ->createBuilder('install_step3')
            ->getForm();
        $sessionData = $this->getSessionData($request);
        $form->setData($sessionData);
        if ($this->isValid($request, $form)) {
            $data = $form->getData();
            $this
                ->createConfigYamlFile($data)
                ->createMailYamlFile($data)
                ->createPathYamlFile($data, $request);

            return $app->redirect($app->url('install_step4'));
        }

        return $app['twig']->render('step3.twig', array(
            'form' => $form->createView(),
        ));
    }

    //    データベースの設定
    public function step4(InstallApplication $app, Request $request)
    {
        $form = $app['form.factory']
            ->createBuilder('install_step4')
            ->getForm();

        $sessionData = $this->getSessionData($request);
        $form->setData($sessionData);

        if ($this->isValid($request, $form)) {
            $this->createDatabaseYamlFile($form->getData());

            return $app->redirect($app->url('install_step5'));
        }

        return $app['twig']->render('step4.twig', array(
            'form' => $form->createView(),
        ));
    }

    //    データベースの初期化
    public function step5(InstallApplication $app, Request $request)
    {
        set_time_limit(0);
        $this->app = $app;
        $form = $app['form.factory']
            ->createBuilder('install_step5')
            ->getForm();
        $sessionData = $this->getSessionData($request);
        $form->setData($sessionData);

        if ($this->isValid($request, $form)) {
            if (!$form['no_update']->getData()) {
                set_time_limit(0);
                
                $this
                    ->setPDO()
                    ->dropTables()
                    ->createTables()
                    ->doMigrate()
                    ->insert();
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

            return $app->redirect($app->url('install_complete'));
        }

        return $app['twig']->render('step5.twig', array(
            'form' => $form->createView(),
        ));
    }

    //    インストール完了
    public function complete(InstallApplication $app, Request $request)
    {
        $config_file = $this->config_path . '/path.yml';
        $config = Yaml::parse($config_file);

        $host = $request->getSchemeAndHttpHost();
        $basePath = $request->getBasePath();

        $adminUrl = $host . $basePath . '/' . $config['admin_dir'];

        return $app['twig']->render('complete.twig', array(
            'admin_url' => $adminUrl,
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
                $app->addWarning('[推奨] ' . $module . ' 拡張モジュールが有効になっていません。', 'install');
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
        $config_file = $this->config_path . '/database.yml';
        $config = Yaml::parse($config_file);

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
        $config_file = $this->config_path . '/database.yml';
        $database = Yaml::parse($config_file);

        $this->app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => $database['database']
        ));

        $this->app->register(new \Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), array(
            'orm.proxies_dir' => __DIR__ . '/../../app/cache/doctrine',
            'orm.em.options' => array(
                'mappings' => array(
                    array(
                        'type' => 'yml',
                        'namespace' => 'Eccube\Entity',
                        'path' => array(
                            __DIR__ . '/../../Resource/doctrine',
                            __DIR__ . '/../../Resource/doctrine/master',
                        ),
                    ),

                ),
            )
        ));

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

    private function insert()
    {
        $this->resetNatTimer();

        $config_file = $this->config_path . '/database.yml';
        $database = Yaml::parse($config_file);
        $config['database'] = $database['database'];

        $config_file = $this->config_path . '/config.yml';
        $baseConfig = Yaml::parse($config_file);
        $config['config'] = $baseConfig;

        $this->PDO->beginTransaction();

        try {

            $config = array(
                'auth_type' => '',
                'auth_magic' => $config['config']['auth_magic'],
                'password_hash_algos' => 'sha256',
            );
            $passwordEncoder = new \Eccube\Security\Core\Encoder\PasswordEncoder($config);
            $salt = \Eccube\Util\Str::random();

            $encodedPassword = $passwordEncoder->encodePassword($this->session_data['login_pass'], $salt);
            $sth = $this->PDO->prepare('INSERT INTO dtb_base_info (
                id,
                shop_name,
                email01,
                email02,
                email03,
                email04,
                update_date,
                option_product_tax_rule
            ) VALUES (
                1,
                :shop_name,
                :admin_mail,
                :admin_mail,
                :admin_mail,
                :admin_mail,
                current_timestamp,
                0);');
            $sth->execute(array(
                ':shop_name' => $this->session_data['shop_name'],
                ':admin_mail' => $this->session_data['email']
            ));

            $sth = $this->PDO->prepare("INSERT INTO dtb_member (member_id, login_id, password, salt, work, del_flg, authority, creator_id, rank, update_date, create_date,name,department) VALUES (2, :login_id, :admin_pass , :salt , '1', '0', '0', '1', '1', current_timestamp, current_timestamp,'管理者','EC-CUBE SHOP');");
            $sth->execute(array(':login_id' => $this->session_data['login_id'], ':admin_pass' => $encodedPassword, ':salt' => $salt));

            $this->PDO->commit();
        } catch (\Exception $e) {
            $this->PDO->rollback();
            throw $e;
        }

        return $this;
    }

    private function getMigration()
    {
        $app = new \Eccube\Application();
        $app->initDoctrine();
        $config = new Configuration($app['db']);
        $config->setMigrationsNamespace('DoctrineMigrations');

        $migrationDir = __DIR__ . '/../../Resource/doctrine/migration';
        $config->setMigrationsDirectory($migrationDir);
        $config->registerMigrationsFromDirectory($migrationDir);

        $migration = new Migration($config);

        return $migration;
    }

    private function doMigrate()
    {
        try {
            $migration = $this->getMigration();
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

    private function createConfigYamlFile($data)
    {
        $fs = new Filesystem();
        $config_file = $this->config_path . '/config.yml';
        if ($fs->exists($config_file)) {
            $fs->remove($config_file);
        }

        $auth_magic = Str::random(32);
        $allowHost = Str::convertLineFeed($data['admin_allow_hosts']);
        if (empty($allowHost)) {
            $adminAllowHosts = array();
        } else {
            $adminAllowHosts = explode("\n", $allowHost);
        }

        $target = array('${AUTH_MAGIC}', '${SHOP_NAME}', '${ECCUBE_INSTALL}', '${FORCE_SSL}');
        $replace = array($auth_magic, $data['shop_name'], '0', $data['admin_force_ssl']);

        $fs = new Filesystem();
        $content = str_replace(
            $target,
            $replace,
            file_get_contents($this->dist_path . '/config.yml.dist')
        );
        $fs->dumpFile($config_file, $content);

        $config = Yaml::Parse($config_file);
        $config['admin_allow_host'] = $adminAllowHosts;
        $yml = Yaml::dump($config);
        file_put_contents($config_file, $yml);

        return $this;
    }

    private function addInstallStatus()
    {
        $config_file = $this->config_path . '/config.yml';
        $config = Yaml::parse($config_file);
        $config['eccube_install'] = 1;
        $yml = Yaml::dump($config);
        file_put_contents($config_file, $yml);

        return $this;
    }

    private function createDatabaseYamlFile($data)
    {
        $fs = new Filesystem();
        $config_file = $this->config_path . '/database.yml';
        if ($fs->exists($config_file)) {
            $fs->remove($config_file);
        }

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
        $target = array('${DBDRIVER}', '${DBSERVER}', '${DBNAME}', '${DBPORT}', '${DBUSER}', '${DBPASS}');
        $replace = array(
            $data['db_driver'],
            $data['database_host'],
            $data['database_name'],
            $data['database_port'],
            $data['database_user'],
            $data['database_password']
        );

        $fs = new Filesystem();
        $content = str_replace(
            $target,
            $replace,
            file_get_contents($this->dist_path . '/database.yml.dist')
        );

        $fs->dumpFile($config_file, $content);

        return $this;
    }

    private function createMailYamlFile($data)
    {
        $fs = new Filesystem();
        $config_file = $this->config_path . '/mail.yml';
        if ($fs->exists($config_file)) {
            $fs->remove($config_file);
        }
        $target = array('${MAIL_BACKEND}', '${MAIL_HOST}', '${MAIL_PORT}', '${MAIL_USER}', '${MAIL_PASS}');
        $replace = array(
            $data['mail_backend'],
            $data['smtp_host'],
            $data['smtp_port'],
            $data['smtp_username'],
            $data['smtp_password']
        );

        $fs = new Filesystem();
        $content = str_replace(
            $target,
            $replace,
            file_get_contents($this->dist_path . '/mail.yml.dist')
        );
        $fs->dumpFile($config_file, $content);

        return $this;
    }

    private function createPathYamlFile($data, Request $request)
    {
        $fs = new Filesystem();
        $config_file = $this->config_path . '/path.yml';
        if ($fs->exists($config_file)) {
            $fs->remove($config_file);
        }

        $ADMIN_ROUTE = $data['admin_dir'];
        $TEMPLATE_CODE = 'default';
        $USER_DATA_ROUTE = 'user_data';
        $ROOT_DIR = realpath(__DIR__ . '/../../../../');
        $ROOT_URLPATH = $request->getBasePath();

        $target = array('${ADMIN_ROUTE}', '${TEMPLATE_CODE}', '${USER_DATA_ROUTE}', '${ROOT_DIR}', '${ROOT_URLPATH}');
        $replace = array($ADMIN_ROUTE, $TEMPLATE_CODE, $USER_DATA_ROUTE, $ROOT_DIR, $ROOT_URLPATH);

        $fs = new Filesystem();
        $content = str_replace(
            $target,
            $replace,
            file_get_contents($this->dist_path . '/path.yml.dist')
        );
        $fs->dumpFile($config_file, $content);

        return $this;
    }

    private function sendAppData($params)
    {
        $config_file = $this->config_path . '/database.yml';
        $db_config = Yaml::parse($config_file);

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


    public function migration(InstallApplication $app, Request $request)
    {
        return $app['twig']->render('migration.twig');
    }

    public function migration_end(InstallApplication $app, Request $request)
    {
        $this->doMigrate();

        $config_app = new \Eccube\Application(); // install用のappだとconfigが取れないので
        $config_app->initialize();
        $config_app->boot();
        \Eccube\Util\Cache::clear($config_app, true);

        return $app['twig']->render('migration_end.twig');
    }
}
