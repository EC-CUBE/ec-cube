<?php

namespace Eccube\Tests;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Migration;
use Eccube\Application;
use Eccube\Entity\Customer;
use Eccube\Tests\Mock\CsrfTokenMock;
use Faker\Factory as Faker;
use Guzzle\Http\Client;
use Silex\WebTestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Abstract class that other unit tests can extend, provides generic methods for EC-CUBE tests.
 *
 * @author Kentaro Ohkouchi
 */
abstract class EccubeTestCase extends WebTestCase
{
    /** MailCatcher の URL. */
    const MAILCATCHER_URL = 'http://127.0.0.1:1080/';

    protected $actual;
    protected $expected;

    /**
     * Applicaiton を生成しトランザクションを開始する.
     */
    public function setUp()
    {
        parent::setUp();
        $this->app->setTestMode(true);
        if ($this->isSqliteInMemory()) {
            $this->initializeDatabase();
        }
        if (isset($this->app['orm.em'])) {
            $this->app['orm.em']->getConnection()->beginTransaction();
        }
    }

    /**
     * トランザクションをロールバックする.
     */
    public function tearDown()
    {
        parent::tearDown();
        if (!$this->isSqliteInMemory()) {
            $this->app['orm.em']->getConnection()->rollback();
            $this->app['orm.em']->getConnection()->close();
        }

        $this->cleanUpProperties();
        $this->app = null;
    }

    /**
     * データベースを初期化する.
     *
     * データベースを初期化し、マイグレーションを行なう.
     * 全てのデータが初期化されるため注意すること.
     *
     * @link http://jamesmcfadden.co.uk/database-unit-testing-with-doctrine-2-and-phpunit/
     */
    public function initializeDatabase()
    {
        // Get an instance of your entity manager
        $entityManager = $this->app['orm.em'];

        // Retrieve PDO instance
        $pdo = $entityManager->getConnection()->getWrappedConnection();

        // Clear Doctrine to be safe
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
        $entityManager->clear();
        gc_collect_cycles();

        // Schema Tool to process our entities
        $tool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $classes = $entityManager->getMetaDataFactory()->getAllMetaData();

        // Drop all classes and re-build them for each test case
        $tool->dropSchema($classes);
        $tool->createSchema($classes);
        $config = new Configuration($this->app['db']);
        $config->setMigrationsNamespace('DoctrineMigrations');

        $migrationDir = __DIR__.'/../../../src/Eccube/Resource/doctrine/migration';
        $config->setMigrationsDirectory($migrationDir);
        $config->registerMigrationsFromDirectory($migrationDir);

        $migration = new Migration($config);
        // initialize migrations.sql from bootstrap
        if (!file_exists(sys_get_temp_dir().'/migrations.sql')) {
            $sql = $migration->migrate(null, false);
            file_put_contents(sys_get_temp_dir().'/migrations.sql', json_encode($sql));
        } else {
            $migrations = json_decode(file_get_contents(sys_get_temp_dir().'/migrations.sql'), true);
            foreach ($migrations as $migration_sql) {
                foreach ($migration_sql as $sql) {
                    if ($this->isSqliteInMemory()) {
                        // XXX #1199 の問題を無理矢理回避...
                        $sql = preg_replace('/CURRENT_TIMESTAMP/i', "datetime('now','-9 hours')", $sql);
                    }
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $stmt->closeCursor();
                }
            }
        }

        // 通常は eccube_install.sh で追加されるデータを追加する
        $sql = "INSERT INTO dtb_member (member_id, login_id, password, salt, work, del_flg, authority, creator_id, rank, update_date, create_date,name,department) VALUES (2, 'admin', 'test', 'test', 1, 0, 0, 1, 1, current_timestamp, current_timestamp,'管理者','EC-CUBE SHOP')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $stmt->closeCursor();

        $sql = "INSERT INTO dtb_base_info (id, shop_name, email01, email02, email03, email04, update_date, option_product_tax_rule) VALUES (1, 'SHOP_NAME', 'admin@example.com', 'admin@example.com', 'admin@example.com', 'admin@example.com', current_timestamp, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $stmt->closeCursor();
    }

    /**
     * Faker を生成する.
     *
     * @param string $locale ロケールを指定する. デフォルト ja_JP
     * @return Faker\Generator
     * @link https://github.com/fzaninotto/Faker
     */
    public function getFaker($locale = 'ja_JP')
    {
        return Faker::create($locale);
    }

