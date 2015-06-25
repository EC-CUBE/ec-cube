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

use Eccube\InstallApplication;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Migrations\MigrationException;;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\DBAL\Migrations\Configuration\Configuration;


class InstallController
{
    private $data;

    private $PDO;

    private $error;

    private $config_path;

    private $dist_path;

    private $session_data;

    const SESSION_KEY = 'eccube.session.install';

    public function __construct()
    {
        $this->config_path = __DIR__ . '/../../../../app/config/eccube';
        $this->dist_path = __DIR__ . '/../../Resource/config';
    }

    private function isValid(Request $request, Form $form)
    {
        $session = $request->getSession();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $sessionData = $session->get(self::SESSION_KEY) ?: array();
                $formData = array_replace_recursive($sessionData, $form->getData());
                $session->set(self::SESSION_KEY , $formData);

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

        return $app['twig']->render('step1.twig', array(
            'form' => $form->createView(),
        ));
    }

    // 権限チェック
    public function step2(InstallApplication $app, Request $request)
    {
        $protectedDirs = $this->getProtectedDirs();

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
        $form = $app['form.factory']
            ->createBuilder('install_step5')
            ->getForm();
        $sessionData = $this->getSessionData($request);
        $form->setData($sessionData);

        if ($this->isValid($request, $form)) {
            if (!$form['no_update']->getData()) {
                $this
                    ->setPDO()
                    ->dropTables()
                    ->revertMigrate()
                    ->createTables()
                    ->insert()
                    ->doMigrate();
            }
            if (isset($sessionData['agree']) && $sessionData['agree'] == '1') {
                $this->sendAppData($sessionData);
            }
            $this->addInstallStatus();

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

        $adminUrl = ($config['root'] . $config['admin_dir']);

        return $app['twig']->render('complete.twig', array(
            'admin_url' => $adminUrl,
        ));
    }

    private function resetNatTimer()
    {
        // NATの無通信タイマ対策（仮）
        echo str_repeat(" ", 4 * 1024);
        ob_flush();
        flush();
    }

    private function setPDO()
    {
        $config_file = $this->config_path . '/database.yml';
        $config = Yaml::parse($config_file);
        $data = $config['database'];

        $dsn = str_replace('pdo_', '', $data['driver'])
            . ':host=' . $data['host']
            . ';dbname=' . $data['dbname'];
        if (!empty($data['port'])) {
            $dsn .= ';port=' . $data['port'];
        }

        $this->PDO = new \PDO(
            $dsn,
            $data['user'],
            $data['password']
        );

        return $this;
    }

    private function dropTables()
    {
        $this->resetNatTimer();

        $doctrine = __DIR__ . '/../../../../vendor/bin/doctrine';
        exec(' php ' . $doctrine . ' orm:schema-tool:drop --force 2>&1', $output, $state);

        if ($state != 0) { // スキーマ作成の失敗時
            throw new \Exception(join("\n", $output));
        }

        return $this;
    }
    private function createTables()
    {
        $this->resetNatTimer();

        $doctrine = __DIR__ . '/../../../../vendor/bin/doctrine';
        exec(' php ' . $doctrine . ' orm:schema-tool:create 2>&1', $output, $state);

        if ($state != 0) { // スキーマ作成の失敗時
            throw new \Exception(join("\n", $output));
        }

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

        if ($config['database']['driver'] == 'pdo_pgsql') {
            $sqlFile = __DIR__ . '/../../Resource/sql/insert_data_pgsql.sql';
        } elseif ($config['database']['driver'] == 'pdo_mysql') {
            $sqlFile = __DIR__ . '/../../Resource/sql/insert_data_mysql.sql';
        } else {
            $sqlFile = __DIR__ . '/../../Resource/sql/insert_data.sql';
        }

        $fp = fopen($sqlFile, 'r');
        $sql = fread($fp, filesize($sqlFile));
        fclose($fp);
        $sqls = explode(';', $sql);

        $this->PDO->beginTransaction();
        foreach ($sqls as $sql) {
            $this->PDO->query(trim($sql));
        }

        $config = array(
            'auth_type' => '',
            'auth_magic' => $config['config']['auth_magic'],
            'password_hash_algos' => 'sha256',
        );
        $passwordEncoder = new \Eccube\Security\Core\Encoder\PasswordEncoder($config);
        $salt = \Eccube\Util\Str::random();

        $encodedPassword = $passwordEncoder->encodePassword($this->session_data['login_pass'], $salt);
        $sth = $this->PDO->prepare("INSERT INTO dtb_base_info (id, shop_name, email01, email02, email03, email04, update_date, point_rate, welcome_point) VALUES (1, :shop_name, :admin_mail, :admin_mail, :admin_mail, :admin_mail, current_timestamp, 0, 0);");
        $sth->execute(array(':shop_name' => $this->session_data['shop_name'], ':admin_mail' => $this->session_data['email']));

        $sth = $this->PDO->prepare("INSERT INTO dtb_member (member_id, login_id, password, salt, work, del_flg, authority, creator_id, rank, update_date, create_date) VALUES (2, 'admin', :admin_pass , :salt , '1', '0', '0', '1', '1', current_timestamp, current_timestamp);");
        $sth->execute(array(':admin_pass' => $encodedPassword, ':salt' => $salt));

        $this->PDO->commit();

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

    private function revertMigrate()
    {
        try {
            $migration = $this->getMigration();
            $migration->migrate('first', false);
        } catch (MigrationException $e) {
        }

        return $this;
    }

    private function getProtectedDirs()
    {
        $protectedDirs = array();
        $base = __DIR__ . '/../../../..';
        $dirs = array(
            '/app/config/eccube',
            '/html',
            '/app',
            '/app/template',
            '/app/cache',
            '/app/config',
            '/app/download',
            '/app/downloads',
            '/app/font',
            '/app/fonts',
            '/app/log',
            '/app/upload',
            '/app/upload/csv',
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

        $auth_magic = \Eccube\Util\Str::random();
        $target = array('${HTTP_URL}', '${HTTPS_URL}', '${AUTH_MAGIC}', '${SHOP_NAME}', '${ECCUBE_INSTALL}');
        $replace = array($data['http_url'], $data['https_url'], $auth_magic, $data['shop_name'], '0');

        $fs = new Filesystem();
        $content = str_replace(
            $target,
            $replace,
            file_get_contents($this->dist_path . '/config.yml.dist')
        );
        $fs->dumpFile($config_file, $content);

        return $this;
    }

    private function addInstallStatus(){
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
            case 'pgsql':
                if (empty($data['db_port'])) {
                    $data['db_port'] = '5432';
                }
                $data['db_driver'] = 'pdo_pgsql';
                break;
            case 'mysql':
                if (empty($data['db_port'])) {
                    $data['db_port'] = '3306';
                }
                $data['db_driver'] = 'pdo_mysql';
                break;
        }
        $target = array('${DBDRIVER}', '${DBSERVER}', '${DBNAME}', '${DBPORT}', '${DBUSER}', '${DBPASS}');
        $replace = array($data['db_driver'], $data['database_host'], $data['database_name'], $data['database_port'], $data['database_user'], $data['database_password']);

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
        $config_file = $this->config_path .'/mail.yml';
        if ($fs->exists($config_file)) {
            $fs->remove($config_file);
        }
        $target = array('${MAIL_BACKEND}', '${MAIL_HOST}', '${MAIL_PORT}', '${MAIL_USER}', '${MAIL_PASS}');
        $replace = array($data['mail_backend'], $data['smtp_host'], $data['smtp_port'], $data['smtp_username'], $data['smtp_password']);

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
        $config_file = $this->config_path .'/path.yml';
        if ($fs->exists($config_file)) {
            $fs->remove($config_file);
        }

        $ADMIN_ROUTE = $data['admin_dir'];
        $TEMPLATE_CODE = 'default';
        $USER_DATA_ROUTE = 'user_data';
        $ROOT_DIR = realpath(__DIR__ . '/../../../../');
        $ROOT_URLPATH = str_replace(
            array($request->server->get('DOCUMENT_ROOT'), '/install.php'),
            array('', ''),
            $request->server->get('SCRIPT_FILENAME')
        );

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

    private function sendAppData($sessionData)
    {
        $config_file = $this->config_path . '/config.yml';
        $config = Yaml::parse($config_file);

        $config_file = $this->config_path . '/database.yml';
        $db_config = Yaml::parse($config_file);

        $this->setPDO();
        $stmt = $this->PDO->query('SELECT version()');

        $version = '';
        foreach ($stmt as $row) {
            $version = $row['version'];
        }

        if ($db_config['database']['driver'] === 'pdo_mysql') {
            $db_ver = 'MySQL:' . $version;
        } else {
            $db_ver = $version;
        }

        $data = http_build_query(
            array(
                'site_url' => $sessionData['http_url'],
                'shop_name' => $sessionData['shop_name'],
                'cube_ver' => $config['ECCUBE_VERSION'],
                'php_ver' => phpversion(),
                'db_ver' => $db_ver,
                'os_type' => php_uname(),
            )
        );

        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Length: ".strlen($data),
        );
        $context = stream_context_create(
            array(
                'http' => array(
                    'method'=> 'POST',
                    'header'=> $header,
                    'content' => $data,
                )
            )
        );
        file_get_contents('http://www.ec-cube.net/mall/use_site.php', false, $context);

        return $this;
    }
}
