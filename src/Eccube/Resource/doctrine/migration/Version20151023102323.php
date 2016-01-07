<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Eccube\Entity\PageLayout;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151023102323 extends AbstractMigration
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

        $DeviceType = $em->getRepository('\Eccube\Entity\Master\DeviceType')->find(10);

        $PageLayout = new PageLayout();
        $PageLayout->setDeviceType($DeviceType);
        $PageLayout->setName( '商品購入/お届け先の追加');
        $PageLayout->setUrl('shopping_shipping_edit');
        $PageLayout->setFileName('Shopping/shipping_edit');
        $PageLayout->setEditFlg(2);
        $PageLayout->setMetaRobots('noindex');
        $PageLayout->setCreateDate(new \DateTime());
        $PageLayout->setUpdateDate(new \DateTime());
        $em->persist($PageLayout);

        $PageLayout = new PageLayout();
        $PageLayout->setDeviceType($DeviceType);
        $PageLayout->setName( '商品購入/お届け先の複数指定(お届け先の追加)');
        $PageLayout->setUrl('shopping_shipping_multiple_edit');
        $PageLayout->setFileName('Shopping/shipping_multiple_edit');
        $PageLayout->setEditFlg(2);
        $PageLayout->setMetaRobots('noindex');
        $PageLayout->setCreateDate(new \DateTime());
        $PageLayout->setUpdateDate(new \DateTime());
        $em->persist($PageLayout);

        $PageLayout = new PageLayout();
        $PageLayout->setDeviceType($DeviceType);
        $PageLayout->setName( '商品購入/購入エラー');
        $PageLayout->setUrl('shopping_error');
        $PageLayout->setFileName('Shopping/shopping_error');
        $PageLayout->setEditFlg(2);
        $PageLayout->setMetaRobots('noindex');
        $PageLayout->setCreateDate(new \DateTime());
        $PageLayout->setUpdateDate(new \DateTime());
        $em->persist($PageLayout);

        $PageLayout = new PageLayout();
        $PageLayout->setDeviceType($DeviceType);
        $PageLayout->setName( 'ご利用ガイド');
        $PageLayout->setUrl('help_guide');
        $PageLayout->setFileName('Help/guide');
        $PageLayout->setEditFlg(2);
        $PageLayout->setCreateDate(new \DateTime());
        $PageLayout->setUpdateDate(new \DateTime());
        $em->persist($PageLayout);

        $PageLayout = new PageLayout();
        $PageLayout->setDeviceType($DeviceType);
        $PageLayout->setName( 'パスワード再発行(入力ページ)');
        $PageLayout->setUrl('forgot');
        $PageLayout->setFileName('Forgot/index');
        $PageLayout->setEditFlg(2);
        $PageLayout->setCreateDate(new \DateTime());
        $PageLayout->setUpdateDate(new \DateTime());
        $em->persist($PageLayout);

        $PageLayout = new PageLayout();
        $PageLayout->setDeviceType($DeviceType);
        $PageLayout->setName( 'パスワード再発行(完了ページ)');
        $PageLayout->setUrl('forgot_complete');
        $PageLayout->setFileName('Forgot/complete');
        $PageLayout->setEditFlg(2);
        $PageLayout->setMetaRobots('noindex');
        $PageLayout->setCreateDate(new \DateTime());
        $PageLayout->setUpdateDate(new \DateTime());
        $em->persist($PageLayout);

        $PageLayout = new PageLayout();
        $PageLayout->setDeviceType($DeviceType);
        $PageLayout->setName( 'パスワード変更((完了ページ)');
        $PageLayout->setUrl('forgot_reset');
        $PageLayout->setFileName('Forgot/reset');
        $PageLayout->setEditFlg(2);
        $PageLayout->setMetaRobots('noindex');
        $PageLayout->setCreateDate(new \DateTime());
        $PageLayout->setUpdateDate(new \DateTime());
        $em->persist($PageLayout);

        $em->flush();

        // 文言、URLの修正
        $this->addSql("UPDATE dtb_page_layout  SET url = 'help_agreement', file_name = 'Help/agreement' WHERE file_name = 'Entry/kiyaku';");
        $this->addSql("UPDATE dtb_page_layout  SET url = 'shopping_shipping_multiple', file_name = 'Shopping/shipping_multiple' WHERE file_name = 'Shopping/multiple';");
        $this->addSql("UPDATE dtb_page_layout  SET page_name = '商品購入' WHERE page_name = '商品購入/ログイン';");
        $this->addSql("UPDATE dtb_page_layout  SET page_name = 'MYページ/お届け先一覧' WHERE page_name = 'MYページ/お届け先変更';");
        $this->addSql("UPDATE dtb_page_layout  SET file_name = 'Mypage/delivery_edit' WHERE page_name = 'MYページ/お届け先追加';");

        // 不要なレコードを削除
        $this->addSql("DELETE from dtb_page_layout  WHERE file_name = 'Shopping/payment';");
        $this->addSql("DELETE from dtb_page_layout  WHERE file_name = 'Shopping/confirm';");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
