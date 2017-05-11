<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160803143037 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if ($schema->hasTable('dtb_plugin_option')) {
            return;
        }
        $app = \Eccube\Application::getInstance();
        $meta = $app['orm.em']->getMetadataFactory()->getMetadataFor('Eccube\Entity\PluginOption');
        $tool = new SchemaTool($app['orm.em']);
        $tool->createSchema(array($meta));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
