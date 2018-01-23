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
        $environment
    ) {
        $this->rootDir = realpath(__DIR__.'/../../../..');
        $this->configDir = realpath($this->rootDir.'/app/config/eccube');
        $this->configDistDir = realpath($this->rootDir.'/src/Eccube/Resource/config');
        $this->cacheDir = realpath($this->rootDir.'/app/cache');
        $this->session = $session;
        $this->formFactory = $formFactory;
        $this->encoder = $encoder;
        $this->environment = $environment;
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
        $authmagic = env('ECCUBE_AUTH_MAGIC', 'secret');
        if ($authmagic == 'secret') {
            $authmagic =  StringUtil::random(32);
        }
        $this->setSessionData($this->session, ['ECCUBE_AUTH_MAGIC' => $authmagic]);

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

        if (empty($sessionData['shop_name'])) {
            // 再インストールの場合は設定ファイルから復旧
            if (file_exists($this->configDir.'/config.php')) {
                // ショップ名/メールアドレス
                $config = require $this->configDir.'/database.php';
                $conn = $this->createConnection($config['database'][$config['database']['default']]);
                $stmt = $conn->query("SELECT shop_name, email01 FROM dtb_base_info WHERE id = 1;");
                $row = $stmt->fetch();
                $sessionData['shop_name'] = $row['shop_name'];
                $sessionData['email'] = $row['email01'];

                // 管理画面ルーティング
                $config = require $this->configDir.'/path.php';
                $sessionData['admin_dir'] = $config['admin_route'];

                // 管理画面許可IP
                $config = require $this->configDir.'/config.php';
                if (!empty($config['admin_allow_hosts'])) {
                    $sessionData['admin_allow_hosts']
                        = StringUtil::convertLineFeed(implode("\n", $config['admin_allow_hosts']));
                }
                // 強制SSL
                $sessionData['admin_force_ssl'] = $config['force_ssl'];

                // ロードバランサ, プロキシサーバ
                if (!empty($config['trusted_proxies_connection_only'])) {
                    $sessionData['trusted_proxies_connection_only'] = (bool)$config['trusted_proxies_connection_only'];
                }
                if (!empty($config['trusted_proxies'])) {
                    $sessionData['trusted_proxies'] = StringUtil::convertLineFeed(implode("\n",
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
            } else {
                // 初期値にmailを設定.
                $sessionData['mail_backend'] = 'mail';
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
            // 再インストールの場合は設定ファイルから復旧.
            $file = $this->configDir.'/database.php';
            if (file_exists($file)) {
                // データベース設定
                $config = require $file;
                $database = $config['database'][$config['database']['default']];
                $sessionData['database'] = $database['driver'];
                if ($database['driver'] === 'pdo_sqlite') {
                    $sessionData['database_name'] = $this->configDir.'/eccube.db';
                } else {
                    $sessionData['database_host'] = $database['host'];
                    $sessionData['database_port'] = $database['port'];
                    $sessionData['database_name'] = $database['dbname'];
                    $sessionData['database_user'] = $database['user'];
                    $sessionData['database_password'] = $database['password'];
                }
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
                $data['database_name'] = $this->configDir.'/eccube.db';
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

            $this->copyConfigFiles();
            $data = array_merge(['root_urlpath' => $request->getBasePath()], $sessionData);
            $this->replaceConfigFiles($data, !$noUpdate);

            $params = require $this->configDir.'/database.php';
            $conn = $this->createConnection($params['database'][$params['database']['default']]);
            $em = $this->createEntityManager($conn);
            $migration = $this->createMigration($conn);

            $config = require $this->configDir.'/config.php';
            if ($noUpdate) {
                $this->update($conn, [
                    'auth_magic' => $config['auth_magic'],
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
                // $this->sendAppData($params);
            }

            // $this->removeSessionData($session);

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

        return [
            'admin_url' => $adminUrl,
        ];
    }

    private function getSessionData(SessionInterface $session)
    {
        return $session->get('eccube.session.install', []);
    }

    private function removeSessionData(SessionInterface $session)
    {
        $session->remove('eccube.session.install');
    }

    private function setSessionData(SessionInterface $session, $data = [])
    {
        $data = array_replace_recursive($this->getSessionData($session), $data);
        $session->set('eccube.session.install', $data);
    }

    private function checkModules()
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

    private function createConnection(array $params)
    {
        $params['driverClass'] = 'Doctrine\DBAL\Driver\PDOSqlite\Driver';
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

    private function migrate(Migration $migration)
    {
        try {
            // nullを渡すと最新バージョンまでマイグレートする
            $migration->migrate(null, false);
        } catch (MigrationException $e) {

        }
    }

    private function copyConfigFiles()
    {
        $from = $this->configDistDir;
        $to = $this->configDir;
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->mirror($from, $to, null, ['override' => true]);
    }

    private function replaceConfigFiles($data, $updateAuthMagic = true)
    {
        $values['ECCUBE_INSTALL'] = 1;
        $values['ECCUBE_ROOT_URLPATH'] = $data['root_urlpath'];

        if ($updateAuthMagic) {
            $values['ECCUBE_AUTH_MAGIC'] = StringUtil::random(32);
        } else {
            if (empty($values['ECCUBE_AUTH_MAGIC'])) {
                $values['ECCUBE_AUTH_MAGIC'] = StringUtil::random(32);
            }
        }
        if (isset($data['force_ssl'])) {
            $values['ECCUBE_FORCE_SSL'] = $data['force_ssl'];
        }
        if (isset($data['admin_dir'])) {
            $values['ECCUBE_ADMIN_ROUTE'] = $data['admin_dir'];
        }
        if (isset($data['database'])) {
            $values['ECCUBE_DB_DEFAULT'] = str_replace('pdo_', '', $data['database']);
        }
        if (isset($data['database_host'])) {
            $values['ECCUBE_DB_HOST'] = $data['database_host'];
        }
        if (isset($data['database_port'])) {
            $values['ECCUBE_DB_PORT'] = $data['database_port'];
        }
        if (isset($data['database_name'])) {
            $values['ECCUBE_DB_DATABASE'] = $data['database_name'];
        }
        if (isset($data['database_user'])) {
            $values['ECCUBE_DB_USERNAME'] = $data['database_user'];
        }
        if (isset($data['database_password'])) {
            $values['ECCUBE_DB_PASSWORD'] = $data['database_password'];
        }
        if (isset($data['mail_backend'])) {
            $values['ECCUBE_MAIL_TRANSPORT'] = $data['mail_backend'];
        }
        if (isset($data['smtp_host'])) {
            $values['ECCUBE_MAIL_HOST'] = $data['smtp_host'];
        }
        if (isset($data['smtp_port'])) {
            $values['ECCUBE_MAIL_PORT'] = $data['smtp_port'];
        }
        if (isset($data['smtp_username'])) {
            $values['ECCUBE_MAIL_USERNAME'] = $data['smtp_username'];
        }
        if (isset($data['smtp_password'])) {
            $values['ECCUBE_MAIL_PASSWORD'] = $data['smtp_password'];
        }
        if (isset($data['admin_allow_hosts'])) {
            $values['ECCUBE_ADMIN_ALLOW_HOSTS'] = $data['admin_allow_hosts'];
        }
        if (isset($data['admin_allow_hosts'])) {
            $hosts = StringUtil::convertLineFeed($data['admin_allow_hosts']);
            if ($hosts) {
                $values['ECCUBE_ADMIN_ALLOW_HOSTS'] = explode("\n", $hosts);
            }
        }
        if (isset($data['trusted_proxies'])) {
            $proxies = StringUtil::convertLineFeed($data['trusted_proxies']);
            if ($proxies) {
                $proxies = explode("\n", $proxies);
                // ループバックアドレスを含める
                $values['ECCUBE_TRUSTED_PROXIES'] = array_merge($proxies, ['127.0.0.1/8', '::1']);
            }
        }
        if (isset($data['ECCUBE_trusted_proxies_connection_only']) && $data['trusted_proxies_connection_only']) {
            // ループバックアドレスを含める
            $values['ECCUBE_TRUSTED_PROXIES'] = array_merge($proxies, ['127.0.0.1/8', '::1']);
        }

        foreach ($values as &$value) {
            if (is_bool($value)
                || is_null($value)
                || is_array($value)
                || is_numeric($value)
            ) {
                $value = var_export($value, true);
            } else {
                $value = "'".$value."'";
            }
        }

        $dir = $this->configDir;
        $files = [
            $dir.'/config.php',
            $dir.'/database.php',
            $dir.'/mail.php',
            $dir.'/path.php',
        ];

        $patternFormat = "/(env\('%s'.*?\),)/s";
        $replacementFormat = "env('%s', %s),";

        foreach ($files as $file) {
            $content = file_get_contents($file);
            foreach ($values as $k => $v) {
                $pattern = sprintf($patternFormat, $k);
                $replace = sprintf($replacementFormat, $k, $v);
                $content = preg_replace($pattern, $replace, $content);
                if (is_null($content)) {
                    throw new \Exception();
                }
            }
            file_put_contents($file, $content);
        }
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
    public function migration(Request $request)
    {
        return $app['twig']->render('migration.twig', array(
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
    public function migration_plugin(Request $request)
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
    public function migration_end(Request $request)
    {
        $this->doMigrate();
        $config_app = new \Eccube\Application(); // install用のappだとconfigが取れないので
        $config_app->initialize();
        $config_app->boot();
        \Eccube\Util\CacheUtil::clear($config_app, true);

        return $app['twig']->render('migration_end.twig', array(
        ));
    }
}
