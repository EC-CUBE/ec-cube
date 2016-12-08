<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Eccube\Entity\PageLayout;

class Version20160823172700 extends AbstractMigration
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
            ->setName('MYページ/お届け先編集')
            ->setUrl('mypage_delivery_edit')
            ->setFileName('Mypage/delivery_edit')
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
