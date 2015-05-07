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


namespace Eccube\Controller;

use Eccube\Application;
use Symfony\Component\Filesystem\Filesystem;

class InstallController
{

    private $data;

    private $PDO;

    public function index(Application $app)
    {
        $form = $app['form.factory']
            ->createBuilder('install')
            ->getForm();

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                switch ($data['db_type']) {
                    case 'pgsql':
                        $data['db_port'] = '5432';
                        $data['db_driver'] = 'pdo_pgsql';
                        break;
                    case 'mysql':
                        $data['db_port'] = '3306';
                        $data['db_driver'] = 'pdo_mysql';
                        $data['db_server'] = '127.0.0.1';
                        break;
                }
                $this->data = $data;
                $this->install();

                return $app->redirect($app['url_generator']->generate('install_complete'));
            }
        }

        return $app['twig']->render('Install/index.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function complete(Application $app)
    {
        return 'Install completed!!<br />EC-CUBE 3.0.0 beta ';
    }

    private function install()
    {
        $this
            ->createConfigPhpFile()
            ->createConfigYmlFile()
            ->modifyFilePermissions()
            ->createTable()
            ->setPDO()
            ->insert()
        ;
    }

    private function setPDO()
    {
        $data = $this->data;
        $this->PDO = new \PDO(
            $data['db_type']
            . ':host=' . $data['db_server']
            . ';dbname=' . $data['db_name'],
            $this->data['db_user'],
            $this->data['db_pass']
        );

        return $this;
    }

    private function createTable()
    {
        $doctrine = __DIR__ . '/../../../vendor/bin/doctrine';
        exec('php ' . $doctrine . ' orm:schema-tool:create', $output);

        return $this;
    }

    private function insert()
    {
        $sqlFile = __DIR__ . '/../../../html/install/sql/insert_data.sql';
        $fp = fopen($sqlFile, 'r');
        $sql = fread($fp, filesize($sqlFile));
        fclose($fp);

        $sqls = explode(';', $sql);
        foreach ($sqls as $sql) {
            $this->PDO->query(trim($sql));
        }

        return $this;
    }

    private function modifyFilePermissions()
    {
        $fs = new Filesystem();
        $base = __DIR__ . '/../../..';
        $fs->chmod($base . '/html', 0777, 0000, true);
        $fs->chmod($base . '/app', 0777);
        $fs->chmod($base . '/app/template', 0777, 0000, true);
        $fs->chmod($base . '/app/cache', 0777, 0000, true);
        $fs->chmod($base . '/app/config', 0777);
        $fs->chmod($base . '/app/download', 0777, 0000, true);
        $fs->chmod($base . '/app/downloads', 0777, 0000, true);
        $fs->chmod($base . '/app/font', 0777);
        $fs->chmod($base . '/app/log', 0777);
        $fs->chmod($base . '/src/Eccube/page', 0777, 0000, true);
        $fs->chmod($base . '/src/smarty_extends', 0777);
        $fs->chmod($base . '/app/upload', 0777);
        $fs->chmod($base . '/app/upload/csv', 0777);

        return $this;
    }

    private function createConfigPhpFile()
    {
        $data = $this->data;
        $url = $data['http_url'] . $data['path'];
        $content = <<<EOF
<?php
define('ECCUBE_INSTALL', 'ON');
define('HTTP_URL', '{$url}');
define('HTTPS_URL', '{$url}');
define('ROOT_URLPATH', '{$data['path']}');
define('DOMAIN_NAME', '');
define('DB_TYPE', '{$data['db_type']}');
define('DB_USER', '{$data['db_user']}');
define('DB_PASSWORD', '{$data['db_pass']}');
define('DB_SERVER', '{$data['db_server']}');
define('DB_NAME', '{$data['db_name']}');
define('DB_PORT', '{$data['db_port']}');
define('ADMIN_DIR', 'admin/');
define('ADMIN_FORCE_SSL', FALSE);
define('ADMIN_ALLOW_HOSTS', 'a:0:{}');
define('AUTH_MAGIC', 'eccube300beta');
define('PASSWORD_HASH_ALGOS', 'sha256');
define('MAIL_BACKEND', 'mail');
define('SMTP_HOST', '');
define('SMTP_PORT', '');
define('SMTP_USER', '');
define('SMTP_PASSWORD', '');
EOF;

        $fs = new Filesystem();
        $filePath = __DIR__ . '/../../../app/config/eccube/config.php';

        $fs = new Filesystem();
        if ($fs->exists($filePath)) {
            $fs->remove($filePath);
        }
        $fs->dumpFile($filePath, $content);

        return $this;
    }

    private function createConfigYmlFile()
    {
        $data = $this->data;

        $content = <<<EOF
database:
    driver: pdo_{$data['db_type']}
    host: {$data['db_server']}
    dbname: {$data['db_name']}
    port: {$data['db_port']}
    user: {$data['db_user']}
    password : {$data['db_pass']}
    charset: utf8
mail:
    host: localhost
    port: 25
    username:
    password:
    encryption:
    auth_mode:
auth_type: HMAC
auth_magic: eccube300beta
password_hash_algos: sha256
use_point: true
option_favorite_product: true
mypage_order_status_disp_flag: true
root: {$data['path']}
tpl: {$data['path']}user_data/packages/default/
admin_tpl: {$data['path']}user_data/packages/admin/
image_path: /upload/save_image/
shop_name: EC-CUBE_3.0.0-beta
release_year: 2015
mail_cc:
    - admin@example.com
stext_len: 50
sample_address1: 市区町村名 (例：千代田区神田神保町)
sample_address2: 番地・ビル名 (例：1-3-5)
ECCUBE_VERSION: 3.0.0-dev
customer_confirm_mail: false
EOF;

        $filePath = __DIR__ . '/../../../app/config/eccube/config.yml';

        $fs = new Filesystem();
        if ($fs->exists($filePath)) {
            $fs->remove($filePath);
        }
        $fs->dumpFile($filePath, $content);

        return $this;
    }
}
