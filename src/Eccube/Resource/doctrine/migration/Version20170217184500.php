<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Eccube\Application;
use Symfony\Component\Yaml\Yaml;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170217184500 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // config.ymlの更新
        $app = Application::getInstance();
        $file = $app['config']['root_dir'].'/app/config/eccube/config.yml';
        $config = Yaml::parse(file_get_contents($file));

        $appendConfig = array();

        // キーが未定義の場合は初期値を設定する
        if (!array_key_exists('trusted_proxies_connection_only', $config)) {
            $appendConfig['trusted_proxies_connection_only'] = false;
        }
        if (!array_key_exists('trusted_proxies', $config)) {
            $appendConfig['trusted_proxies'] = array();
        }

        if (count($appendConfig)) {
            file_put_contents($file, "\n", FILE_APPEND);
            file_put_contents($file, Yaml::dump($appendConfig), FILE_APPEND);
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // do nothing
    }
}
