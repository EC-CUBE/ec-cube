<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160908161616 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // DeliveryDate のお届け日数を修正
        // see https://github.com/EC-CUBE/ec-cube/issues/1732
        $app = \Eccube\Application::getInstance();
        $em = $app["orm.em"];
        $DeliveryDate = $app['eccube.repository.delivery_date']->find(9);
        if ($DeliveryDate->getValue() === 0) {
            $DeliveryDate->setValue(-1);
            $em->flush($DeliveryDate);
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $app = \Eccube\Application::getInstance();
        $em = $app["orm.em"];
        $DeliveryDate = $app['eccube.repository.delivery_date']->find(9);
        if ($DeliveryDate->getValue() === -1) {
            $DeliveryDate->setValue(0);
            $em->flush($DeliveryDate);
        }
    }
}
