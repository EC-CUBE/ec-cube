<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Eccube\Entity\PageLayout;
/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160725110400 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $app = \Eccube\Application::getInstance();
        $em = $app["orm.em"];

        $DeviceType = $em->getRepository('\Eccube\Entity\Master\DeviceType')->find(\Eccube\Entity\Master\DeviceType::DEVICE_TYPE_PC);
        $PageLayout = new PageLayout();
        $PageLayout->setDeviceType($DeviceType);
        $PageLayout->setName('404エラーページ');
        $PageLayout->setUrl('404');
        $PageLayout->setFileName('404');
        $PageLayout->setEditFlg(2);
        $PageLayout->setMetaRobots('noindex');
        $PageLayout->setCreateDate(new \DateTime());
        $PageLayout->setUpdateDate(new \DateTime());

        $em->persist($PageLayout);
        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
