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
use Eccube\InstallApplication;
use Symfony\Component\Filesystem\Filesystem;

class InstallController
{

    private $data;

    private $PDO;

    public function index(InstallApplication $app)
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
                        if(empty($data['db_port'])){
                            $data['db_port'] = '5432';
                        }
                        $data['db_driver'] = 'pdo_pgsql';
                        break;
                    case 'mysql':
                        if(empty($data['db_port'])){
                            $data['db_port'] = '3306';
                        }
                        $data['db_driver'] = 'pdo_mysql';
                        break;
                }
                $this->data = $data;
                $this->install();

    #            return $app->redirect($app['url_generator']->generate('install_complete'));
                 return 'Install completed!!<br />EC-CUBE 3.0.0 beta ';
            }
        }

        return $app['twig']->render('Install/index.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function complete(InstallApplication $app)
    {
        return 'Install completed!!<br />EC-CUBE 3.0.0 beta ';
    }

    private function install()
    {
        $this
            ->checkDirPermission()
            ->setPDO()
            ->createConfigPhpFile()
            ->createConfigYmlFile()
            ->createTable()
            ->insert()
        ;
    }

    // TODO:このへん サービスに分離する

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
        exec(' php ' . $doctrine . ' orm:schema-tool:create 2>&1', $output,$state);

        // NATの無通信タイマ対策（仮）
        echo str_repeat(" ",4*1024); 
        ob_flush();
        flush();

        if($state!=0) // スキーマ作成の失敗時
        {
            throw new \Exception( join("\n",$output) );
        }
        return $this;
    }

    private function insert()
    {
        $data = $this->data;

        if($data['db_type']=='pgsql'){
            $sqlFile = __DIR__ . '/../../../html/install/sql/insert_data_pgsql.sql';
        }elseif($data['db_type']=='mysql'){
            $sqlFile = __DIR__ . '/../../../html/install/sql/insert_data_mysql.sql';
        }else{
            $sqlFile = __DIR__ . '/../../../html/install/sql/insert_data.sql';
        }

        $fp = fopen($sqlFile, 'r');
        $sql = fread($fp, filesize($sqlFile));
        fclose($fp);
        $sqls = explode(';', $sql);

        $this->PDO->beginTransaction();
        foreach ($sqls as $sql) {
            $this->PDO->query(trim($sql));
        }

        $sth=$this->PDO->prepare( "INSERT INTO dtb_baseinfo (id, shop_name, email01, email02, email03, email04, top_tpl, product_tpl, detail_tpl, mypage_tpl, update_date, point_rate, welcome_point) VALUES (1, :shop_name, :admin_mail, :admin_mail, :admin_mail, :admin_mail, 'default1', 'default1', 'default1', 'default1', current_timestamp, 0, 0);");
        $sth->execute(array(':shop_name' => $data['shop_name'], ':admin_mail'=>$data['admin_mail']));

        $sth=$this->PDO->prepare("INSERT INTO dtb_member (member_id, login_id, password, salt, work, del_flg, authority, creator_id, rank, update_date, create_date) VALUES (2, 'admin', :admin_pass , :auth_magic , '1', '0', '0', '1', '1', current_timestamp, current_timestamp);");
        $sth->execute(array(':admin_pass' => $data['admin_pass'], ':auth_magic'=>$data['auth_magic']));

        $this->setSequenceVal();

        $this->PDO->commit();

        return $this;
    }

    private function setSequenceVal(){
        $seqs=array(
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
        foreach($seqs as $seq){
            if($data['db_type']=='pgsql'){
                $sql="SELECT SETVAL('$seq', 10000);";
                $this->PDO->query(trim($sql));
            }
        }
    }

    private function checkDirPermission()
    {
        
        $protectedDirs=array(); 
        $base = __DIR__ . '/../../..';
        $dirs=array('/html' ,'/app', '/app/template', '/app/cache', '/app/config', '/app/download', '/app/downloads', '/app/font', '/app/fonts','/app/log' ,'/app/upload', '/app/upload/csv');
        foreach($dirs as $dir) {
            if(!is_writable($base.$dir)) {
                $protectedDirs[]=$base.$dir;
            }
        }
        if(count($protectedDirs)>0) {
            throw new \Exception("directory not writable ".implode($protectedDirs,","));
        }
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
admin_dir: /admin
password_hash_algos: sha256
root: {$data['path']}
tpl: {$data['path']}user_data/packages/default/
admin_tpl: {$data['path']}user_data/packages/admin 
image_path: /upload/save_image/
release_year: 2015
shop_name: ec-cube shop 20150525
mail_cc:
    - admin@example.com
stext_len: 50
sample_address1: 市区町村名 (例：千代田区神田神保町)
sample_address2: 番地・ビル名 (例：1-3-5)
ECCUBE_VERSION: 3.0.0-beta
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