    /**
     * Expected と Actual を比較する.
     *
     * @param string $message エラーメッセージ
     * @link http://objectclub.jp/community/memorial/homepage3.nifty.com/masarl/article/junit/scenario-based-testcase.html#verify%20%E3%83%A1%E3%82%BD%E3%83%83%E3%83%89
     */
    public function verify($message = '')
    {
        $this->assertEquals($this->expected, $this->actual, $message);
    }

    /**
     * Member オブジェクトを生成して返す.
     *
     * @param string $username . null の場合は, ランダムなユーザーIDが生成される.
     * @return \Eccube\Entity\Member
     */
    public function createMember($username = null)
    {
        return $this->app['eccube.fixture.generator']->createMember($username);
    }

    /**
     * Customer オブジェクトを生成して返す.
     *
     * @param string $email メールアドレス. null の場合は, ランダムなメールアドレスが生成される.
     * @return \Eccube\Entity\Customer
     */
    public function createCustomer($email = null)
    {
        return $this->app['eccube.fixture.generator']->createCustomer($email);
    }

    /**
     * CustomerAddress を生成して返す.
     *
     * @param Customer $Customer 対象の Customer インスタンス
     * @param boolean $is_nonmember 非会員の場合 true
     * @return \Eccube\Entity\CustomerAddress
     */
    public function createCustomerAddress(Customer $Customer, $is_nonmember = false)
    {
        return $this->app['eccube.fixture.generator']->createCustomerAddress($Customer, $is_nonmember);
    }

    /**
     * 非会員の Customer オブジェクトを生成して返す.
     *
     * @param string $email メールアドレス. null の場合は, ランダムなメールアドレスが生成される.
     * @return \Eccube\Entity\Customer
     */
    public function createNonMember($email = null)
    {
        return $this->app['eccube.fixture.generator']->createNonMember($email);
    }

    /**
     * Product オブジェクトを生成して返す.
     *
     * @param string $product_name 商品名. null の場合はランダムな文字列が生成される.
     * @param integer $product_class_num 商品規格の生成数
     * @return \Eccube\Entity\Product
     */
    public function createProduct($product_name = null, $product_class_num = 3)
    {
        return $this->app['eccube.fixture.generator']->createProduct($product_name, $product_class_num);
    }

    /**
     * Order オブジェクトを生成して返す.
     *
     * @param \Eccube\Entity\Customer $Customer Customer インスタンス
     * @return \Eccube\Entity\Order
     */
    public function createOrder(Customer $Customer)
    {
        $Product = $this->createProduct();
        $ProductClasses = $Product->getProductClasses();

        // 後方互換のため最初の1つのみ渡す
        return $this->app['eccube.fixture.generator']->createOrder($Customer, array($ProductClasses[0]));
    }

    /**
     * Payment オプジェクトを生成して返す.
     *
     * @param \Eccube\Entity\Delivery $Delivery デフォルトで設定する配送オブジェクト
     * @param string $method 支払い方法名称
     * @param integer $charge 手数料
     * @param integer $rule_min 下限金額
     * @param integer $rule_max 上限金額
     * @return \Eccube\Entity\Payment
     */
    public function createPayment(\Eccube\Entity\Delivery $Delivery, $method, $charge = 0, $rule_min = 0, $rule_max = 999999999)
    {
        return $this->app['eccube.fixture.generator']->createPayment($Delivery, $method, $charge, $rule_min, $rule_max);
    }

    /**
     * PageLayout オブジェクトを生成して返す
     *
     * @return \Eccube\Entity\PageLayout
     */
    public function createPageLayout()
    {
        return $this->app['eccube.fixture.generator']->createPageLayout();
    }

