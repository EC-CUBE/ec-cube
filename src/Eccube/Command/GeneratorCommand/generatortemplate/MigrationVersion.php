<?php

/*
 * This file is part of the [code]
 *
 * Copyright (C) [year] [author]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Application;

class Version[datetime] extends AbstractMigration
{
    protected $entities = array(
[entityList]
    );

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $app = Application::getInstance();
        $meta = $this->getMetadata($app['orm.em']);
        $tool = new SchemaTool($app['orm.em']);
        $tool->createSchema($meta);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

        $app = Application::getInstance();
        $meta = $this->getMetadata($app['orm.em']);

        $tool = new SchemaTool($app['orm.em']);
        $schemaFromMetadata = $tool->getSchemaFromMetadata($meta);

        // テーブル削除
        foreach ($schemaFromMetadata->getTables() as $table) {
            if ($schema->hasTable($table->getName())) {
                $schema->dropTable($table->getName());
            }
        }

        // シーケンス削除
        foreach ($schemaFromMetadata->getSequences() as $sequence) {
            if ($schema->hasSequence($sequence->getName())) {
                $schema->dropSequence($sequence->getName());
            }
        }
    }

    /**
     * @param EntityManager $em
     * @return array
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    protected function getMetadata(EntityManager $em)
    {
        $meta = array();
        foreach ($this->entities as $entity) {
            $meta[] = $em->getMetadataFactory()->getMetadataFor($entity);
        }

        return $meta;
    }
}
