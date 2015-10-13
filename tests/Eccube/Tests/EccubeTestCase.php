<?php

namespace Eccube\Tests;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Migrations\MigrationException;
use Eccube\Application;
use Silex\WebTestCase;
use Faker\Factory as Faker;

/**
 * Abstract class that other unit tests can extend, provides generic methods for EC-CUBE tests.
 *
 * @author Kentaro Ohkouchi
 */
abstract class EccubeTestCase extends WebTestCase
{

    protected $actual;
    protected $expected;

    /**
     * Applicaiton を生成しトランザクションを開始する.
     */
    public function setUp()
    {
        parent::setUp();
        // in the case of sqlite in-memory database only.
        if (array_key_exists('memory', $this->app['config']['database'])
            && $this->app['config']['database']['memory']) {
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
        $this->app['orm.em']->getConnection()->rollback();
        $this->app['orm.em']->getConnection()->close();
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
        $entityManager->clear();

        // Schema Tool to process our entities
        $tool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $classes = $entityManager->getMetaDataFactory()->getAllMetaData();

        // Drop all classes and re-build them for each test case
        $tool->dropSchema($classes);
        $tool->createSchema($classes);
        $config = new Configuration($this->app['db']);
        $config->setMigrationsNamespace('DoctrineMigrations');

        $migrationDir = __DIR__ . '/../../../src/Eccube/Resource/doctrine/migration';
        $config->setMigrationsDirectory($migrationDir);
        $config->registerMigrationsFromDirectory($migrationDir);

        $migration = new Migration($config);
        $migration->migrate(null, false);

        // 通常は eccube_install.sh で追加されるデータを追加する
        $sql = "INSERT INTO dtb_member (member_id, login_id, password, salt, work, del_flg, authority, creator_id, rank, update_date, create_date,name,department) VALUES (2, 'admin', 'test', 'test', 1, 0, 0, 1, 1, current_timestamp, current_timestamp,'管理者','EC-CUBE SHOP')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = "INSERT INTO dtb_base_info (id, shop_name, email01, email02, email03, email04, update_date, option_product_tax_rule) VALUES (1, 'SHOP_NAME', 'admin@example.com', 'admin@example.com', 'admin@example.com', 'admin@example.com', current_timestamp, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
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
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = new Application();
        $app['debug'] = true;
        $app->initialize();
        $app->initPluginEventDispatcher();
        $app['session.test'] = true;
        $app['exception_handler']->disable();

        $app->boot();

        return $app;
    }
}
