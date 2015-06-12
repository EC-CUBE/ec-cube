<?php


namespace Plugin\SampleEntity;
use Eccube\Plugin\AbstractPluginManager;
use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Migrations\Configuration\Configuration;

class PluginManager extends AbstractPluginManager {

    public function install($plugin,$app)
    {
        echo "<hr>";
        echo "installed". date('r');
        echo "<hr>";

        $config = new Configuration($app['db']);
        $config->setMigrationsNamespace('DoctrineMigrations');
        $migrationDir= __DIR__ . '/migration' ;
        $config->setMigrationsDirectory($migrationDir);
        $config->registerMigrationsFromDirectory($migrationDir );
        $config->setMigrationsTableName("tomitadb_migration");
        $migration = new Migration($config);
                                  // nullを渡すと最新バージョンまでマイグレートする
        $migration->migrate(null, false); 

        

    }

    public function uninstall($config,$app){}

    public function enable($config,$app){}

    public function disable($config,$app){}

    public function update($config,$app){}
}
