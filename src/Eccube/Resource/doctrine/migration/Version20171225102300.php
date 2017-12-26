<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Eccube\Application;
use Symfony\Component\Yaml\Yaml;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171225102300 extends AbstractMigration
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

        // Update config
        if (!array_key_exists('x_frame_options', $config)) {
            $appendConfig['x_frame_options'] = false;
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
