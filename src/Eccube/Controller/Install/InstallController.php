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

    private $progress;

    private $error;

    const SESSION_KEY = 'eccube.session.install';

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

    // ようこそ
    public function step1(InstallApplication $app, Request $request)
    {
        $form = $app['form.factory']
            ->createBuilder('install_step1')
            ->getForm();

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

        if ($this->isValid($request, $form)) {
            $this->createConfigYamlFile($form->getData());
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

        if ($this->isValid($request, $form)) {
            if (!$form['no_update']->getData()) {
                try {
                    $this
                        ->setPDO()
                        ->createTable()
                        ->insert()
                        // TODO: migrationどうするか
//                        ->doMigrate()
                    ;
                } catch (\MigrationException $e) {
                }
            }

            return $app->redirect($app->url('install_complete'));
        }

        return $app['twig']->render('step5.twig', array(
            'form' => $form->createView(),
        ));
    }

    //    インストール完了
    public function complete(InstallApplication $app, Request $request)
    {
        return $app['twig']->render('complete.twig');
    }

    public function admin(InstallApplication $app, Request $request)
    {
        $config_file = __DIR__ . '/../../../../app/config/eccube/config.yml';
        $config = Yaml::parse($config_file);

        return $app->redirect($config['root'] . $config['admin_dir']);
    }

    private function setPDO()
    {
        $config_file = __DIR__ . '/../../../../app/config/eccube/database.yml';
        $data = Yaml::parse($config_file);

        $this->PDO = new \PDO(
            str_replace('pdo_', '', $data['driver'])
            . ':host=' . $data['host']
            . ';dbname=' . $data['dbname'],
            $data['user'],
            $data['password']
        );

        return $this;
    }

    private function resetNatTimer()
    {
        // NATの無通信タイマ対策（仮）
        echo str_repeat(" ", 4 * 1024);
        ob_flush();
        flush();
    }



    private function createTable()
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

        $config_file = __DIR__ . '/../../../../app/config/eccube/database.yml';
        $data = Yaml::parse($config_file);

        if ($data['driver'] == 'pdo_pgsql') {
            $sqlFile = __DIR__ . '/../../../../html/install/sql/insert_data_pgsql.sql';
        } elseif ($data['driver'] == 'pdo_mysql') {
            $sqlFile = __DIR__ . '/../../../../html/install/sql/insert_data_mysql.sql';
        } else {
            $sqlFile = __DIR__ . '/../../../../html/install/sql/insert_data.sql';
        }

        $fp = fopen($sqlFile, 'r');
        $sql = fread($fp, filesize($sqlFile));
        fclose($fp);
        $sqls = explode(';', $sql);

        $this->PDO->beginTransaction();
        foreach ($sqls as $sql) {
            $this->PDO->query(trim($sql));
        }

        $sth = $this->PDO->prepare("INSERT INTO dtb_baseinfo (id, shop_name, email01, email02, email03, email04, top_tpl, product_tpl, detail_tpl, mypage_tpl, update_date, point_rate, welcome_point) VALUES (1, :shop_name, :admin_mail, :admin_mail, :admin_mail, :admin_mail, 'default1', 'default1', 'default1', 'default1', current_timestamp, 0, 0);");
        $sth->execute(array(':shop_name' => $data['shop_name'], ':admin_mail' => $data['admin_mail']));

        $sth = $this->PDO->prepare("INSERT INTO dtb_member (member_id, login_id, password, salt, work, del_flg, authority, creator_id, rank, update_date, create_date) VALUES (2, 'admin', :admin_pass , :auth_magic , '1', '0', '0', '1', '1', current_timestamp, current_timestamp);");
        $sth->execute(array(':admin_pass' => $data['admin_pass'], ':auth_magic' => $data['auth_magic']));

        $this->setSequenceVal();

        $this->PDO->commit();

        return $this;
    }

    private function doMigrate()
    {
        $app = new \Eccube\Application();
        $config = new Configuration($app['db']);
        $config->setMigrationsNamespace('DoctrineMigrations');

        $migrationDir = __DIR__ . '/../Resource/doctrine/migration';
        $config->setMigrationsDirectory($migrationDir);
        $config->registerMigrationsFromDirectory($migrationDir);

        $migration = new Migration($config);
        // nullを渡すと最新バージョンまでマイグレートする
        $migration->migrate(null, false);
        return $this;
    }

    private function setSequenceVal()
    {
        $seqs = array(
            'dtb_best_products_best_id_seq',
            'dtb_category_category_id_seq',
            'dtb_class_name_class_name_id_seq',
            'dtb_class_category_class_category_id_seq',
            'dtb_csv_no_seq',
            'dtb_csv_sql_sql_id_seq',
            'dtb_customer_customer_id_seq',
            'dtb_deliv_deliv_id_seq',
            'dtb_holiday_holiday_id_seq',
            'dtb_kiyaku_kiyaku_id_seq',
            'dtb_mail_history_send_id_seq',
            'dtb_maker_maker_id_seq',
            'dtb_member_member_id_seq',
            'dtb_module_update_logs_log_id_seq',
            'dtb_news_news_id_seq',
            'dtb_order_order_id_seq',
            'dtb_order_detail_order_detail_id_seq',
            'dtb_other_deliv_other_deliv_id_seq',
            'dtb_payment_payment_id_seq',
            'dtb_products_class_product_class_id_seq',
            'dtb_products_product_id_seq',
            'dtb_review_review_id_seq',
            'dtb_send_history_send_id_seq',
            'dtb_mailmaga_template_template_id_seq',
            'dtb_plugin_plugin_id_seq',
            'dtb_plugin_hookpoint_plugin_hookpoint_id_seq',
            'dtb_api_config_api_config_id_seq',
            'dtb_api_account_api_account_id_seq',
            'dtb_tax_rule_tax_rule_id_seq',
        );
        $data = $this->data;
        foreach ($seqs as $seq) {
            if ($data['db_type'] == 'pgsql') {
                $sql = "SELECT SETVAL('$seq', 10000);";
                $this->PDO->query(trim($sql));
            }
        }
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


    private function createDatabaseYamlFile($data)
    {
        $fs = new Filesystem();
        $config_file = __DIR__ . '/../../../../app/config/eccube/database.yml';
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

        $content = array(
            'driver' => $data['db_driver'],
            'host' => $data['database_host'],
            'dbname' => $data['database_name'],
            'port' => $data['database_port'],
            'user' => $data['database_user'],
            'password' => $data['database_password'],
            'charset' => 'utf8',
        );
        file_put_contents($config_file, Yaml::dump($content));

        return $this;
    }
    private function createConfigYamlFile($data)
    {
        $fs = new Filesystem();
        $config_file = __DIR__ . '/../../../../app/config/eccube/config.yml';
        if ($fs->exists($config_file)) {
            $fs->remove($config_file);
        }

        $root = preg_replace('|^https?://[a-zA-Z0-9_:~=&\?\.\-]+|', '', $data['http_url']);
        $content = array(
            'mail' => array(
                'host' => $data['smtp_host'],
                'port' => $data['smtp_port'],
                'username' => $data['smtp_username'],
                'password' => $data['smtp_password'],
                'encryption' => '',
                'auth_mode' => '',
            ),
            'auth_magic' => 'droucliuijeanamiundpnoufrouphudrastiokec',
            'admin_dir' => '/' . $data['admin_dir'],
            'password_hash_alogs' => 'sha256',
            'root' => $root,
            'tpl' => $root . '/user_data/packages/default/',
            'admin_tpl' => $root . '/user_data/packages/admin/',
            'image_path' => $root . '/upload/save_image',
            'release_year' => date('Y'),
            'shop_name' => $data['shop_name'],
            'stext_len' => 50,
            'sample_address1' => '',
            'sample_address2' => '',
            'ECCUBE_VERSION' => '3.0.0-beta2',
            'customer_confirm_mail' => false,
        );
        file_put_contents($config_file, Yaml::dump($content));

        return $this;
    }
}