    /**
     * テーブルのデータを全て削除する.
     *
     * このメソッドは、参照制約の関係で、 Doctrine ORM ではデータ削除できない場合に使用する.
     * 通常は、 EntityManager::remove() を使用して削除すること.
     *
     * @param array $tables 削除対象のテーブル名の配列
     */
    public function deleteAllRows(array $tables)
    {
        $pdo = $this->app['orm.em']->getConnection()->getWrappedConnection();
        foreach ($tables as $table) {
            $sql = 'DELETE FROM '.$table;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = Application::getInstance();
        $app['debug'] = true;

        // ログの内容をERRORレベルでしか出力しないように設定を上書き
        $app['config'] = $app->share($app->extend('config', function ($config, \Silex\Application $app) {
            $config['log']['log_level'] = 'ERROR';
            $config['log']['action_level'] = 'ERROR';
            $config['log']['passthru_level'] = 'ERROR';

            $channel = $config['log']['channel'];
            foreach (array('monolog', 'front', 'admin') as $key) {
                $channel[$key]['log_level'] = 'ERROR';
                $channel[$key]['action_level'] = 'ERROR';
                $channel[$key]['passthru_level'] = 'ERROR';
            }
            $config['log']['channel'] = $channel;

            return $config;
        }));
        $app->initLogger();

        $app->initialize();
        $app->initializePlugin();
        $app['session.test'] = true;
        $app['exception_handler']->disable();

        $app['form.csrf_provider'] = $app->share(function () {
            return new CsrfTokenMock();
        });
        $app->register(new \Eccube\Tests\ServiceProvider\FixtureServiceProvider());
        $app->boot();

        return $app;
    }

    /**
     * PHPUnit_* インスタンスのプロパティを初期化する.
     *
     * このメソッドは、PHPUnit のメモリリーク解消のため、 tearDown() メソッドでコールされる.
     *
     * @link http://stackoverflow.com/questions/13537545/clear-memory-being-used-by-php
     */
    protected function cleanUpProperties()
    {
        $refl = new \ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }
        \Eccube\Application::clearInstance();
    }

    /**
     * MailCatcher を初期化する.
     *
     * このメソッドは主に setUp() メソッドでコールされる.
     * MailCatcher が起動してない場合は, テストをスキップする.
     * MailCatcher については \Eccube\Tests\Service\MailServiceTest のコメントを参照してください
     *
     * @see \Eccube\Tests\Service\MailServiceTest
     * @link http://mailcatcher.me/
     */
    protected function initializeMailCatcher()
    {
        $this->checkMailCatcherStatus();
        $config = $this->app['config'];
        $config['mail']['transport'] = 'smtp';
        $config['mail']['host'] = '127.0.0.1';
        $config['mail']['port'] = 1025;
        $config['mail']['username'] = null;
        $config['mail']['password'] = null;
        $config['mail']['encryption'] = null;
        $config['mail']['auth_mode'] = null;
        $this->app['config'] = $config;
        $this->app['swiftmailer.use_spool'] = false;
        $this->app['swiftmailer.options'] = $this->app['config']['mail'];
    }

    /**
     * MailCatcher の起動状態をチェックする.
     *
     * MailCatcher が起動していない場合は, テストをスキップする.
     */
    protected function checkMailCatcherStatus()
    {
        try {
            $client = new Client();
            $request = $client->get(self::MAILCATCHER_URL.'messages');
            $response = $request->send();
            if ($response->getStatusCode() !== 200) {
                throw new HttpException($response->getStatusCode());
            }
        } catch (HttpException $e) {
            $this->markTestSkipped($e->getMailCatcherMessage().'['.$e->getStatusCode().']');
        } catch (\Exception $e) {
            $message = 'MailCatcher is not available';
            $this->markTestSkipped($message);
            $this->app->log($message);
        }
    }

    /**
     * MailCatcher のメッセージをすべて削除する.
     */
    protected function cleanUpMailCatcherMessages()
    {
        try {
            $client = new Client();
            $request = $client->delete(self::MAILCATCHER_URL.'messages');
            $request->send();
        } catch (\Exception $e) {
            $this->app->log('['.get_class().'] '.$e->getMessage());
        }
    }

    /**
     * MailCatcher のメッセージをすべて取得する.
     *
     * @return array MailCatcher のメッセージの配列
     */
    protected function getMailCatcherMessages()
    {
        $client = new Client();
        $request = $client->get(self::MAILCATCHER_URL.'messages');
        $response = $request->send();

        return json_decode($response->getBody(true));
    }

    /**
     * MailCatcher のメッセージを ID を指定して取得する.
     *
     * @param integer $id メッセージの ID
     * @return object MailCatcher のメッセージ
     */
    protected function getMailCatcherMessage($id)
    {
        $client = new Client();
        $request = $client->get(self::MAILCATCHER_URL.'messages/'.$id.'.json');
        $response = $request->send();

        return json_decode($response->getBody(true));
    }

    /**
     * MailCatcher のメッセージソースをデコードする.
     *
     * @param object $Message MailCatcher のメッセージ
     * @return string デコードされた eml 形式のソース
     */
    protected function parseMailCatcherSource($Message)
    {
        return quoted_printable_decode($Message->source);
    }

    /**
     * in the case of sqlite in-memory database.
     */
    protected function isSqliteInMemory()
    {
        if (array_key_exists('memory', $this->app['config']['database'])
            && $this->app['config']['database']['memory']
        ) {
            return true;
        }

        return false;
    }
}
