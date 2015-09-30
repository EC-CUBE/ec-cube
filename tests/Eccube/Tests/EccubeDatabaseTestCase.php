<?php

namespace Eccube\Tests;

use Eccube\Application;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Migrations\MigrationException;


abstract class EccubeDatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{

    protected static $app;
    /**
     * @see http://jamesmcfadden.co.uk/database-unit-testing-with-doctrine-2-and-phpunit/
     */
    public function getConnection()
    {
        // 別途 Application を生成しているような箇所があると動作しないので注意
        $app = EccubeTestCase::createApplication();

        // Get an instance of your entity manager
        $entityManager = $app['orm.em'];

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
        $config = new Configuration($app['db']);
        $config->setMigrationsNamespace('DoctrineMigrations');

        $migrationDir = __DIR__ . '/../../../src/Eccube/Resource/doctrine/migration';
        $config->setMigrationsDirectory($migrationDir);
        $config->registerMigrationsFromDirectory($migrationDir);

        $migration = new Migration($config);
        $migration->migrate(null, false);
        self::$app = $app;
        // Pass to PHPUnit
        return $this->createDefaultDBConnection($pdo, 'db_name');
    }

    public function getDataSet()
    {
        return new \PHPUnit_Extensions_Database_DataSet_DefaultDataSet();
    }
}
