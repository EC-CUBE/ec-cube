<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Eccube\Application;
use Symfony\Component\Yaml\Yaml;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160413151321 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        // path.ymlの更新
        $app = Application::getInstance();
        $file = $app['config']['root_dir'] . '/app/config/eccube/path.yml';
        $config = Yaml::parse(file_get_contents($file));

        if (!array_key_exists('public_path', $config)) {
            // public_pathが未定義なら作成

            $config['public_path'] = '/html';
            $config['public_path_realdir'] = $config['root_dir'].$config['public_path'];
            $config['plugin_html_realdir'] = $config['root_dir'].$config['public_path'].'/plugin';
            $config['plugin_urlpath'] = $config['root_urlpath'].'/plugin';

            file_put_contents($file, Yaml::dump($config));

        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}