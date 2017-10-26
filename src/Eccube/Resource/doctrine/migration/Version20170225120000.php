<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Eccube\Entity\PageLayout;

class Version20170225120000 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $app = \Eccube\Application::getInstance();
        /** @var EntityManager $em */
        $em = $app["orm.em"];

        $DeviceType = $app['eccube.repository.master.device_type']->find(10);

        $PageLayout = new PageLayout();
        $PageLayout
            ->setDeviceType($DeviceType)
            ->setName('商品購入/確認')
            ->setUrl('shopping_confirm')
            ->setFileName('Shopping/confirm')
            ->setEditFlg(2)
            ->setMetaRobots('noindex');
        $em->persist($PageLayout);

        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
