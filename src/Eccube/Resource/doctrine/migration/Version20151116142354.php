<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Eccube\Entity\PageLayout;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151116142354 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        // pageを追加
        $app = \Eccube\Application::getInstance();
        $em = $app["orm.em"];

        $DeviceType = $app['eccube.repository.master.device_type']->find(10);

        $PageLayout = new PageLayout();
        $PageLayout->setDeviceType($DeviceType);
        $PageLayout->setName( '商品購入/配送方法選択');
        $PageLayout->setUrl('shopping_delivery');
        $PageLayout->setFileName('Shopping/index');
        $PageLayout->setEditFlg(2);
        $PageLayout->setMetaRobots('noindex');
        $em->persist($PageLayout);

        $PageLayout = new PageLayout();
        $PageLayout->setDeviceType($DeviceType);
        $PageLayout->setName( '商品購入/支払方法選択');
        $PageLayout->setUrl('shopping_payment');
        $PageLayout->setFileName('Shopping/index');
        $PageLayout->setEditFlg(2);
        $PageLayout->setMetaRobots('noindex');
        $em->persist($PageLayout);

        $PageLayout = new PageLayout();
        $PageLayout->setDeviceType($DeviceType);
        $PageLayout->setName( '商品購入/お届け先変更');
        $PageLayout->setUrl('shopping_shipping_change');
        $PageLayout->setFileName('Shopping/index');
        $PageLayout->setEditFlg(2);
        $PageLayout->setMetaRobots('noindex');
        $em->persist($PageLayout);

        $PageLayout = new PageLayout();
        $PageLayout->setDeviceType($DeviceType);
        $PageLayout->setName( '商品購入/お届け先変更');
        $PageLayout->setUrl('shopping_shipping_edit_change');
        $PageLayout->setFileName('Shopping/index');
        $PageLayout->setEditFlg(2);
        $PageLayout->setMetaRobots('noindex');
        $em->persist($PageLayout);

        $PageLayout = new PageLayout();
        $PageLayout->setDeviceType($DeviceType);
        $PageLayout->setName( '商品購入/お届け先の複数指定');
        $PageLayout->setUrl('shopping_shipping_multiple_change');
        $PageLayout->setFileName('Shopping/index');
        $PageLayout->setEditFlg(2);
        $PageLayout->setMetaRobots('noindex');
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
